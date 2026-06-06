<?php
    include("../db_connect.php");

    // ===== COUNT DATA =====

    // total residents
    $resident_count = $conn->query("SELECT COUNT(*) as total FROM resident")->fetch_assoc()['total'];

    // total NGOs
    $ngo_count = $conn->query("SELECT COUNT(*) as total FROM organization")->fetch_assoc()['total'];

    // total reports
    $report_count = $conn->query("SELECT COUNT(*) as total FROM report")->fetch_assoc()['total'];

    // total adoption
    $adopt_count = $conn->query("SELECT COUNT(*) as total FROM adopt_application")->fetch_assoc()['total'];

    // latest resident
    $new_resident = $conn->query("SELECT FirstName, LastName FROM resident ORDER BY ResidentID DESC LIMIT 1")->fetch_assoc();

    // latest NGO
    $new_ngo = $conn->query("SELECT OrgName FROM organization ORDER BY OrgID DESC LIMIT 1")->fetch_assoc();

    // latest report
    $new_report = $conn->query("SELECT PetName FROM report ORDER BY ReportID DESC LIMIT 1")->fetch_assoc();

    // latest post
    $new_post = $conn->query("SELECT Title FROM community_board ORDER BY Date DESC LIMIT 1")->fetch_assoc();

    // latest guideline
    $new_guide = $conn->query("SELECT Title FROM guidelines ORDER BY GuidelineID DESC LIMIT 1")->fetch_assoc();

    // latest FAQ
    $new_faq = $conn->query("SELECT Question FROM faq ORDER BY FaqID DESC LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en"> 
    <head>
        <meta charset="UTF-8">
        <title> Dashboard </title>
        <meta name = "viewport" content="width=device-width, initial-scale=1.0">
        <!--for now mesti save file ni dalam folder yang sama baru style dia muncul untuk testing-->
        <link rel="stylesheet" href="../css/style.css">
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
                <button class="notif-btn" title="Notifications" onclick="window.location.href='resident/inbox.php';">🔔<span class="notif-dot"></span></button>
                <div class="avatar" title="My Profile" onclick="window.location.href='User Login.html';">AT</div>
                </div>
            </div>

            <!---Tab Navigation-->
            <div class="nav-links">
                <a href="dashboard.php" class="nav-tab"> Dashboard</a>
                <a href=" " class="nav-tab"> Users/NGOs</a>
                <a href=" " class="nav-tab"> Report</a>
                <a href="../Analytics.html" class="nav-tab"> Analytics</a>
                <a href="pet_communityadmin.html" class="nav-tab">  Pet Community</a>
                <a href="help_center.html" class="nav-tab"> Help Center</a>
            </div>
    </nav>


<div class="dashboard-container">

    <div class="dashboard-title">
        <h1>Admin Dashboard</h1>
        <p>Monitor residents, NGOs, reports and platform activity.</p>
    </div>

    <!-- OVERVIEW -->
    <div class="dashboard-overview">

        <div class="overview-card">
            <div class="overview-label">Total Residents</div>
            <div class="overview-value"><?php echo $resident_count; ?></div>
            <div class="overview-sub">Registered users</div>
        </div>

        <div class="overview-card">
            <div class="overview-label">Total NGOs</div>
            <div class="overview-value"><?php echo $ngo_count; ?></div>
            <div class="overview-sub">Partner organizations</div>
        </div>

        <div class="overview-card">
            <div class="overview-label">Reports</div>
            <div class="overview-value"><?php echo $report_count; ?></div>
            <div class="overview-sub">Submitted reports</div>
        </div>

        <div class="overview-card">
            <div class="overview-label">Adoptions</div>
            <div class="overview-value"><?php echo $adopt_count; ?></div>
            <div class="overview-sub">Applications received</div>
        </div>

    </div>

    <!-- MAIN GRID -->
    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h2>New Resident</h2>
            <p>
                <?php
                echo $new_resident
                ? $new_resident['FirstName'].' '.$new_resident['LastName']
                : 'No data';
                ?>
            </p>
            <a href="user_count.html" class="dashboard-btn btn-resident">
                View Residents
            </a>
        </div>

        <div class="dashboard-card">
            <h2>New NGO</h2>
            <p><?php echo $new_ngo['OrgName'] ?? 'No data'; ?></p>
            <a href="ngo_count.html" class="dashboard-btn btn-ngo">
                View NGOs
            </a>
        </div>

        <div class="dashboard-card">
            <h2>Latest Report</h2>
            <p><?php echo $new_report['PetName'] ?? 'No data'; ?></p>
            <a href="report.html" class="dashboard-btn btn-report">
                View Reports
            </a>
        </div>

        <div class="dashboard-card">
            <h2>Community Board</h2>
            <p><?php echo $new_post['Title'] ?? 'No data'; ?></p>
            <a href="pet_communityadmin.html" class="dashboard-btn btn-community">
                View Posts
            </a>
        </div>

        <div class="dashboard-card">
            <h2>Guidelines</h2>
            <p><?php echo $new_guide['Title'] ?? 'No data'; ?></p>
            <a href="guideline.html" class="dashboard-btn btn-guideline">
                View Guidelines
            </a>
        </div>

        <div class="dashboard-card">
            <h2>Help Center</h2>
            <p><?php echo $new_faq['Question'] ?? 'No data'; ?></p>
            <a href="help_center.html" class="dashboard-btn btn-help">
                View Help Articles
            </a>
        </div>

    </div>

</div>
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
        <script src="../js/script.js"></script>

</html>
        
