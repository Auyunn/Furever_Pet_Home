<?php

include("../db_connect.php");

$search = $_GET['search'] ?? '';
$topic  = $_GET['topic'] ?? 'all';

if ($search !== '') {

    $sql = "SELECT Question, Description FROM faq WHERE Question LIKE ?";
    $stmt = $conn->prepare($sql);
    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();

    $no_match = ($result->num_rows == 0);

} else {

    $sql = "SELECT Question, Description FROM faq ";

    $stmt = $conn->prepare($sql);

    $stmt->execute();

    $result = $stmt->get_result();

    $no_match = ($result->num_rows == 0);
}

$cat_labels = [
    'adoption' => ['label' => 'Adoption Process', 'class' => 'badge-green'],
    'care' => ['label' => 'Pet Care', 'class' => 'badge-blue'],
    'fostering' => ['label' => 'Fostering', 'class' => 'badge-purple'],
    'volunteer' => ['label' => 'Volunteering', 'class' => 'badge-green']
];

$topic_keywords = [
    'adoption' => ['adopt', 'adoption', 'adopting'],
    'care' => ['care', 'health', 'vet', 'nutrition'],
    'fostering' => ['foster', 'fostering', 'temporary care'],
    'volunteer' => ['volunteer', 'volunteering', 'get involved']

];

function detect_category($question, $topics_keywords) {

        foreach ($topics_keywords as $slug => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($question, $keyword) !== false) {
                    return $slug;
                }
            }
        }
    
    return null;
}

$sql = "SELECT Question, Description FROM faq";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $all_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
 
    $filtered = [];
    foreach ($all_rows as $row) {
 
        // Free-text search box: match against question OR description.
        if ($search !== '') {
            $hit = (stripos($row['Question'], $search) !== false)
                || (stripos($row['Description'], $search) !== false);
            if (!$hit) {
                continue;
            }
        }
 
        // Topic filter from the "Browse by Topic" cards.
        if ($topic !== 'all') {
            $slug = detect_category($row['Question'] . ' ' . $row['Description'], $topic_keywords);
            if ($slug !== $topic) {
                continue;
            }
        }
 
        $filtered[] = $row;
    }
 
    $no_match = (count($filtered) === 0);
 ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help Center - Furever Pet Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/helpcenter_ngo.css">
    <link rel="stylesheet" href="../css/style.css">
    
</head>

<body>
<v class="container">

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
            <a href="Pet_listing.php" class="nav-tab"> Home</a>
            <a href="inbox.php" class="nav-tab"> Inbox</a>
            <a href="findapet.html" class="nav-tab"> Find A Pet</a>
            <a href="pet_community.html" class="nav-tab"> Pet Community</a>
            <a href="helpcenter_ngo.php" class="nav-tab"> Help Center</a>
            <a href="Analytics.html" class="nav-tab"> Analytics</a>
            <a href="report..php" class="nav-tab"> Report</a>
        </div>
               
        </nav>

         <section class="sub-navbar">

        <button class = "guidelines-btn" onclick="window.location.href='guidelines.php';">Guidelines</button>
        <button class = "faq-btn" onclick="window.location.href='help_center.php';">FAQ</button>

        </section>

    <div class="search-container">
        <form method="GET" action="help_center.php">
            
            <input type="text" name="search" placeholder="Search your question here ..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    
    <!------------ main contentS --------------->
    <div class="hc-page">

    <!------------ BROWSE TOPIC CATEGORIES --------------->
