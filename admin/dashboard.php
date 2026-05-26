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
        <div class="container">
            <div class="header">
                <h1> Furever Pet Home </h1>
            </div>

            <!--Navigation-->
            <div class="nav">
                <a href="HomePage.html" class="active">HOME</a>
                <a href="Inbox.html">INBOX</a>
                <a href="FindPet.html">FIND A PET</a>
                <a href="PetCommunity.html">PET COMMUNITY</a>
                <a href="RegisterPage.html">HELP CENTER</a>
                <a href="Analytics.html">ANALYTICS</a>
                <a href="Report.html">REPORT</a>
            </div>

            <!--Overview-->
            <div class="Overview">
                <h2>Overview</h2>
                <h3>Total Users: <?php echo $resident_count; ?></h3>
                <h3>Total NGOs: <?php echo $ngo_count; ?></h3>
                <h3>Reports: <?php echo $report_count; ?></h3>
                <h3>Adoption: <?php echo $adopt_count; ?></h3>
            </div>

            <!--New Residents-->
            <div class="New-Residents">
                <h2>New Residents</h2>
                <p>
                    <?php 
                    echo $new_resident 
                    ? $new_resident['FirstName'].' '.$new_resident['LastName'] 
                    : 'No data'; 
                    ?>
                </p> <!--get from database the current registered user -->
                <a href="user_count.html">View Residents</a>
            </div>

            <!--New NGOs-->
            <div class="New-NGOs">
                <h2>New NGOs</h2>
                <p><?php echo $new_ngo['OrgName'] ?? 'No data'; ?></p> <!--get from database the current registered NGO -->
                <a href="ngo_count.html">View NGOs</a>
            </div>

            <!--Reports-->
            <div class="Reports">
                <h2>Reports</h2>
                <p><?php echo $new_report['PetName'] ?? 'No data'; ?></p> <!--get from database the current report -->
                <a href="report.html">View Reports</a>
            </div>

            <!--Community Board-->
            <div class="Community-Board">
                <h2>Community Board</h2>
                <p><?php echo $new_post['Title'] ?? 'No data'; ?></p> <!--get from database the current post -->
                <a href="pet_communityadmin.html">View Posts</a>
            </div>

            <!--Guideline-->
            <div class="Guidelines">
                <h2>Guidelines</h2>
                <p><?php echo $new_guide['Title'] ?? 'No data'; ?></p> <!--get from database the current guideline -->
                <a href="guideline.html">View Guidelines</a>
            </div>

            <!--Help Center-->
            <div class="Help-Center">
                <h2>Help Center</h2>
                <p><?php echo $new_faq['Question'] ?? 'No data'; ?></p><!--get from database the current help article -->
                <a href="help_center.html">View Help Articles</a>
            </div>

        <!--Footer-->
        <footer>

            <div class="footer-top">

                <div class="logo">
                    <img src="../image/icons/logo.png" alt="Furever Pet Home Logo">
                    Furever Pet Home
                </div>

                <div class="footer-mid">
                    <p>41700 Bandar Klang, Selangor, Malaysia</p>

                    <p>
                        <a href="mailto:infor@FureverPetHome.com">
                            infor@FureverPetHome.com
                        </a>
                    </p>

                    <p>+60 123-456-7890</p>
                </div>

                <div class="footer-links">

                    <p><strong>Follow Us</strong></p>

                    <p>
                        <a href="https://www.facebook.com/FureverPetHome">
                            <img src="../image/icons/facebook.png" alt="Facebook">
                            Facebook
                        </a>

                        <a href="https://www.instagram.com/FureverPetHome">
                            <img src="../image/icons/instagram.png" alt="Instagram">
                            Instagram
                        </a>

                        <a href="https://www.x.com/FureverPetHome">
                            <img src="../image/icons/x.png" alt="Twitter">
                            X
                        </a>
                    </p>

                </div>

            </div>

            <div class="footer-bottom">
                <p>© 2026 FureverHome | Urban Pet Adoption & Community Management</p>
            </div>

        </footer>
    </body>
        <script src="../js/script.js"></script>

</html>
        
