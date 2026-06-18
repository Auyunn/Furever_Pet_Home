<?php
session_start();
require_once "../db_connect.php"; // petcommunity.php is in /ngo, db_connect.php is one level up in project root

/* ============================================================
   CURRENT NGO (logged-in organization)
   ------------------------------------------------------------
   TODO: Once login is wired up, your login.php should set:
       $_SESSION['org_id'] = $row['OrgID'];
   on successful NGO login. This page just reads it from there.

   Until then, this fallback lets you test the page without
   being logged in. REMOVE the fallback block once auth exists.
============================================================ */
if (!isset($_SESSION['org_id'])) {
    $_SESSION['org_id'] = "ORG01"; // TEMP placeholder - remove after login is built
}
$currentOrgID = $_SESSION['org_id'];


/* ============================================================
   HANDLE POST ACTIONS (delete post, update post)
   These run before any HTML is output, then redirect back to
   this same page (prevents form re-submission on refresh).

   Both actions check OrgID = $currentOrgID in the WHERE clause
   so an NGO can never edit/delete another NGO's post, even if
   someone tampers with the hidden BoardID field in devtools.
============================================================ */

// ---- DELETE POST ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_post') {
    $boardID = $_POST['BoardID'];

    $stmt = $conn->prepare("DELETE FROM community_board WHERE BoardID = ? AND OrgID = ?");
    $stmt->bind_param("ss", $boardID, $currentOrgID);
    $stmt->execute();
    $stmt->close();

    header("Location: petcommunity.php");
    exit();
}

// ---- UPDATE POST (inline edit) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_post') {
    $boardID = $_POST['BoardID'];
    $title   = trim($_POST['Title']);
    $content = trim($_POST['Content']);

    $stmt = $conn->prepare("UPDATE community_board SET Title = ?, Content = ? WHERE BoardID = ? AND OrgID = ?");
    $stmt->bind_param("ssss", $title, $content, $boardID, $currentOrgID);
    $stmt->execute();
    $stmt->close();

    header("Location: petcommunity.php");
    exit();
}


