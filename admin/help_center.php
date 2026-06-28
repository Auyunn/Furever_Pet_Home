<?php
session_start();
include('../db_connect.php');

//fetching guideline
$guideline ="SELECT g.GuidelineID, g.Title, g.PetType, g.Description, g.Budget, o.OrgName
            FROM guidelines g
            LEFT JOIN organization o ON g.OrgID= o.OrgID
            ORDER BY g.GuidelineID ASC";

$result_guideline= mysqli_query($conn, $guideline);
$total_guideline = mysqli_num_rows($result_guideline);

//fetching faq
$faq = "SELECT f.FaqID, f.Question, f.Description, o.OrgName
        FROM faq f
        LEFT JOIN organization o
        ON f.OrgID = o.OrgID
        ORDER BY f.FaqID ASC";
$result_faq = mysqli_query($conn,$faq);
$total_faq = mysqli_num_rows($result_faq);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Help Center</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/help_centeradmin.css">
</head>
<body>

   <nav class="navbar" id="navbar">
        <div class ="navbar-top">
            <a href="#" class="nav-logo">
            <img src="../image/icons/logo.png" alt="Furever Pet Home">
            <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
                <div class="profile-dropdown">

                    <div class="avatar"
                        title="My Profile"
                        onclick="toggleProfileDropdown()"
                        style="cursor:pointer;">
                    A
                    </div>

                    <div class="dropdown-menu" id="profileDropdown">

                    <div class="dropdown-user-info">
                        <strong>
                        <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
                        </strong>
                        <span>Admin Account</span>
                    </div>

                    <form method="post" action="../logout.php" style="margin:0;">
                        <button type="submit" class="logout-btn">
                        &#128274; Log Out
                        </button>
                    </form>

                    </div>
                </div>
            </div>
        </div>

        <!navigation bar->
        <div class="nav-links">
            <a href="dashboard.php" class="nav-tab"> Dashboard</a>
                <a href="usercount.php" class="nav-tab"> Users/NGOs</a>
                <a href="Add_Report.php" class="nav-tab"> Report</a>
                <a href="analytics_admin.php" class="nav-tab"> Analytics</a>
                <a href="pet_communityadmin.php" class="nav-tab"> Pet Community</a>
                <a href="help_center.php" class="nav-tab"> Help Center</a> 
        </div>
</nav>

    <div class="main">
        <div class="breadcrumb" id="breadcrumb" style="display:none;">
            <span class="crumb" onclick="goBack()">Help Center</span>
            <span>,</span>
            <span class="crumb" id="breadcrumb-tab" onclick="goBack()"></span>
            <span>,</span>
            <span class="crumb-current" id="breadcrumb-title"></span>
        </div>
    <!tab faq guideline>
        <div class="tabs" id="tabs">
            <button class="tab active" onclick="switchTab('guideline', this)">Guideline</button>
            <button class="tab" onclick="switchTab('faq', this)">FAQ</button>
        </div>
     <!guideline>
        <div id="panel-guideline" class="panel active">
            <div class="section-header">
                <span class="section-title">Guidelines</span>
            </div>

            <div class="card-list">
            <?php
            if($total_guideline == 0)
            {
                 echo "<div class='empty-state'>
                        <strong> No guideline found </strong>
                        <p> There are no guideline available yet.</p>
                    </div>";
            }else{
               while($row_guideline = mysqli_fetch_assoc($result_guideline))
                {
                    if($row_guideline['PetType']== 'Cat'){
                        $icon = 'Cat';
                    }else{
                        $icon= 'Dog';
                    }

                    if(!empty($row_guideline['OrgName'])){
                        $org_name = $row_guideline['OrgName'];
                    }else{
                        $org_name = 'Unknown';
                    }
                    $data_json = htmlspecialchars(json_encode($row_guideline));
            ?>
                <div class="card">
                    <div class="card-top">
                        <span class="card-title">
                            <?php echo htmlspecialchars($row_guideline['Title']);?>
                        </span>
                    </div>

                    <div class="card-meta">
                        Pet Type: <?php echo htmlspecialchars($row_guideline['PetType']);?>
                        &nbsp;.&nbsp;
                        By: <?php echo htmlspecialchars($org_name);?>
                    </div>

                    <div class="card-preview">
                        <?php echo htmlspecialchars($row_guideline['Description']);?>
                    </div>
                    <div class="card-action">
                        <button class="btn-view" onclick="viewGuideline(<?php echo $data_json; ?>)"> View </button>
                    </div>

                </div>
        <?php
                }
            }
        ?>
        </div>
    </div>
        <!faq>
        <div id="panel-faq" class="panel">
                <div class="section-header">
                    <span class="section-title">Frequently Asked Questions</span>
                </div>
                <div class="card-list">
                <?php
                if($total_faq == 0)
                {
                 echo "<div class='empty-state'>
                        <strong> No FAQs found </strong>
                        <p> There are no FAQs available yet.</p>
                    </div>";
            }else{
                while($row_faq = mysqli_fetch_assoc($result_faq))
                {
                     if(!empty($row_faq['OrgName'])){
                        $org_name = $row_faq['OrgName'];
                    }else{
                        $org_name = 'Unknown';
                    }
                    $data_json = htmlspecialchars(json_encode($row_faq));
            ?>
                <div class="card">
                    <div class= "card-body">

                    <div class ="card-top">
                        <span class ="card-title">
                            <?php echo htmlspecialchars($row_faq['Question']);?>
                        </span>
                    </div>

                    <div class="card-meta">
                        By: <?php echo htmlspecialchars($org_name); ?>
                    </div>

                    <div class="card-preview">
                        <?php echo htmlspecialchars($row_faq['Description']); ?>
                    </div>

                    <div class="card-action">
                        <button class="btn-view" onclick="viewFaq(<?php echo $data_json; ?>)"> View </button>
                    </div>

                </div>
        </div>

        <?php
                }
            }
        ?>
        </div>
    </div>

        <!view detail >
        <div id="panel-view" class="panel">
            <div class="view-card" id="view-content"></div>
        </div>
    </div>
    <script src="../js/script.js"></script>
    <script src="../js/help_centeradmin.js"></script>
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
