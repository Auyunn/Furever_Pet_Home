<?php
session_start();
require_once "../db_connect.php"; // petcommunity.php is in /ngo, db_connect.php is one level up in project root


$currentOrgID = $_SESSION['orgID'] ?? null;
if (!$currentOrgID) {
    header("Location: ../login.php");
    exit();
}
// ---- DELETE COMMENT ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
    $commentID = $_POST['CommentID'];

    $stmt = $conn->prepare("DELETE FROM comment WHERE CommentID = ?");
    $stmt->bind_param("s", $commentID);
    $stmt->execute();
    $stmt->close();

    header("Location: petcommunity.php");
    exit();
}

// ---- DELETE POST ----
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


$commentsByBoard = [];
if (!empty($posts)) {
    $boardIDs = array_column($posts, 'BoardID');
    $placeholders = implode(',', array_fill(0, count($boardIDs), '?'));
    $types = str_repeat('s', count($boardIDs));

    $sql = "
    SELECT c.CommentID, c.BoardID, c.ResidentID, c.OrgID, c.Content, c.Date, c.ReplyID,
        COALESCE(CONCAT(r.FirstName, ' ', r.LastName), o.OrgName) AS CommenterName,
        COALESCE(CONCAT(rp.FirstName, ' ', rp.LastName), op.OrgName) AS ReplyToName
    FROM comment c
    LEFT JOIN resident r ON c.ResidentID = r.ResidentID
    LEFT JOIN organization o ON c.OrgID = o.OrgID
    LEFT JOIN comment parent ON c.ReplyID = parent.CommentID
    LEFT JOIN resident rp ON parent.ResidentID = rp.ResidentID
    LEFT JOIN organization op ON parent.OrgID = op.OrgID
    WHERE c.BoardID IN ($placeholders)
    ORDER BY COALESCE(c.ReplyID, c.CommentID), c.Date ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$boardIDs);
    $stmt->execute();
    $commentResult = $stmt->get_result();
    while ($row = $commentResult->fetch_assoc()) {
        $commentsByBoard[$row['BoardID']][] = $row;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ngo_reply') {
    $boardID  = $_POST['BoardID'];
    $replyID  = $_POST['ReplyID'];
    $content  = trim($_POST['Content']);
    $date     = date('Y-m-d H:i:s');

    // Generate new CommentID
    $result_max = $conn->query("SELECT MAX(CAST(SUBSTRING(CommentID,4) AS UNSIGNED)) AS maxNum FROM comment");
    $row_max    = $result_max->fetch_assoc();
    $next_num   = ($row_max['maxNum'] !== NULL) ? $row_max['maxNum'] + 1 : 1;
    $comment_id = "COM" . str_pad($next_num, 2, "0", STR_PAD_LEFT);


   $stmt = $conn->prepare("
    INSERT INTO comment (CommentID, ResidentID, OrgID, BoardID, Content, Date, ReplyID)
    VALUES (?, NULL, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssss", $comment_id, $currentOrgID, $boardID, $content, $date, $replyID);
        $stmt->execute();
    $stmt->close();

    header("Location: petcommunity.php");
    exit();
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
    
        <div class="profile-dropdown">
            <div class="avatar" title="My Profile" onclick="toggleProfileDropdown()" style="cursor:pointer;">
                <?= htmlspecialchars(strtoupper(substr($currentOrgID, 0, 2))) ?>
            </div>
            <div class="dropdown-menu" id="profileDropdown">
                <div class="dropdown-user-info">
                    <strong><?= htmlspecialchars($currentOrgID) ?></strong>
                    <span>NGO Account</span>
                </div>
                <form method="post" action="../logout.php" style="margin:0;">
                    <button type="submit" class="logout-btn">🔒 Log Out</button>
                </form>
            </div>
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
                                        <div class="comment-item <?= $comment['ReplyID'] ? 'comment-reply' : '' ?>"
                                            id="comment-<?= htmlspecialchars($comment['CommentID']) ?>">

                                            <!-- VIEW MODE -->
                                            <div class="comment-view" data-comment="<?= htmlspecialchars($comment['CommentID']) ?>">
                                                <?php if ($comment['ReplyID']): ?>
                                                    <span class="reply-badge">↳ replying to <?= htmlspecialchars($comment['ReplyToName'] ?? $comment['ReplyID']) ?></span>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?= htmlspecialchars($comment['CommenterName'] ?? $comment['ResidentID']) ?>:</strong>
                                                    <span><?= htmlspecialchars($comment['Content']) ?></span>
                                                </div>
                                                <div class="comment-footer">
                                                    <span class="comment-date"><?= date('d M Y, g:i A', strtotime($comment['Date'])) ?></span>
                                                    <div class="comment-actions">
                                                    <button type="button"
                                                        onclick="openNgoReply('<?= htmlspecialchars($boardID) ?>', '<?= htmlspecialchars($comment['CommentID']) ?>', '<?= htmlspecialchars($comment['CommenterName']) ?>')">
                                                        ↩ Reply
                                                    </button>
                                                    <form method="POST" action="petcommunity.php" onsubmit="return confirm('Delete this comment?');" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete_comment">
                                                        <input type="hidden" name="CommentID" value="<?= htmlspecialchars($comment['CommentID']) ?>">
                                                        <button type="submit" title="Delete">🗑️</button>
                                                    </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- EDIT MODE -->
                                            <form class="comment-edit" data-comment="<?= htmlspecialchars($comment['CommentID']) ?>"
                                                method="POST" action="petcommunity.php" style="display:none;">
                                                <input type="hidden" name="action" value="update_comment">
                                                <input type="hidden" name="CommentID" value="<?= htmlspecialchars($comment['CommentID']) ?>">
                                                <input type="text" name="Content" class="edit-title-input"
                                                    value="<?= htmlspecialchars($comment['Content']) ?>" required>
                                                <div class="edit-actions">
                                                    <button type="submit" class="save-btn">Save</button>
                                                    <button type="button" class="cancel-btn"
                                                            onclick="toggleCommentEdit('<?= htmlspecialchars($comment['CommentID']) ?>')">Cancel</button>
                                                </div>
                                            </form>

                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

            <!-- NGO REPLY FORM (one per board, hidden until Reply clicked) -->
                            <form class="ngo-reply-form" id="ngo-reply-form-<?= htmlspecialchars($boardID) ?>"
                                method="POST" action="petcommunity.php" style="display:none;">
                                <input type="hidden" name="action" value="ngo_reply">
                                <input type="hidden" name="BoardID" value="<?= htmlspecialchars($boardID) ?>">
                                <input type="hidden" name="ReplyID" id="ngo-reply-id-<?= htmlspecialchars($boardID) ?>" value="">
                                <div class="comment-input-row">
                                    <span class="reply-to-label" id="ngo-reply-label-<?= htmlspecialchars($boardID) ?>"></span>
                                    <input type="text" name="Content" class="comment-input"
                                        id="ngo-reply-input-<?= htmlspecialchars($boardID) ?>"
                                        placeholder="Write your reply..." required>
                                    <button type="button" class="cancel-reply-btn"
                                            onclick="cancelNgoReply('<?= htmlspecialchars($boardID) ?>')">✕</button>
                                    <button type="submit" class="comment-send">➤</button>
                                </div>
                            </form>
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

<script src="../js/script.js"></script>

</body>
</html>
