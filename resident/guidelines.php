<?php
    session_start();
    include("../db_connect.php"); //call database

    //check current user id
    if (empty($_SESSION['loggedin']) || empty($_SESSION['residentID']) || ($_SESSION['role'] ?? '') !== 'user') {
        header('Location: ../User_Login.php');
        exit;
    }

    $residentID = $_SESSION['residentID'];
    $is_logged_in = true; //check log in
    
    // ── FETCH RESIDENT INFO ──
    $stmt = mysqli_prepare($conn, "SELECT FirstName, LastName FROM resident WHERE ResidentID = ?");
    mysqli_stmt_bind_param($stmt, 's', $residentID);
    mysqli_stmt_execute($stmt);
    $residentResult = mysqli_stmt_get_result($stmt);
    $resident = mysqli_fetch_assoc($residentResult);
    mysqli_stmt_close($stmt);

    $firstName = $resident['FirstName'] ?? 'Resident';
    $lastName  = $resident['LastName'] ?? '';
    $avatarInitials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));

    //for search bar
    $search = $_GET['search'] ?? '';

    if ($search !== '') {
        $sql = "SELECT Title, Description FROM guidelines WHERE Title LIKE ?";
        $stmt = $conn->prepare($sql);
        $like = "%$search%";
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $result = $stmt->get_result();
        
        
        $no_match = ($result->num_rows === 0) ? true : false;
    } else {
        //onlu show 4 guidelines
        $sql = "SELECT Title, Description FROM guidelines LIMIT 4";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $no_match = false;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help Center - Furever Pet Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
<div class="container">

    <!--Top bar-->
        <nav class="navbar" id="navbar">
        <div class ="navbar-top">
            <a href="#" class="nav-logo">
            <img src="../image/icons/logo.png" alt="Furever Pet Home">
            <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
                <button class="notif-btn" title="Notifications" onclick="window.location.href='inbox.php';">🔔<span class="notif-dot"></span></button>
                
                <div class="profile-dropdown">
                    <div class="avatar" title="My Profile" onclick="toggleProfileDropdown()" style="cursor:pointer;">
                        <?php echo htmlspecialchars($avatarInitials); ?>
                    </div>
                    <div id="profileDropdown" class="dropdown-menu">
                        <div class="dropdown-user-info">
                            <strong><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></strong>
                            <span><?php echo htmlspecialchars($residentID); ?></span>
                        </div>
                        <button class="logout-btn" onclick="window.location.href='../Logout.php'">&#128274; Logout</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- NAVIGATION -->
        <div class="nav-links">
                <a href="HomePage(registed).php" class="nav-tab">Home</a>
                <a href="inbox.php" class="nav-tab">Inbox</a>
                <a href="findapet.php" class="nav-tab"> Find A Pet</a>
                <a href="pet_community.php" class="nav-tab"> Pet Community</a>
                <a href="help_center.php" class="nav-tab"> Help Center</a>
                <a href="Analytics.php" class="nav-tab">Analytics</a>
                <a href="Report.php" class="nav-tab">Report</a>
            </div>
               
        </nav>

        <section class="sub-navbar">

        <button class = "guidelines-btn" onclick="window.location.href='guidelines.php';">Guidelines</button>
        <button class = "faq-btn" onclick="window.location.href='help_center.php';">FAQ</button>

        </section>
        
        <!-- sarh bar-->
    <div class="search-container">
        <form method="GET" action="guidelines.php">
            <input type="text" name="search" placeholder="Find Your Quetsion..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="help-center">
        <h2><?php echo ($search !== '') ? 'Result Of Search' : 'Guidelines'; ?></h2>

        <ul class="guidelines-list">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<li class='guidelines-item'>";
                    echo "<strong class='guidelines-title'>Q: " . htmlspecialchars($row['Title']) . "</strong>";
                    echo "<p class='guidelines-description'>A: " . htmlspecialchars($row['Description']) . "</p>";
                    echo "</li>";
                }
            } else {
                echo "<li class='guidelines-item' style='border-left: 4px solid var(--rose);'>";
                echo "<p class='guidelines-description'>No Information For This Keyword.</p>";
                echo "</li>";
            }
            ?>
        </ul>
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
                    <li><a href="HomePage(registed).php">Home</a></li>
                    <li><a href="inbox.php">Inbox</a></li>
                    <li><a href="findpet.php">Find A Pet</a></li>
                    <li><a href="pet_community.php">Community Board</a></li>
                    <li><a href="Analytics.php">Analytics</a></li>
                    <li><a href="Report.php">Report Animal</a></li>
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


<script>
    var triggerAlert = <?php echo $no_match ? 'true' : 'false'; ?>;
</script>
<script src="../js/script.js"></script>

</body>
</html>