/* ============================================================
   FETCH POSTS - only this NGO's own posts
   community_board columns: BoardID, OrgID, Title, Content, Photo, Date
============================================================ */
$posts = [];
$stmt = $conn->prepare("
    SELECT cb.BoardID, cb.Title, cb.Content, cb.Photo, cb.Date, cb.OrgID,
           o.OrgName
    FROM community_board cb
    LEFT JOIN organization o ON cb.OrgID = o.OrgID
    WHERE cb.OrgID = ?
    ORDER BY cb.Date DESC
");
$stmt->bind_param("s", $currentOrgID);
$stmt->execute();
$postResult = $stmt->get_result();
while ($row = $postResult->fetch_assoc()) {
    $posts[] = $row;
}
$stmt->close();


/* ============================================================
   FETCH COMMENTS for those posts (read-only display)
   comment columns: CommentID, BoardID, ResidentID, Content, Date, ReplyID
   Residents add these from a different page - this page only displays them.
============================================================ */
$commentsByBoard = [];
if (!empty($posts)) {
    $boardIDs = array_column($posts, 'BoardID');
    $placeholders = implode(',', array_fill(0, count($boardIDs), '?'));
    $types = str_repeat('s', count($boardIDs));

    $sql = "
        SELECT c.CommentID, c.BoardID, c.ResidentID, c.Content, c.Date, c.ReplyID,
               CONCAT(r.FirstName, ' ', r.LastName) AS CommenterName
        FROM comment c
        LEFT JOIN resident r ON c.ResidentID = r.ResidentID
        WHERE c.BoardID IN ($placeholders)
        ORDER BY c.Date ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$boardIDs);
    $stmt->execute();
    $commentResult = $stmt->get_result();
    while ($row = $commentResult->fetch_assoc()) {
        $commentsByBoard[$row['BoardID']][] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pet Community | Furever Pet Home</title>
<link rel="stylesheet" href="../css/petcommunityngo.css">
</head>
<body>

<!-- NOTE: Replace this with your actual shared navbar include, e.g. <?php include 'navbar.php'; ?> -->

<div class="community-grid">

    <?php foreach ($posts as $post): ?>
        <?php
            $boardID  = $post['BoardID'];
            $comments = $commentsByBoard[$boardID] ?? [];
        ?>

        <div class="community-card" id="board-<?php echo htmlspecialchars($boardID); ?>">

            <div class="community-image">
                <?php if (!empty($post['Photo'])): ?>
                    <img src="<?php echo htmlspecialchars($post['Photo']); ?>" alt="<?php echo htmlspecialchars($post['Title']); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">
                <?php endif; ?>
            </div>

            <div class="community-content">

                <!-- ===== VIEW MODE ===== -->
                <div class="view-mode" data-board="<?php echo htmlspecialchars($boardID); ?>">
                    <h3 class="post-title"><?php echo htmlspecialchars($post['Title']); ?></h3>
                    <p class="post-text"><?php echo htmlspecialchars($post['Content']); ?></p>
                    <span class="post-meta">
                        Posted by <?php echo htmlspecialchars($post['OrgName'] ?? $post['OrgID']); ?>
                        on <?php echo htmlspecialchars($post['Date']); ?>
                    </span>
                </div>

                <!-- ===== EDIT MODE (hidden until pencil icon clicked) ===== -->
                <form class="edit-mode" data-board="<?php echo htmlspecialchars($boardID); ?>" method="POST" action="petcommunity.php" style="display:none;">
                    <input type="hidden" name="action" value="update_post">
                    <input type="hidden" name="BoardID" value="<?php echo htmlspecialchars($boardID); ?>">
                    <input type="text" name="Title" class="edit-title-input" value="<?php echo htmlspecialchars($post['Title']); ?>" required>
                    <textarea name="Content" class="edit-content-input" required><?php echo htmlspecialchars($post['Content']); ?></textarea>
                    <div class="edit-actions">
                        <button type="submit" class="save-btn">Save</button>
                        <button type="button" class="cancel-btn" onclick="toggleEdit('<?php echo htmlspecialchars($boardID); ?>')">Cancel</button>
                    </div>
                </form>

                <!-- ===== COMMENT PANEL (read-only - residents comment elsewhere) ===== -->
                <div class="panel" id="panel-<?php echo htmlspecialchars($boardID); ?>">
                    <div class="comment-list">
                        <?php if (empty($comments)): ?>
                            <p class="no-comments">No comments yet.</p>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment-item">
                                    <strong><?php echo htmlspecialchars($comment['CommenterName'] ?? $comment['ResidentID']); ?>:</strong>
                                    <span><?php echo htmlspecialchars($comment['Content']); ?></span>
                                    <span class="comment-date"><?php echo htmlspecialchars($comment['Date']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="community-actions">
                <button type="button" onclick="toggleComments('<?php echo htmlspecialchars($boardID); ?>')" title="View comments">&#128172;</button>
                <button type="button" onclick="toggleEdit('<?php echo htmlspecialchars($boardID); ?>')" title="Edit">&#9998;</button>
                <form method="POST" action="petcommunity.php" onsubmit="return confirm('Delete this post? This cannot be undone.');" style="display:inline;">
                    <input type="hidden" name="action" value="delete_post">
                    <input type="hidden" name="BoardID" value="<?php echo htmlspecialchars($boardID); ?>">
                    <button type="submit" title="Delete">&#128465;</button>
                </form>
            </div>

        </div>

    <?php endforeach; ?>

    <?php if (empty($posts)): ?>
        <p>You haven't posted anything yet. Click the + button to add your first post.</p>
    <?php endif; ?>

</div>

<a href="addboard.php" class="add-btn" title="Add new post">+</a>

<script>
// Toggle the comment panel open/closed for a given post
function toggleComments(boardID) {
    const panel = document.getElementById('panel-' + boardID);
    panel.classList.toggle('open');
}

// Toggle between view mode and inline edit mode for a given post
function toggleEdit(boardID) {
    const viewEl = document.querySelector('.view-mode[data-board="' + boardID + '"]');
    const editEl = document.querySelector('.edit-mode[data-board="' + boardID + '"]');

    if (editEl.style.display === 'none') {
        viewEl.style.display = 'none';
        editEl.style.display = 'block';
    } else {
        viewEl.style.display = 'block';
        editEl.style.display = 'none';
    }
}
</script>

</body>
</html>