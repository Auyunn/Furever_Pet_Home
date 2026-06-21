<?php
    session_start();
    $admin_name = $_SESSION['admin_name'] ?? 'Admin';
    
    include("../db_connect.php");

    // ===== COUNT DATA =====
    $resident_count = $conn->query("SELECT COUNT(*) as total FROM resident")->fetch_assoc()['total'];
    $ngo_count = $conn->query("SELECT COUNT(*) as total FROM organization")->fetch_assoc()['total'];
    $report_count = $conn->query("SELECT COUNT(*) as total FROM report")->fetch_assoc()['total'];
    $adopt_count = $conn->query("SELECT COUNT(*) as total FROM adopt_application")->fetch_assoc()['total'];
    $new_resident = $conn->query("SELECT FirstName, LastName FROM resident ORDER BY ResidentID DESC LIMIT 1")->fetch_assoc();
    $new_ngo = $conn->query("SELECT OrgName FROM organization ORDER BY OrgID DESC LIMIT 1")->fetch_assoc();
    $new_report = $conn->query("SELECT PetName FROM report ORDER BY ReportID DESC LIMIT 1")->fetch_assoc();
    $new_post = $conn->query("SELECT Title FROM community_board ORDER BY Date DESC LIMIT 1")->fetch_assoc();
    $new_guide = $conn->query("SELECT Title FROM guidelines ORDER BY GuidelineID DESC LIMIT 1")->fetch_assoc();
    $new_faq = $conn->query("SELECT Question FROM faq ORDER BY FaqID DESC LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en"> 
    <head>
        <meta charset="UTF-8">
        <title>Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/style.css">
        <style>
            .profile-dropdown { position: relative; }
            .dropdown-menu {
                display: none;
                position: absolute;
                top: calc(100% + 10px);
                right: 0;
                background: rgba(250,246,240,0.98);
                backdrop-filter: blur(16px);
                border-radius: 16px;
                padding: 1rem;
                min-width: 190px;
                box-shadow: 0 10px 30px rgba(44,26,14,0.14);
                border: 1px solid rgba(130,85,64,0.12);
                z-index: 200;
            }
            .dropdown-menu.show { display: block; }
            .dropdown-user-info {
                display: flex;
                flex-direction: column;
                gap: 0.2rem;
                padding-bottom: 0.8rem;
                border-bottom: 1px solid rgba(130,85,64,0.1);
                margin-bottom: 0.8rem;
            }
            .dropdown-user-info strong { font-size: 0.9rem; color: var(--deep-brown); }
            .dropdown-user-info span { font-size: 0.78rem; color: var(--text-muted); }
            .logout-btn {
                width: 100%;
                padding: 0.4rem 0;
                background: transparent;
                color: #c0392b;
                border: none;
                font-size: 0.88rem;
                font-weight: 600;
                cursor: pointer;
                text-align: left;
            }
            .logout-btn:hover { color: #e74c3c; }
        </style>
    </head>

    <body>
    <!-- Header -->
    <nav class="navbar" id="navbar">
        <!-- Logo and Profile -->
        <div class="navbar-top">
            <a href="#" class="nav-logo">
                <img src="../image/icons/logo.png" alt="Furever Pet Home">
                <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
                <div class="profile-dropdown">
                    <div class="avatar" title="My Profile" onclick="toggleProfileDropdown()" style="cursor:pointer;">A</div>
                    <div class="dropdown-menu" id="profileDropdown">
                        <div class="dropdown-user-info">
                            <strong><?php echo htmlspecialchars($admin_name); ?></strong>
                            <span>Admin Account</span>
                        </div>
                        <form method="post" action="../logout.php" style="margin:0;">
                            <button type="submit" class="logout-btn">&#128274; Log Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="nav-links">
            <a href="dashboard.php" class="nav-tab active">Dashboard</a>
            <a href="usercount.php" class="nav-tab">Users/NGOs</a>
            <a href="Add_Report.php" class="nav-tab">Report</a>
            <a href="analytics_admin.php" class="nav-tab">Analytics</a>
            <a href="pet_communityadmin.php" class="nav-tab">Pet Community</a>
            <a href="help_center.php" class="nav-tab">Help Center</a>
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
                <p><?php echo $new_resident ? htmlspecialchars($new_resident['FirstName'].' '.$new_resident['LastName']) : 'No data'; ?></p>
                <a href="usercount.php" class="dashboard-btn btn-resident">View Residents</a>
            </div>
            <div class="dashboard-card">
                <h2>New NGO</h2>
                <p><?php echo htmlspecialchars($new_ngo['OrgName'] ?? 'No data'); ?></p>
                <a href="usercount.php?tab=ngo" class="dashboard-btn btn-ngo">View NGOs</a>
            </div>
            <div class="dashboard-card">
                <h2>Latest Report</h2>
                <p><?php echo htmlspecialchars($new_report['PetName'] ?? 'No data'); ?></p>
                <a href="Add_Report.php" class="dashboard-btn btn-report">View Reports</a>
            </div>
            <div class="dashboard-card">
                <h2>Community Board</h2>
                <p><?php echo htmlspecialchars($new_post['Title'] ?? 'No data'); ?></p>
                <a href="pet_communityadmin.php" class="dashboard-btn btn-community">View Posts</a>
            </div>
            <div class="dashboard-card">
                <h2>Guidelines</h2>
                <p><?php echo htmlspecialchars($new_guide['Title'] ?? 'No data'); ?></p>
                <a href="help_center.php?tab=guidelines" class="dashboard-btn btn-guideline">View Guidelines</a>
            </div>
            <div class="dashboard-card">
                <h2>Help Center</h2>
                <p><?php echo htmlspecialchars($new_faq['Question'] ?? 'No data'); ?></p>
                <a href="help_center.php?tab=faq" class="dashboard-btn btn-help">View Help Articles</a>
            </div>
        </div>

    </div>

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


    <script src="../js/script.js"></script>
    </body>
</html>
