<?php

// ── START SESSION ──
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── DATABASE CONNECTION ──
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'furever_pet_home';

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// ── NO AUTH CHECK — public page ──

function time_ago($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 3600) return max(1, floor($diff / 60)) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 172800) return 'Yesterday';
    return floor($diff / 86400) . ' days ago';
}

//  STATS STRIP 
$petsAdoptedRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM adopt_application WHERE Status = 'Approved'"));
$petsAdopted = (int) $petsAdoptedRow['cnt'];

$availableNowRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM pet WHERE IsAvailable = 1"));
$availableNow = (int) $availableNowRow['cnt'];

$sheltersRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM organization"));

$membersRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM resident WHERE Status = 1"));
$memberCount = (int) $membersRow['cnt'];

//  FEATURED PETS 
$petsResult = mysqli_query($conn, "
    SELECT PetID, PetType, Breed, Age, Location, Photo, Gender, PetName
    FROM pet
    WHERE IsAvailable = 1
    ORDER BY PetID DESC
    LIMIT 4
");

$petTypeEmoji = ['Dog' => '🐕', 'Cat' => '🐈'];
$cardColors = ['#ead9c8', '#d9c8d4', '#c8d9d0', '#d4c8e0'];

//  COMMUNITY POSTS 
$postsResult = mysqli_query($conn, "
    SELECT cb.BoardID,
           cb.Title,
           cb.Content,
           cb.Date,
           o.OrgName
    FROM community_board cb
    JOIN organization o ON cb.OrgID = o.OrgID
    ORDER BY cb.Date DESC
    LIMIT 4
");

function org_initials($name) {
    $words = preg_split('/\s+/', trim($name));
    $initials = '';
    foreach (array_slice($words, 0, 2) as $w) {
        $initials .= strtoupper(substr($w, 0, 1));
    }
    return $initials ?: 'NG';
}
$postAccentColors = ['var(--amber)', 'var(--sage)', 'var(--rose)', '#8b6fcf'];

$totalAvailablePets = $availableNow;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Furever Pet Home</title>
<link rel="stylesheet" href="css/base.css">
<link rel="stylesheet" href="css/HomePage(registed).css">
 <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div id="progress-bar"></div>

<canvas id="hero-canvas"></canvas>

<div class="parallax-layer p-paw" id="paw1" style="top:20vh;left:5vw;">🐾</div>
<div class="parallax-layer p-paw" id="paw2" style="top:55vh;right:6vw;font-size:3rem;">🐾</div>
<div class="parallax-layer p-paw" id="paw3" style="top:80vh;left:12vw;font-size:2.5rem;">🐾</div>

<div class="wrapper">
  <nav class="navbar" id="navbar">
    <div class="navbar-top">
      <a href="#" class="nav-logo">
        <img src="image/icons/logo.png" alt="Furever Pet Home">
        <span>Furever Pet Home</span>
      </a>
      <div class="login">
                <a href="User_Login.php" title="Log In">Log In</a> <!--log in icon -->
      </div>
    </div>

    <!-- navigation -->
    <div class="nav-links">
      <a href="HomePage_Unregistered.php" class="nav-tab">Home</a>
      <a href="User_Login.php" class="nav-tab">Inbox</a>
      <a href="User_Login.php" class="nav-tab">Find A Pet</a>
      <a href="User_Login.php" class="nav-tab">Pet Community</a>
      <a href="helpcenter_unregister.php" class="nav-tab">Help Center</a>
      <a href="analytics_unregister.php" class="nav-tab">Analytics</a>
      <a href="User_Login.php" class="nav-tab">Report</a>
    </div>
  </nav>

  <!-- banner -->
  <div class="scroll-container">
    <section class="hero" id="hero">
      <p class="hero-eyebrow">Welcome to Furever Pet Home 🌿</p>
      <h1 class="hero-title">Every <em>stray</em> deserves a place to call <em>home</em></h1>
      <p class="hero-sub">Browse available pets, connect with shelters, and join a community of pet lovers in Bandar Klang.</p>
      <div class="hero-ctas">
        <a href="#pets" class="btn-primary">Browse Available Pets →</a>
        <a href="User_Register.php" class="btn-ghost">Join Now</a>
      </div>
    </section>

    <!-- show overview -->
    <div class="stats-strip reveal">
      <div class="stat-item">
        <div class="stat-num" data-target="<?php echo $petsAdopted; ?>"><?php echo $petsAdopted; ?></div>
        <div class="stat-label">Pets Adopted</div>
      </div>
      <div class="stat-item">
        <div class="stat-num" data-target="<?php echo $availableNow; ?>"><?php echo $availableNow; ?></div>
        <div class="stat-label">Available Now</div>
      </div>
      <div class="stat-item">
        <div class="stat-num" data-target="<?php echo $sheltersRow['cnt']; ?>"><?php echo $sheltersRow['cnt']; ?></div>
        <div class="stat-label">Shelters & NGOs</div>
      </div>
      <div class="stat-item">
        <div class="stat-num" data-target="<?php echo $memberCount; ?>"><?php echo $memberCount; ?></div>
        <div class="stat-label">Community Members</div>
      </div>
    </div>

    <!-- show newest pet -->
    <section class="section" id="pets" style="background:var(--warm-tan);padding-top:5rem;padding-bottom:6rem;">
      <div class="section-header reveal">
        <div>
          <p class="section-eyebrow">New Arrivals</p>
          <h2 class="section-title">Pets Looking<br>for a Family</h2>
        </div>
        <a href="User_Login.php" class="section-link">See all <?php echo $totalAvailablePets; ?> pets →</a>
      </div>
      <div class="pets-grid">
        <?php
        $i = 0;
        while ($pet = mysqli_fetch_assoc($petsResult)):
            $emoji = $petTypeEmoji[$pet['PetType']] ?? '🐾';
            $bgColor = $cardColors[$i % count($cardColors)];
            $i++;
        ?>
          <div class="pet-card reveal">
            <div class="pet-img" style="background:<?php echo $bgColor; ?>; position: relative; overflow: hidden;">
              <?php if (!empty($pet['Photo'])): ?>
                <img src="image/pets/<?php echo htmlspecialchars($pet['Photo']); ?>"
                    alt="<?php echo htmlspecialchars($pet['PetName']); ?>"
                    style="width: 100%; height: 100%; object-fit: cover; display: block;">
              <?php else: ?>
                <span class="emoji" style="font-size: 3rem; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"><?php echo $emoji; ?></span>
              <?php endif; ?>
            </div>
            <div class="pet-info">
              <div class="pet-name-row">
                <span class="pet-name"><?php echo htmlspecialchars($pet['PetName']); ?></span>
                <span class="pet-distance"><?php echo htmlspecialchars($pet['Location']); ?></span>
              </div>
              <div class="pet-details">
                <span class="pet-tag"><?php echo htmlspecialchars($pet['PetType']); ?></span>
                <span class="pet-tag"><?php echo (int) $pet['Age']; ?> yr<?php echo $pet['Age'] != 1 ? 's' : ''; ?></span>
                <span class="pet-tag"><?php echo htmlspecialchars($pet['Breed']); ?></span>
                <span class="pet-tag"><?php echo htmlspecialchars($pet['Gender']); ?></span>
              </div>
              <p class="pet-desc">Friendly and ready for a loving home. Vaccinated & cared for by shelter staff.</p>
              <a href="User_Login.php" class="btn-primary" style="margin-top:0.8rem;display:inline-block;font-size:0.85rem;">Login to Adopt →</a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </section>

    <!-- Show map -->
    <div class="map-section reveal">
      <div class="map-blob"></div>
      <div class="map-content">
        <p class="map-eyebrow">Nearby Shelters</p>
        <h2 class="map-title">Find pets near you in Bandar Klang</h2>
        <p class="map-desc">Explore shelters and NGOs within your area. Filter by species, age, and distance to find your perfect match.</p>
        <a href="User_Register.php" class="btn-primary">Register to Explore →</a>
      </div>
      <div class="map-mini">
        🗺️
        <div class="map-pin" style="top:38%;left:42%;"></div>
        <div class="map-pin" style="top:60%;left:65%;animation-delay:0.7s;"></div>
        <div class="map-pin" style="top:25%;left:70%;animation-delay:1.4s;"></div>
      </div>
    </div>

    <section class="section" id="community">
      <div class="section-header reveal">
        <div>
          <p class="section-eyebrow">Pet Community</p>
          <h2 class="section-title">What People<br>Are Sharing</h2>
        </div>
        <a href="User_Login.php" class="section-link">Login to join →</a>
      </div>
      <div class="community-posts">
        <?php if (mysqli_num_rows($postsResult) === 0): ?>
          <p style="color:var(--text-muted);">No community posts yet.</p>
        <?php else: ?>
          <?php
          $j = 0;
          while ($post = mysqli_fetch_assoc($postsResult)):
              $accent = $postAccentColors[$j % count($postAccentColors)];
              $initials = org_initials($post['OrgName']);
              $j++;
          ?>
          <!-- comment -->
            <div class="post-card reveal">
              <span class="post-emoji-big"></span>
              <div class="post-header">
                <div class="post-avatar" style="background:<?php echo $accent; ?>;"><?php echo htmlspecialchars($initials); ?></div>
                <div>
                  <div class="post-meta-name"><?php echo htmlspecialchars($post['OrgName']); ?></div>
                  <div class="post-meta-time"><?php echo time_ago($post['Date']); ?></div>
                </div>
              </div>
              <p class="post-text"><strong><?php echo htmlspecialchars($post['Title']); ?></strong><br><?php echo htmlspecialchars($post['Content']); ?></p>
              <div class="post-reactions"><span>❤️ —</span><span>💬 View replies</span><span>🔁 Share</span></div>
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>
    </section>

    <!-- Footer -->
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
              <li><a href="inbox.php">Inbox</a></li>
              <li><a href="findapet.php">Find A Pet</a></li>
              <li><a href="pet_community.php">Community Board</a></li>
              <li><a href="guidelines.php">Help Center</a></li>
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
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="js/HomePage_Registed.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
