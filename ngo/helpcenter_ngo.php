<?php

    include ("../db_connect.php");
    $search =$_GET['search'];

    if ($search !== ''){
        $sql = "SELECT Question , Description FROM faq WHERE Question LIKE ?";

        $stmt = $conn->prepare($sql);
        $like = "%$search%";
        $stmt ->bind_param("s", $like );
        $stmt ->execute();
        $result = $stmt->get_result();

        $no_match = ($result->num_rows ===0) ? true : false;
    }

    else {

    $sql = "SELECT Question , Description FROM faq LIMIT 4";
    $stmt = $conn->prepare($sql);
    $stmt ->execute();
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
    <style>
        /* ── Browse By Topic ── */
        .faq-categories {
            padding: 2rem 1.5rem 0.5rem;
        }
        .faq-category-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #9E8E7E;
            margin-bottom: 1.25rem;
        }
        .faq-cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 1rem;
        }
        .faq-cat-card {
            background: #fff;
            border: 1.5px solid #DDD8D0;
            border-radius: 14px;
            padding: 1.3rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
        }
        .faq-cat-card:hover {
            border-color: #6FA882;
            box-shadow: 0 6px 18px rgba(74,124,89,0.12);
            transform: translateY(-2px);
        }
        .faq-cat-card.active {
            border-color: #4A7C59;
            background: #EAF2EC;
        }
        .faq-cat-icon { font-size: 2rem; margin-bottom: 0.55rem; }
        .faq-cat-card h3 { font-size: 0.9rem; font-weight: 600; margin-bottom: 0.2rem; color: #2C2C2C; }
        .faq-cat-card p  { font-size: 0.76rem; color: #9E8E7E; margin: 0; }
    </style>
</head>

<body>
<div class="container">

    <nav class="navbar" id="navbar">
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

        <!-- NAVIGATION -->
        <div class="nav-links">
            <a href="../HomePage(registed).html" class="nav-tab"> Home</a>
            <a href="inbox.php" class="nav-tab"> Inbox</a>
            <a href="findapet.html" class="nav-tab">Find A Pet</a>
            <a href="pet_community.html" class="nav-tab"> Pet Community</a>
            <a href="help_center.php" class="nav-tab"> Help Center</a>
            <a href="../Analytics.html" class="nav-tab"> Analytics</a>
            <a href="Report.html" class="nav-tab"> Report</a>
        </div>
               
        </nav>

         <section class="sub-navbar">

        <button class = "guidelines-btn" onclick="window.location.href='guidelines.php';">Guidelines</button>
        <button class = "faq-btn" onclick="window.location.href='help_center.php';">FAQ</button>

        </section>

    <div class="search-container">
        <form method="GET" action="help_center.php">
            <input type="text" name="search" placeholder="Cari soalan anda di sini..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!------------ BROWSE TOPIC CATEGORIES --------------->
</div class="faq-categorries">
    <p class ="faq-category-label">BROWSE BY TOPIC</p>
    <div class="faq-cat-grid">
        <div class= "faq-cat-card active" onclick="filterFaqCategory('all' , this)">
            <div class="faq-cat-icon">📚</div>
            <h3>All Categories</h3>
            <p>View all FAQ topics</p>
        </div>
        <div class= "faq-cat-card" onclick="filterFaqCategory('adoption' , this)">
            <div class="faq-cat-icon">🐶</div>
            <h3>Adoption Process</h3>
            <p>Learn about adopting pets</p>
        </div >
    </div>
</div>

    <div class="help-center">
        <h2><?php echo ($search !== '') ? 'Hasil Carian FAQ' : 'Soalan Lazim (FAQ)'; ?></h2>

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
                echo "<p class='faq-answer'>Tiada maklumat FAQ ditemui bagi kata kunci ini.</p>";
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



   