<div class="faq-categorries">
    <p class ="faq-category-label">BROWSE BY TOPIC</p>
    <div class="faq-cat-grid">

        <a href="help_center.php" class="faq-cat-card
         <?php echo ($search === '') ? 'active' : ''; ?>">
                <span class="faq-cat-icon">📚</span>
                <h3>All Categories</h3>
                <p>View all FAQ topics</p>
            </a>

        <a href="help_center.php? search=adoption" class="faq-cat-card 
               <?php echo ($search === 'adoption') ? 'active' : ''; ?>">
                <span class="faq-cat-icon">🐶</span>
                <h3>Adoption Process</h3>
                <p>Learn about adopting pets</p>
            </a>

        <a href="help_center.php?search=care"
               class="faq-cat-card <?php echo ($search === 'care') ? 'active' : ''; ?>">
                <span class="faq-cat-icon">💊</span>
                <h3>Pet Care</h3>
                <p>Health, vets &amp; nutrition</p>
            </a>

        <a href="help_center.php?search=fostering"
               class="faq-cat-card <?php echo ($search === 'fostering') ? 'active' : ''; ?>">
                <span class="faq-cat-icon">🏡</span>
                <h3>Fostering</h3>
                <p>Temporary care program</p>
            </a>
 
            
 
            <a href="help_center.php?search=volunteer"
               class="faq-cat-card <?php echo ($search === 'volunteer') ? 'active' : ''; ?>">
                <span class="faq-cat-icon">🤝</span>
                <h3>Volunteering</h3>
                <p>How to get involved</p>
            </a>
    </div>
</div>

<!-------------- FAQ ACCORDION ------------------------->

    <div class="faq-section">
        <div class="faq-header">
            <h2><?php echo ($search !== '') ? 'Search results for FAQ' : 'Frequently Asked Questions (FAQ)'; ?></h2>
            <a href="help_center.php" class="faq-view-all">View all articles →</a>
        </div>
 
        <?php if ($no_match): ?>
            <div class="faq-empty">
                <div class="faq-empty-icon">🔍</div>
                <p>No FAQ found matching your keyword.</p>
            </div>
 
        <?php else: ?>
            <ul class="faq-accordion">
            <?php
            $idx = 0;
            foreach ($filtered as $row):
                $idx++;
                $slug = detect_category($row['Question'] . ' ' . $row['Description'], $topic_keywords);
                $badge = $slug ? $cat_labels[$slug] ?? null : null;
            ?>
                <li class="faq-acc-item" id="faq-<?php echo $idx; ?>">
 
                    <!-- ── Trigger button ── -->
                    <button class="faq-acc-trigger"
                        id="faq-trigger-<?php echo $idx ; ?>"
                        onclick="toggleFaq(<?php echo $idx; ?>)"
                        aria-expanded="false">
                        <span><?php echo htmlspecialchars($row['Question']); ?></span>


                        <span class="faq-acc-right">
                            <?php if ($badge): ?>
                                <span class="faq-badge <?php echo $badge['class']; ?>">
                                    <?php echo $badge['label']; ?>
                                </span>
                            <?php endif; ?>

                            <span class="faq-chevron">
                                <svg viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                                    <polyline points="1,3.5 6,8.5 11,3.5"
                                              stroke-width="1.8"
                                              stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </span>
                    </button>
 
                    <!-- ── Answer panel ── -->
                    <div class="faq-acc-body" id="faq-body-<?php echo $idx; ?>">
                        <div class="faq-acc-inner">
                            <?php echo nl2br(htmlspecialchars($row['Description'])); ?>
                        </div>
                    </div>
 
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
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
<script>
function toggleFaq(idx) {

    var item = document.getElementById('faq-' + idx);
    var body = document.getElementById('faq-body-' + idx);
    var btn = document.getElementById('faq-acc-trigger-' + idx);
    var isOpen = item.classList.contains('is-open');

    document.querySelectorAll('.faq-acc-item').forEach(function(el) {

        el.classList.remove('is-open');
        var panel = el.querySelector('.faq-acc-body');

        if(panel){
            panel.setAttribute('aria-expanded', 'false');
        }

    });

    if (!isOpen) {

        item.classList.add('is-open');
        if(body){
            body.setAttribute('aria-expanded', 'true');
        }
    }
}
</script>
</body>
</html>
