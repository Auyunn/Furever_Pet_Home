<?php
session_start();
include('../db_connect.php');

//================ FETCH GUIDELINE DATA ============
$guideline ="SELECT g.GuidelineID, g.Title, g.PetType, g.Description, g.Budget, o.OrgName
            FROM guidelines g
            LEFT JOIN organization o ON g.OrgID= o.OrgID
            ORDER BY g.GuidelineID ASC";

$result_guideline= mysqli_query($conn, $guideline);
$total_guideline = mysqli_num_rows($result_guideline);

//==================== FETCH FAQ DATA ===========
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
    <!-- Header -->
   <nav class="navbar" id="navbar">
        <!--logo and profile-->
        <div class ="navbar-top">
            <a href="#" class="nav-logo">
            <img src="../image/icons/logo.png" alt="Furever Pet Home">
            <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
            <button class="notif-btn" title="Notifications" onclick="window.location.href='admin/inbox.php';">🔔<span class="notif-dot"></span></button>
            <div class="avatar" title="My Profile" onclick="window.location.href='User Login.html';">AT</div>
            </div>
        </div>

        <!---Tab Navigation-->
        <div class="nav-links">
            <a href="dashboard.php" class="nav-tab"> Dashboard</a>
            <a href=" " class="nav-tab">Users/NGOs</a>
            <a href=" " class="nav-tab">Report</a>
            <a href="../Analytics.html" class="nav-tab"> Analytics</a>
            <a href="pet_communityadmin.html" class="nav-tab"> Pet Community</a>
            <a href="help_center.html" class="nav-tab active"> Help Center</a>
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
    <!=== Tab =====>
        <div class="tabs" id="tabs">
            <button class="tab active" onclick="switchTab('guideline', this)">Guideline</button>
            <button class="tab" onclick="switchTab('faq', this)">FAQ</button>
        </div>
     <!=== Guideline =====>
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
<!===================== FAQ =============>
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

<!=============== View Detail ================= >
        <div id="panel-view" class="panel">
            <div class="view-card" id="view-content"></div>
        </div>
    </div>

    <script src="../js/help_centeradmin.js"></script>
<!================== FOOTER ===================== >
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
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Report Animal</a></li>
                <li><a href="#">Analytics</a></li>
                <li><a href="#">Pet Community</a></li>
                <li><a href="#">Help Center</a></li>
                </ul>
            </div>
            <div>
                <p class="footer-col-title">Account</p>
                <ul class="footer-links-list">
                <li><a href="#">My Profile</a></li>
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