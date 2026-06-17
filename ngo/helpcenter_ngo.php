<?php

    include ("../db_connect.php");
    $search =$_GET['search'] ?? '';
    
    if ($search !== ''){
        $sql = "SELECT Question , Description, FROM faq WHERE Question LIKE ?";
        $stmt = $conn->prepare($sql);
        $like = "%$search%";
        $stmt ->bind_param("s", $like ,$like) ;
        $stmt ->execute();
        $result = $stmt->get_result();

        $no_match = ($result->num_rows ===0) ;

    }


    else {

    $sql = "SELECT Question , Description, FROM faq ";
    $stmt = $conn->prepare($sql);
    $stmt ->bind_param("s", $category);
    $stmt ->execute();
    $result = $stmt->get_result();
    $no_match = false;
    }

    $cat_labels = [
        'adoption' => ['label' => 'Adoption Process', 'class'=> 'badge-green'],
        'care' => ['label' => 'Pet Care', 'class'=> 'badge-green'],
        'fostering' => ['label' => 'Fostering', 'class'=> 'badge-green'],
        'volunteer'  => ['label' => 'Volunteering', 'class' => 'badge-green'],
        'popular'    => ['label' => 'Popular', 'class' => 'badge-amber'],
    ];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help Center - Furever Pet Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* ── Reset & base ── */
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --sage:        #4A7C59;
            --sage-light:  #6FA882;
            --sage-bg:     #EAF2EC;
            --amber:       #C8860A;
            --amber-bg:    #FDF3DC;
            --off-white:   #F5F0E8;
            --charcoal:    #2C2412;
            --taupe:       #9E8E7E;
            --border:      #E0D8CC;
            --white:       #FFFFFF;
            --radius:      14px;
            --shadow-sm:   0 2px 8px rgba(44,36,18,0.08);
            --shadow-md:   0 6px 20px rgba(44,36,18,0.12);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--off-white);
            color: var(--charcoal);
            margin: 0;
        }

        /* ── Help-center page wrapper ── */
        .hc-page {
            max-width: 860px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
        }

        /* ── BROWSE BY TOPIC ── */
        .faq-categories { margin-bottom: 2.5rem; }

        .faq-category-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.13em;
            text-transform: uppercase;
            color: var(--taupe);
            margin-bottom: 1.1rem;
        }

        .faq-cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
            gap: 0.85rem;
        }

        .faq-cat-card {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: 1.2rem 0.9rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
            text-decoration: none;
            display: block;
        }
        .faq-cat-card:hover {
            border-color: var(--sage-light);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        .faq-cat-card.active {
            border-color: var(--sage);
            background: var(--sage-bg);
        }
        .faq-cat-icon { font-size: 1.9rem; margin-bottom: 0.5rem; display: block; }
        .faq-cat-card h3 {
            font-size: 0.88rem;
            font-weight: 600;
            margin: 0 0 0.2rem;
            color: var(--charcoal);
        }
        .faq-cat-card p {
            font-size: 0.74rem;
            color: var(--taupe);
            margin: 0;
            line-height: 1.4;
        }

        /* ── FAQ SECTION ── */
        .faq-section { }

        .faq-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .faq-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--charcoal);
            margin: 0;
        }
        .faq-view-all {
            font-size: 0.85rem;
            color: var(--taupe);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .faq-view-all:hover { color: var(--sage); }

        /* ── ACCORDION ITEMS ── */
        .faq-accordion { display: flex; flex-direction: column; gap: 0.7rem; list-style: none; padding: 0; margin: 0; }

        .faq-acc-item {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: border-color 0.25s, box-shadow 0.25s;
            box-shadow: var(--shadow-sm);
        }
        .faq-acc-item.is-open {
            border-color: var(--sage-light);
            box-shadow: var(--shadow-md);
        }

        .faq-acc-trigger {
            width: 100%;
            background: none;
            border: none;
            padding: 1.1rem 1.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 0.97rem;
            font-weight: 600;
            color: var(--charcoal);
            text-align: left;
            transition: color 0.2s;
        }
        .faq-acc-trigger:hover { color: var(--sage); }
        .faq-acc-item.is-open .faq-acc-trigger { color: var(--sage); }

        .faq-acc-right {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-shrink: 0;
        }

        /* Badges */
        .faq-badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.22rem 0.65rem;
            border-radius: 50px;
            white-space: nowrap;
        }
        .badge-green { background: var(--sage-bg); color: var(--sage); border: 1px solid #c2dac9; }
        .badge-amber { background: var(--amber-bg); color: var(--amber); border: 1px solid #f0d080; }

        /* Chevron */
        .faq-chevron {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--off-white);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.3s, background 0.2s, border-color 0.2s;
        }
        .faq-chevron svg {
            width: 12px; height: 12px;
            stroke: var(--taupe);
            fill: none;
            transition: stroke 0.2s;
        }
        .faq-acc-item.is-open .faq-chevron {
            transform: rotate(180deg);
            background: var(--sage-bg);
            border-color: var(--sage-light);
        }
        .faq-acc-item.is-open .faq-chevron svg { stroke: var(--sage); }

        /* Answer panel */
        .faq-acc-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.38s ease;
        }
        .faq-acc-item.is-open .faq-acc-body { max-height: 500px; }

        .faq-acc-inner {
            border-top: 1px solid var(--border);
            padding: 1rem 1.4rem 1.25rem;
            font-size: 0.92rem;
            color: #5a5040;
            line-height: 1.78;
        }

        /* No results */
        .faq-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--taupe);
        }
        .faq-empty .faq-empty-icon { font-size: 2.5rem; margin-bottom: 0.75rem; }
        .faq-empty p { font-size: 0.95rem; }

        @media (max-width: 600px) {
            .faq-header { flex-direction: column; }
            .faq-acc-trigger { font-size: 0.9rem; padding: 0.95rem 1rem; }
            .faq-cat-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); }
        }
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
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
            <input type="text" name="search" placeholder="Search your question here ..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    
    <!------------ main contentS --------------->
    <div class="hc-page">

    <!------------ BROWSE TOPIC CATEGORIES --------------->
