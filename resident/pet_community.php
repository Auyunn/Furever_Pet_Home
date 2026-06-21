<?php
session_start();

include '../db_connect.php';

//Submit Comment use POST
if(isset($_POST['submit_comment'])){
    if(!isset($_SESSION['ResidentID'])){
        header("Location: login.php");
        exit();
    }
    //filtered user- provide data before system process
    $board_id =mysqli_real_escape_string($conn, $_POST['board_id']);
    $content = mysqli_real_escape_string($conn, trim($_POST['content']));
    $resident_id =$_SESSION['ResidentID'];
    $date = date('Y-m-d H:i:s');

    //Avoid duplicate commentID  from  deleted rows
    $result_max = mysqli_query($conn,"SELECT MAX(CAST(SUBSTRING(CommentID,4)AS UNSIGNED)) AS maxNum FROM comment");
    $row_max = mysqli_fetch_array($result_max);
    $next_num= ($row_max['maxNum']!==NULL)?$row_max['maxNum']+ 1:1;
    $comment_id="COM".str_pad($next_num,2,"0", STR_PAD_LEFT);

    //insert new comment into table comment
    $query_insert = "INSERT INTO comment (CommentID, ResidentID, BoardID, Content, Date, ReplyID)
                    VALUES('$comment_id','$resident_id','$board_id','$content','$date', NULL)";
    //if fail, prompt message error from MYSQL 
    if(mysqli_query($conn, $query_insert)){
        header("Location:pet_community.php");
        exit();
    }else{
        echo"Update has error: ". mysqli_error($conn);
    }

}

//status user login
$is_logged_in = isset($_SESSION['ResidentID']);
//take pet community data from table 
$query_board= "SELECT pc.*, o.OrgName 
                FROM community_board pc
                LEFT JOIN organization o ON pc.OrgID= o.OrgID
                ORDER BY pc.BoardID ASC";
$result_board = mysqli_query($conn, $query_board);
?>

<!DOCTYPE html>
<html lang ="en">
<head>
    <meta charset="UTF-8">
    <title> Pet Community</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/petcommunityrs.css">
</head>
<body>
    <!-- Navigation -->
<nav class="navbar" id="navbar">
        <!--logo and profile-->
    <div class ="navbar-top">
        <a href="#" class="nav-logo">
            <img src="../image/icons/logo.png" alt="Furever Pet Home">
            <span>Furever Pet Home</span>
        </a>
        <div class="nav-right">
            <?php if($is_logged_in): ?>
                <button class="notif-btn" title="Notifications" onclick="window.location.href='resident/inbox.php';">🔔<span class="notif-dot"></span></button>
                <div class="avatar" title="My Profile" >AT</div>
            <?php else: ?>
                <button class="login-btn" onclick="window.location.href= 'login.php';">Login</button>
            <?php endif; ?>
        </div>
    </div>

    <!---Tab Navigation-->
    <div class="nav-links">
            <?php if($is_logged_in): ?>
                <a href="../HomePage(registed).php" class="nav-tab">Home</a>
            <?php else: ?>
                <a href="../HomePage_Unregistered.html" class="nav-tab">Home</a>
            <?php endif; ?>
            <a href="inbox.php" class="nav-tab">Inbox</a>
            <a href="../findapet.html" class="nav-tab"> Find A Pet</a>
            <a href="pet_community.php" class="nav-tab"> Pet Community</a>
            <a href="help_center.php" class="nav-tab"> Help Center</a>
            <a href="Analytics.php" class="nav-tab">Analytics</a>
            <a href="Report.php" class="nav-tab">Report</a>
        </div>
</nav>

    <!-- Structure of the content section-->
<div class= "wrapper">
    <h3 class="content-title"> Pet Community</h3>
        <!--- to calculate how many row-->
    <?php if($row=mysqli_num_rows($result_board)> 0):?>
        <?php while($post = mysqli_fetch_assoc($result_board)) : ?>
            <?php 
            $board_id=$post['BoardID'];
            //Use join to display the resident name 
            $query_comment = "SELECT c.CommentID, c.Content, c.Date,CONCAT(r.FirstName,' ', r.LastName) AS ResidentName
                                FROM comment c
                                LEFT JOIN resident r ON c.ResidentID = r.ResidentID
                                WHERE c.BoardID = '$board_id'
                                AND c.ReplyID IS NULL
                                ORDER BY c.Date ASC";
            $result_comment = mysqli_query($conn, $query_comment);
            $comment_count = mysqli_num_rows($result_comment);
            ?>
        
            <div class="box">
                <img src="../image/pet_community/<?php echo htmlspecialchars($post['Photo']);?>"
                alt="Pet Community Image" class="img">

                <div class="content">
                    <h4><?php echo htmlspecialchars($post['Title']);?></h4>
                    <p><?php echo htmlspecialchars($post['Content']);?></p>
                    <small style="color:#aaa">
                        📅<?php echo date('d M Y', strtotime($post['Date']));?>
                        &nbsp; |&nbsp; 🏢 <?php echo htmlspecialchars($post['OrgName']?? $post['OrgID']); ?>
                    </small>

                    <!-- comment panel -->
                    <div class="panel" id="panel-<?php echo $board_id;?>">
                        <div class="list">
                            <?php if($comment_count> 0): ?>
                                <?php while($comment = mysqli_fetch_assoc($result_comment)) : ?>
                                    <div class="comment-item">
                                        <div class ="author">
                                            <?php echo htmlspecialchars($comment['ResidentName']??'Unknown');?>
                                        </div>
                                        <div class="comment-text">
                                            <?php echo htmlspecialchars($comment['Content']);?>
                                        </div>
                                        <div class="comment-time">
                                            <?php echo date('d M Y, h:i A',strtotime($comment['Date']));?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p style="font-size:12px; color:#bbb; padding:6px 0;">No comment yet</p>
                            <?php endif; ?>
                        </div>

                        <?php if ($is_logged_in): ?>
                            <form action="pet_community.php" method ="POST" class="comment-input-row">
                                <input type="hidden" name="board_id" value="<?php echo $board_id;?>">
                                <input type="text" name="content" class="comment-input" placeholder="Write your comment"required>
                                <button type="submit" name="submit_comment" class="comment-send"> ➤ </button>
                            </form>
                        <?php else: ?>
                            <div class ="comment-input-row">
                                <input type="text" class="comment-input" placeholder="Please login to write a comment"style="background:#e9ecef; color:#6c757d; cursor:not-allowed;"
                                                disabled>
                                <button type="button" class="comment-send" style="background:#cccccc;cursor: not-allowed; " disable> ➤</button>
                            </div>
                        <?php endif; ?>
                    </div> 
                </div>
            <div class="comment" onclick="toggleComment('<?php echo $board_id; ?>')">💬 </div>
        </div>

        <?php endwhile; ?>
    <?php else:?>
        <div class ="box" style="justify-content: center;">
            <p>No found data.</p>
        </div>

    <?php endif; ?>
    </div>
    <script src="../js/script.js"></script>
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

</body>
</html>
