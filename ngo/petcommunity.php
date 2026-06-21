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
$currentOrgID = $_SESSION['orgID'] ?? null;
if (!$currentOrgID) {
    header("Location: ../login.php");
    exit();
}

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

// NOTE: adjust this if your community post photos live in a different folder
$photoFolder = "../image/pet_community/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pet Community | Furever Pet Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- petcommunity.php is inside /ngo, so styles need the ../ prefix -->
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/petcommunityngo.css?v=2"></head>
<body>

    <nav class="navbar" id="navbar">
    <!--logo and profile-->
    <div class="navbar-top">
        <a href="#" class="nav-logo">
        <img src="../image/icons/logo.png" alt="Furever Pet Home">
        <span>Furever Pet Home</span>
        </a>
        <div class="nav-right">
        <button class="notif-btn" title="Notifications" onclick="window.location.href='inbox.php';">🔔<span class="notif-dot"></span></button>
        <div class="avatar" title="My Profile" onclick="window.location.href='profile.php';">
            <?= htmlspecialchars(strtoupper(substr($currentOrgID, 0, 2))) ?>
        </div>
        </div>
    </div>

    <!---Tab Navigation-->
    <div class="nav-links">
            <a href="Pet_listing.php" class="nav-tab"> Home</a>
            <a href="inbox.php" class="nav-tab"> Inbox</a>
            <a href="findapet.php" class="nav-tab"> Find A Pet</a>
            <a href="petcommunity.php" class="nav-tab"> Pet Community</a>
            <a href="helpcenter_ngo.php" class="nav-tab"> Help Center</a>
            <a href="Analytics.php" class="nav-tab"> Analytics</a>
            <a href="report.php" class="nav-tab"> Report</a>
    </div>
    </nav>

    <!--wrapper pushes all page content below the fixed navbar-->
    <div class="wrapper">

    <div class="community-grid">

        <?php if (empty($posts)): ?>
            <p class="empty-state">You haven't posted anything yet. Click the + button to add your first post.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?php
                    $boardID  = $post['BoardID'];
                    $comments = $commentsByBoard[$boardID] ?? [];
                ?>

                <div class="community-card" id="board-<?= htmlspecialchars($boardID) ?>">

                    <div class="community-image">
                        <?php if (!empty($post['Photo'])): ?>
                            <img src="<?= $photoFolder . htmlspecialchars($post['Photo']) ?>" alt="<?= htmlspecialchars($post['Title']) ?>">
                        <?php endif; ?>
                    </div>

                    <div class="community-content">

                        <!-- ===== VIEW MODE ===== -->
                        <div class="view-mode" data-board="<?= htmlspecialchars($boardID) ?>">
                            <h3 class="post-title"><?= htmlspecialchars($post['Title']) ?></h3>
                            <p class="post-text"><?= htmlspecialchars($post['Content']) ?></p>
                            <span class="post-meta">
                                Posted by <?= htmlspecialchars($post['OrgName'] ?? $post['OrgID']) ?>
                                on <?= date('d M Y, g:i A', strtotime($post['Date'])) ?>
                            </span>
                        </div>

                        <!-- ===== EDIT MODE (hidden until pencil icon clicked) ===== -->
                        <form class="edit-mode" data-board="<?= htmlspecialchars($boardID) ?>" method="POST" action="petcommunity.php" style="display:none;">
                            <input type="hidden" name="action" value="update_post">
                            <input type="hidden" name="BoardID" value="<?= htmlspecialchars($boardID) ?>">
                            <input type="text" name="Title" class="edit-title-input" value="<?= htmlspecialchars($post['Title']) ?>" required>
                            <textarea name="Content" class="edit-content-input" required><?= htmlspecialchars($post['Content']) ?></textarea>
                            <div class="edit-actions">
                                <button type="submit" class="save-btn">Save</button>
                                <button type="button" class="cancel-btn" onclick="toggleEdit('<?= htmlspecialchars($boardID) ?>')">Cancel</button>
                            </div>
                        </form>

                        <!-- ===== COMMENT PANEL (read-only - residents comment elsewhere) ===== -->
                        <div class="panel" id="panel-<?= htmlspecialchars($boardID) ?>">
                            <div class="comment-list">
                                <?php if (empty($comments)): ?>
                                    <p class="no-comments">No comments yet.</p>
                                <?php else: ?>
                                    <?php foreach ($comments as $comment): ?>
                                        <div class="comment-item">
                                            <strong><?= htmlspecialchars($comment['CommenterName'] ?? $comment['ResidentID']) ?>:</strong>
                                            <span><?= htmlspecialchars($comment['Content']) ?></span>
                                            <span class="comment-date"><?= date('d M Y, g:i A', strtotime($comment['Date'])) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                    <div class="community-actions">
                        <button type="button" onclick="toggleComments('<?= htmlspecialchars($boardID) ?>')" title="View comments">💬</button>
                        <button type="button" onclick="toggleEdit('<?= htmlspecialchars($boardID) ?>')" title="Edit">✏️</button>
                        <form method="POST" action="petcommunity.php" onsubmit="return confirm('Delete this post? This cannot be undone.');" style="display:inline;">
                            <input type="hidden" name="action" value="delete_post">
                            <input type="hidden" name="BoardID" value="<?= htmlspecialchars($boardID) ?>">
                            <button type="submit" title="Delete">🗑️</button>
                        </form>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <a href="addboard.php" class="add-btn" title="Add new post">+</a>

    <!--Footer-->
    <footer>
        <div class="footer-grid">
        <div>
            <div style="font-size:2rem;">🐾</div>
            <div class="footer-brand-name">Furever Pet Home</div>
            <p class="footer-tagline">A compassionate digital hub for stray pet adoption and community care in Bandar Klang, Selangor.</p>
        </div>
        <div>
            <p class="footer-col-title">Platform</p>
            <ul class="footer-links-list">
            <li><a href="#">Find A Pet</a></li>
            <li><a href="#">Report Animal</a></li>
            <li><a href="#">Community Board</a></li>
            <li><a href="#">Analytics</a></li>
            </ul>
        </div>
        <div>
            <p class="footer-col-title">Account</p>
            <ul class="footer-links-list">
            <li><a href="#">My Profile</a></li>
            <li><a href="#">My Applications</a></li>
            <li><a href="#">Favourites</a></li>
            <li><a href="#">Inbox</a></li>
            </ul>
        </div>
        <div>
            <p class="footer-col-title">Contact</p>
            <ul class="footer-links-list">
            <li><a href="#">41700 Bandar Klang, Selangor</a></li>
            <li><a href="mailto:info@fureverpethome.com">info@fureverpethome.com</a></li>
            <li><a href="#">+60 123-456-7890</a></li>
            <li><a href="#">Facebook · Instagram · X</a></li>
            </ul>
        </div>
        </div>
        <div class="footer-bottom">
        <span>© 2026 Furever Pet Home — Urban Pet Adoption & Community Management</span>
        <span>Made with ❤️ for Bandar Klang</span>
        </div>
    </footer>

    </div><!--/wrapper-->

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