</div class="faq-categorries">
    <p class ="faq-category-label">BROWSE BY TOPIC</p>
    <div class="faq-cat-grid">

        <a href="help_center.php"
               class="faq-cat-card <?php echo ($category === 'all' && $search === '') ? 'active' : ''; ?>">
                <span class="faq-cat-icon">📚</span>
                <h3>All Categories</h3>
                <p>View all FAQ topics</p>
            </a>

        <a href="help_center.php?category=adoption"
               class="faq-cat-card <?php echo ($category === 'adoption') ? 'active' : ''; ?>">
                <span class="faq-cat-icon">🐶</span>
                <h3>Adoption Process</h3>
                <p>Learn about adopting pets</p>
            </a>

        <a href="help_center.php?category=care"
               class="faq-cat-card <?php echo ($category === 'care') ? 'active' : ''; ?>">
                <span class="faq-cat-icon">💊</span>
                <h3>Pet Care</h3>
                <p>Health, vets &amp; nutrition</p>
            </a>

        <a href="help_center.php?category=fostering"
               class="faq-cat-card <?php echo ($category === 'fostering') ? 'active' : ''; ?>">
                <span class="faq-cat-icon">🏡</span>
                <h3>Fostering</h3>
                <p>Temporary care program</p>
            </a>
 
            <a href="help_center.php?category=returns"
               class="faq-cat-card <?php echo ($category === 'returns') ? 'active' : ''; ?>">
                <span class="faq-cat-icon">↩️</span>
                <h3>Returns &amp; Rehoming</h3>
                <p>Surrendering a pet</p>
            </a>
 
            <a href="help_center.php?category=volunteer"
               class="faq-cat-card <?php echo ($category === 'volunteer') ? 'active' : ''; ?>">
                <span class="faq-cat-icon">🤝</span>
                <h3>Volunteering</h3>
                <p>How to get involved</p>
            </a>
    </div>
</div>

<!-------------- FAQ ACCORDION ------------------------->

    <div class="faq-section">
        <div class="faq-header">
            <h2><?php echo ($search !== '') ? 'Hasil Carian FAQ' : 'Soalan Lazim (FAQ)'; ?></h2>
            <a href="help_center.php" class="faq-view-all">View all articles →</a>
        </div>
 
        <?php if ($no_match || $result->num_rows === 0): ?>
            <div class="faq-empty">
                <div class="faq-empty-icon">🔍</div>
                <p>No Information needed FAQ using keyword.</p>
            </div>
 
        <?php else: ?>
            <ul class="faq-accordion">
            <?php
            $idx = 0;
            while ($row = $result->fetch_assoc()):
                $idx++;
                $slug  = strtolower(trim($row['Category'] ?? ''));
                $badge = $cat_labels[$slug] ?? null;
            ?>
                <li class="faq-acc-item" id="faq-<?php echo $idx; ?>">
 
                    <!-- ── Trigger button ── -->
                    <button class="faq-acc-trigger"
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
            <?php endwhile; ?>
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
            function triggerFaq(idx) {
                var item = document.getElementsById('faq-' + idx);
                var body = document.getElementById('faq-body-' + idx);
                var btn = document.getElementById('faq-acc-trigger-');
                var isOpen = item.classList.contains('is-open');

                //Tutup srmua
                document.querySelectorAll('.faq-acc-item.is-open').forEach(function(el) {
                    el.classList.remove('is-open');
                    el.querySelector('.faq-acc-body').setAttribute('aria-expanded', 'false');
                });

                //open kalu tetutup
                if (!isOpen) {
                    item.classList.add('is-open');
                    body.setAttribute('aria-expanded', 'true');
                }
            }
</script>
</body>
</html>
