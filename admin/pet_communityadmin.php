<?php
session_start();
include('../db_connect.php');
/*if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
*/
if($_SERVER['REQUEST_METHOD']=== 'POST'){
    header('Content-Type: application/json');
    $action = $_POST['action']?? '';

    if($action === 'detail'){
       $boardID = $_POST['boardID'] ?? '';
        if(!$boardID){
            echo json_encode(['error' => 'Board ID is empty']);
            exit();
        }

        $sql_board = "SELECT b.BoardID, b.Title, b.Content, b.Photo, b.Date, b.OrgID, o.OrgName
        FROM community_board b
        LEFT JOIN organization o ON b.OrgID = o.OrgID
        WHERE b.BoardID = '". $boardID . "'";
        $result_board = mysqli_query($conn, $sql_board);
        $post = mysqli_fetch_assoc($result_board);
        if(!$post){
            echo json_encode(['error' => 'Post not found']);
            exit();
        }

        // Fetch comments
        $sql_comments = "SELECT c.CommentID, c.Content, c.Date, c.ReplyID, c.ResidentID, r.FirstName, r.LastName,c2.Content AS ReplyContent
        FROM comment c
        LEFT JOIN resident r ON c.ResidentID = r.ResidentID
        LEFT JOIN comment c2 ON c.ReplyID = c2.CommentID
        WHERE c.BoardID = '". $boardID . "'
        ORDER BY c.Date ASC";
        $result_comments = mysqli_query($conn, $sql_comments);

        //Loop through comments and store in array
        $commentsData = [];
        while($row = mysqli_fetch_assoc($result_comments)){
            $commentsData[] = $row;
        }
        echo json_encode([
            'post' => $post,
            'comments' => $commentsData
        ]);
        exit;
    }

    // Handle delete action
    if($action === 'delete'){
        $boardID =$_POST['boardID'] ?? '';
        //Stop if empty
        if(!$boardID){
            echo json_encode(['error' => 'Board ID is empty']);
            exit();
        }

        //Delete all comments that related to the board
        $sql_delete_comments = "DELETE FROM comment WHERE BoardID = '". $boardID . "'";
        mysqli_query($conn, $sql_delete_comments);
        //Delete the board
        $sql_delete_board = "DELETE FROM community_board WHERE BoardID = '". $boardID. "'";
        mysqli_query($conn, $sql_delete_board);

        echo json_encode(['success' => true]);
        exit();
    }

    echo json_encode(['error' => 'Invalid action']);
    exit();
}

// Load all boards
$sql_posts = "SELECT b.BoardID, b.Title, b.Content, b.Photo, b.Date, b.OrgID, o.OrgName
FROM community_board b
LEFT JOIN organization o ON b.OrgID = o.OrgID
ORDER BY b.Date DESC";
$result_boards = $conn->query($sql_posts);
$boards = [];
while($row = $result_boards->fetch_assoc()){
    $boards[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title> Pet Community</title>
  
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/pet_communityadmin.css">
</head>
<body>
    <!-- Header -->
<nav class="navbar" id="navbar">
        <!--logo and profile-->
        <div class ="navbar-top">
            <a href="#" class="nav-logo">
            <img src="../image/icons/logo.png" alt="Furever Pet Home">
            <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
            <div class="avatar" title="My Profile">A</div>

            </div>
        </div>

        <!---Tab Navigation-->
        <div class="nav-links">
            <a href="dashboard.php" class="nav-tab"> Dashboard</a>
                <a href="usercount.php" class="nav-tab"> Users/NGOs</a>
                <a href="Add_Report.php" class="nav-tab"> Report</a>
                <a href="analytics_admin.php" class="nav-tab"> Analytics</a>
                <a href="pet_communityadmin.php" class="nav-tab"> Pet Community</a>
                <a href="help_center.php" class="nav-tab"> Help Center</a> 
        </div>
</nav>
    <div class = "page-header">
        <h1 class="page-title">Pet Community</h1>
    </div>
    <!-- Structure of the content section-->
    <div class= "wrapper">

        <?php if (empty($boards)): ?>

            <div class="empty-state">No posts found.</div>
         <?php else: ?>
            <?php foreach ($boards as $board): ?>
            <div class ="box" id="post-<?= htmlspecialchars($board['BoardID'])?>">
                <div class="img-wrapper">
                <?php if(!empty($board['Photo'])): ?>
                    <img src="../image/pet_community/<?= htmlspecialchars($board['Photo']) ?>" 
                    alt="<?= htmlspecialchars($board['Title']) ?>" class="img">
                <?php endif; ?>
                </div>

        
                <div class = "content">
                    <h4><?= htmlspecialchars($board['Title']) ?></h4>
                    <p class="post-preview">
                        <?= htmlspecialchars(mb_substr($board['Content'], 0, 100)) ?>...
                    </p>
                    
                    <div class="meta-info">
                        <span class="org-name"><?= htmlspecialchars($board['OrgName'] ?? $board['OrgID']) ?></span>
                        <span class="date">• <?= date('d M Y, g:i A', strtotime($board['Date'])) ?></span>
                    </div>
                </div>

                <div class="actions">
    
                <button class="btn-view" onclick="viewPost('<?= htmlspecialchars($board['BoardID']); ?>')" title="View Detail">
                    ✏️
                </button>
                
                <button class="btn-delete" onclick="confirmDelete('<?= htmlspecialchars($board['BoardID']); ?>')" title="Delete Post">
                    🗑️
                </button>

            </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<!------------- view detail--------------->
    <div class="modal-overlay" id="modal-overlay" onclick="closeModal()">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modal-title">Post Detail</h3>
                <span class="modal-close" onclick="closeModal()">✕</span>
            </div>
            <div class="modal-body" id="modal-body">
                <div class="modal-loading"></div>
            </div>
        </div>
    </div>

<!------------ display confrimation---->
    <div class="modal-overlay" id="confirm-overlay">
        <div class="confirm-box" onclick="event.stopPropagation()">
            <div class="confirm-icon">🗑️</div>
            <p>Are you sure you want to delete this post?
                <span class="confirmm-sub"> All data related to this post will be permanently deleted.</span>
            </p>
        
            <div class="confirm-buttons">
                <button class="confirm" onclick="doDelete()">Confirm</button>
                <button class="cancel" onclick="cancelDelete()">Cancel</button>
            </div>
        </div>
    </div>

    <script src="../js/pet_communityadmin.js"></script>
    <!--Footer-->
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
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="usercount.php">Users/NGOs</a></li>
                    <li><a href="Add_Report.php">Report</a></li>
                    <li><a href="analytics_admin.php">Analytics</a></li>
                    <li><a href="pet_communityadmin.php">Pet Community</a></li>
                    <li><a href="help_center.php">Help Center</a></li>
                </ul>
            </div>
            <div>
                  <p class="footer-col-title">Contact</p>
                  <ul class="footer-links-list">
                  <li>41700 Bandar Klang, Selangor</li>
                  <li>info@fureverpethome.com</li>
                  <li>+6012-456 7890</li>
                  <li>Facebook · Instagram · X</li>
                  </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2026 Furever Pet Home — Urban Pet Adoption & Community Management</span>
            <span>Made with ❤️ for Bandar Klang</span>
        </div>
    </footer>

</body>
</html>

