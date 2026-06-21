<?php
    include("db_connect.php");

    $search = $_GET['search'] ?? '';

    if ($search !== '') {
        $sql = "SELECT Question, Description FROM faq WHERE Question LIKE ?";
        $stmt = $conn->prepare($sql);
        $like = "%$search%";
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $result = $stmt->get_result();
        
        
        $no_match = ($result->num_rows === 0) ? true : false;
    } else {
        
        $sql = "SELECT Question, Description FROM faq LIMIT 4";
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/base.css">
</head>

<body>
<div class="container">

    <nav class="navbar" id="navbar">
        <div class ="navbar-top">
            <a href="#" class="nav-logo">
            <img src="image/icons/logo.png" alt="Furever Pet Home">
            <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
            <button class="login-btn-nav" title="Log In" onclick="window.location.href='User_Login.php';">Log In</button>
            </div>
        </div>

        <!-- NAVIGATION -->
        <div class="nav-links">
            <a href="HomePage_Unregistered.php" class="nav-tab"> Home</a>
            <a href="Sign_Up.php" class="nav-tab"> Inbox</a>
            <a href="Sign_Up.php" class="nav-tab"> Find A Pet</a>
            <a href="Sign_Up.php" class="nav-tab"> Pet Community</a>
            <a href="helpcenter_unregister.php" class="nav-tab"> Help Center</a>
            <a href="analytics_unregister.php" class="nav-tab"> Analytics</a>
            <a href="Sign_Up.php" class="nav-tab"> Report</a>
        </div>
               
        </nav>

         <section class="sub-navbar">

        <button class = "guidelines-btn" onclick="window.location.href='guidelines_unregister.php';">Guidelines</button>
        <button class = "faq-btn" onclick="window.location.href='helpcenter_unregister.php';">FAQ</button>

        </section>

    <div class="search-container">
        <form method="GET" action="help_center.php">
            <input type="text" name="search" placeholder="Search Your Question Here..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="help-center">
        <h2><?php echo ($search !== '') ? 'Result FAQ' : 'Frequent Ask Question (FAQ)'; ?></h2>

        <ul class="faq-list">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<li class='faq-item'>";
                    echo "<strong class='faq-question'>Q: " . htmlspecialchars($row['Question']) . "</strong>";
                    echo "<p class='faq-answer'>A: " . htmlspecialchars($row['Description']) . "</p>";
                    echo "</li>";
                }
            } else {
                echo "<li class='faq-item' style='border-left: 4px solid var(--rose);'>";
                echo "<p class='faq-answer'>No FAQ found.</p>";
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


<script>
    var triggerAlert = <?php echo $no_match ? 'true' : 'false'; ?>;
</script>
<script src="../js/script.js"></script>

</body>
</html>
