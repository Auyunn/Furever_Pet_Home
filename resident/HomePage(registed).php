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

if (empty($_SESSION['loggedin']) || empty($_SESSION['residentID']) || ($_SESSION['role'] ?? '') !== 'user') {
  header('Location: ../User_Login.php');
  exit;
}

$residentID = $_SESSION['residentID'];

$stmt = mysqli_prepare($conn, "SELECT ResidentID FROM resident WHERE ResidentID = ? AND Status = 1");
mysqli_stmt_bind_param($stmt, 's', $residentID);
mysqli_stmt_execute($stmt);
$authResult = mysqli_stmt_get_result($stmt);
$authRow = mysqli_fetch_assoc($authResult);
mysqli_stmt_close($stmt);

if (!$authRow) {
  session_unset();
  session_destroy();
  header('Location: ../User_Login.php');
  exit;
}

// ── FETCH RESIDENT INFO ──
$stmt = mysqli_prepare($conn, "SELECT FirstName, LastName FROM resident WHERE ResidentID = ?");
mysqli_stmt_bind_param($stmt, 's', $residentID);
mysqli_stmt_execute($stmt);
$residentResult = mysqli_stmt_get_result($stmt);
$resident = mysqli_fetch_assoc($residentResult);
mysqli_stmt_close($stmt);

$firstName = $resident['FirstName'] ?? 'Resident';
$lastName = $resident['LastName'] ?? '';
$avatarInitials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));

// ── DASHBOARD STATS ──

// Applications count + breakdown
$stmt = mysqli_prepare($conn, "SELECT Status, COUNT(*) AS cnt FROM adopt_application WHERE ResidentID = ? GROUP BY Status");
mysqli_stmt_bind_param($stmt, 's', $residentID);
mysqli_stmt_execute($stmt);
$appResult = mysqli_stmt_get_result($stmt);
$appCounts = ['Submit' => 0, 'Pending' => 0, 'Approved' => 0, 'Rejected' => 0];
$totalApplications = 0;
while ($row = mysqli_fetch_assoc($appResult)) {
  $appCounts[$row['Status']] = (int) $row['cnt'];
  $totalApplications += (int) $row['cnt'];
}
mysqli_stmt_close($stmt);
$applicationsSub = $appCounts['Pending'] . ' pending review, ' . $appCounts['Approved'] . ' approved';

// Reports filed count
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS cnt, SUM(Status = 'Resolved') AS resolved FROM report WHERE ResidentID = ?");
mysqli_stmt_bind_param($stmt, 's', $residentID);
mysqli_stmt_execute($stmt);
$reportResult = mysqli_stmt_get_result($stmt);
$reportRow = mysqli_fetch_assoc($reportResult);
mysqli_stmt_close($stmt);
$totalReports = (int) ($reportRow['cnt'] ?? 0);
$resolvedReports = (int) ($reportRow['resolved'] ?? 0);
$reportsSub = ($totalReports - $resolvedReports) . ' under investigation';

// Messages (inbox)
$stmt = mysqli_prepare($conn, "
    SELECT COUNT(*) AS cnt FROM inbox i
    LEFT JOIN report r ON i.ReportID = r.ReportID
    LEFT JOIN adopt_application a ON i.AdoptionID = a.AdoptionID
    WHERE r.ResidentID = ? OR a.ResidentID = ?
");
mysqli_stmt_bind_param($stmt, 'ss', $residentID, $residentID);
mysqli_stmt_execute($stmt);
$msgResult = mysqli_stmt_get_result($stmt);
$totalMessages = (int) (mysqli_fetch_assoc($msgResult)['cnt'] ?? 0);
mysqli_stmt_close($stmt);

// Favourites
$totalFavourites = 12;
$favouritesSub = '3 new matches this week';

// ── RECENT ACTIVITY FEED ──
$activityStmt = mysqli_prepare($conn, "
    SELECT i.Title, i.Message, i.DateTime, i.Type
    FROM inbox i
    LEFT JOIN report r ON i.ReportID = r.ReportID
    LEFT JOIN adopt_application a ON i.AdoptionID = a.AdoptionID
    WHERE r.ResidentID = ? OR a.ResidentID = ?
    ORDER BY i.DateTime DESC
    LIMIT 5
");
mysqli_stmt_bind_param($activityStmt, 'ss', $residentID, $residentID);
mysqli_stmt_execute($activityStmt);
$activityResult = mysqli_stmt_get_result($activityStmt);

function time_ago($datetime)
{
  $diff = time() - strtotime($datetime);
  if ($diff < 3600)
    return max(1, floor($diff / 60)) . 'm ago';
  if ($diff < 86400)
    return floor($diff / 3600) . 'h ago';
  if ($diff < 172800)
    return 'Yesterday';
  return floor($diff / 86400) . ' days ago';
}

// ── STATS STRIP ──
$petsAdoptedRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM adopt_application WHERE Status = 'Approved'"));
$petsAdopted = (int) $petsAdoptedRow['cnt'];

$availableNowRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM pet WHERE IsAvailable = 1"));
$availableNow = (int) $availableNowRow['cnt'];

$sheltersRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM organization"));

$membersRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM resident WHERE Status = 1"));
$memberCount = (int) $membersRow['cnt'];

// ── FEATURED PETS ──
$petsResult = mysqli_query($conn, "
    SELECT PetID, PetType, Breed, Age, Location, Photo, Gender, PetName
    FROM pet
    WHERE IsAvailable = 1
    ORDER BY PetID DESC
    LIMIT 4
");

$petTypeEmoji = ['Dog' => '🐕', 'Cat' => '🐈'];
$cardColors = ['#ead9c8', '#d9c8d4', '#c8d9d0', '#d4c8e0'];

// ── COMMUNITY POSTS ──
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

function org_initials($name)
{
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
  <title>Furever Pet Home – Dashboard</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/HomePage(registed).css">
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
          <img src="../image/icons/logo.png" alt="Furever Pet Home">
          <span>Furever Pet Home</span>
        </a>
        <div class="nav-right">
          <button class="notif-btn" title="Notifications" onclick="window.location.href='inbox.php';">🔔<span
              class="notif-dot"></span></button>
          <div class="profile-dropdown">
            <div class="avatar" title="My Profile" onclick="toggleProfileDropdown()"
              style="cursor:pointer; position: relative;">
              <?php echo htmlspecialchars($avatarInitials); ?>
            </div>

            <div class="dropdown-menu" id="profileDropdown">
              <div class="dropdown-user-info">
                <strong><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></strong>
                <span>Resident Account</span>
              </div>
              <form method="post" action="../logout.php" style="margin:0;">
                <button type="submit" class="logout-btn"
                  style="cursor: pointer; width: 100%; text-align: left;">&#128274; Log Out</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="nav-links">
        <a href="HomePage(registed).php" class="nav-tab">Home</a>
        <a href="inbox.php" class="nav-tab">Inbox</a>
        <a href="findapet.php" class="nav-tab">Find A Pet</a>
        <a href="pet_community.php" class="nav-tab">Pet Community</a>
        <a href="help_center.php" class="nav-tab">Help Center</a>
        <a href="Analytics.php" class="nav-tab">Analytics</a>
        <a href="Report.php" class="nav-tab">Report</a>
      </div>
    </nav>

    <div class="scroll-container">
      <section class="hero" id="hero">
        <p class="hero-eyebrow">Welcome back, <?php echo htmlspecialchars($firstName); ?> 🌿</p>
        <h1 class="hero-title">Every <em>stray</em> deserves a place to call <em>home</em></h1>
        <p class="hero-sub">Browse new arrivals, track your applications, and connect with fellow pet lovers in Bandar
          Klang.</p>
        <div class="hero-ctas">
          <a href="#pets" class="btn-primary">Browse Available Pets →</a>
          <a href="#dashboard" class="btn-ghost">My Dashboard</a>
        </div>
      </section>

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
          <div class="stat-num" data-target="<?php echo $sheltersRow['cnt']; ?>"><?php echo $sheltersRow['cnt']; ?>
          </div>
          <div class="stat-label">Shelters & NGOs</div>
        </div>
        <div class="stat-item">
          <div class="stat-num" data-target="<?php echo $memberCount; ?>"><?php echo $memberCount; ?></div>
          <div class="stat-label">Community Members</div>
        </div>
      </div>

      <section class="section" id="dashboard">
        <div class="section-header reveal">
          <div>
            <p class="section-eyebrow">My Account</p>
            <h2 class="section-title">Your Dashboard</h2>
          </div>
          <a href="#" class="section-link">View full profile →</a>
        </div>
        <div class="dash-grid">
          <div class="dash-card reveal">
            <div class="dash-card-icon icon-amber">❤️</div>
            <div class="dash-card-label">Favourites Saved</div>
            <div class="dash-card-value"><?php echo $totalFavourites; ?></div>
            <div class="dash-card-sub"><?php echo htmlspecialchars($favouritesSub); ?></div>
            <div class="dash-card-accent">❤️</div>
          </div>
          <div class="dash-card reveal">
            <div class="dash-card-icon icon-sage">📋</div>
            <div class="dash-card-label">Applications</div>
            <div class="dash-card-value"><?php echo $totalApplications; ?></div>
            <div class="dash-card-sub"><?php echo htmlspecialchars($applicationsSub); ?></div>
            <div class="dash-card-accent">📋</div>
          </div>
          <div class="dash-card reveal">
            <div class="dash-card-icon icon-rose">💬</div>
            <div class="dash-card-label">Messages</div>
            <div class="dash-card-value"><?php echo $totalMessages; ?></div>
            <div class="dash-card-sub">Updates from shelters</div>
            <div class="dash-card-accent">💬</div>
          </div>
          <div class="dash-card reveal">
            <div class="dash-card-icon icon-brown">🐾</div>
            <div class="dash-card-label">Reports Filed</div>
            <div class="dash-card-value"><?php echo $totalReports; ?></div>
            <div class="dash-card-sub"><?php echo htmlspecialchars($reportsSub); ?></div>
            <div class="dash-card-accent">🐾</div>
          </div>
        </div>

        <div style="margin-top:3rem;">
          <h3
            style="font-family:'Playfair Display',serif;font-size:1.3rem;margin-bottom:1.2rem;color:var(--deep-brown);"
            class="reveal">Recent Activity</h3>
          <div class="activity-feed">
            <?php if (mysqli_num_rows($activityResult) === 0): ?>
              <p style="color:var(--text-muted);">No recent activity yet.</p>
            <?php else: ?>
              <?php while ($activity = mysqli_fetch_assoc($activityResult)): ?>
                <?php
                $icon = $activity['Type'] === 'Pet Report' ? '🚨' : '📬';
                $iconBg = $activity['Type'] === 'Pet Report' ? 'rgba(201,125,125,0.12)' : 'rgba(107,143,113,0.12)';
                ?>
                <div class="activity-item reveal">
                  <div class="activity-icon" style="background:<?php echo $iconBg; ?>;"><?php echo $icon; ?></div>
                  <div class="activity-text">
                    <div class="activity-title"><?php echo htmlspecialchars($activity['Title']); ?></div>
                    <div class="activity-desc"><?php echo htmlspecialchars($activity['Message']); ?></div>
                  </div>
                  <div class="activity-time"><?php echo time_ago($activity['DateTime']); ?></div>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <section class="section" id="pets" style="background:var(--warm-tan);padding-top:5rem;padding-bottom:6rem;">
        <div class="section-header reveal">
          <div>
            <p class="section-eyebrow">New Arrivals</p>
            <h2 class="section-title">Pets Looking<br>for a Family</h2>
          </div>
          <a href="findapet.php" class="section-link">See all <?php echo $totalAvailablePets; ?> pets →</a>
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
                  <img src="../image/pets/<?php echo htmlspecialchars($pet['Photo']); ?>"
                    alt="<?php echo htmlspecialchars($pet['PetName']); ?>"
                    style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <?php else: ?>
                  <span class="emoji"
                    style="font-size: 3rem; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"><?php echo $emoji; ?></span>
                <?php endif; ?>
                <button class="pet-heart" onclick="toggleHeart(this)" style="position: absolute; z-index: 2;">🤍</button>
              </div>
              <div class="pet-info">
                <div class="pet-name-row">
                  <span class="pet-name"><?php echo htmlspecialchars($pet['PetName']); ?></span>
                  <span class="pet-distance"><?php echo htmlspecialchars($pet['Location']); ?></span>
                </div>
                <div class="pet-details">
                  <span class="pet-tag"><?php echo htmlspecialchars($pet['PetType']); ?></span>
                  <span class="pet-tag"><?php echo (int) $pet['Age']; ?>
                    yr<?php echo $pet['Age'] != 1 ? 's' : ''; ?></span>
                  <span class="pet-tag"><?php echo htmlspecialchars($pet['Breed']); ?></span>
                  <span class="pet-tag"><?php echo htmlspecialchars($pet['Gender']); ?></span>
                </div>
                <p class="pet-desc">Friendly and ready for a loving home. Vaccinated & cared for by shelter staff.</p>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </section>

      <div class="map-section reveal">
        <div class="map-blob"></div>
        <div class="map-content">
          <p class="map-eyebrow">Nearby Shelters</p>
          <h2 class="map-title">Find pets near you in Bandar Klang</h2>
          <p class="map-desc">Explore shelters and NGOs within your area. Filter by species, age, and distance to find
            your perfect match.</p>
          <a href="findapet.php" class="btn-primary">Open Pet Map →</a>
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
          <a href="pet_community.php" class="section-link">Join the conversation →</a>
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
              <div class="post-card reveal">
                <span class="post-emoji-big"></span>
                <div class="post-header">
                  <div class="post-avatar" style="background:<?php echo $accent; ?>;">
                    <?php echo htmlspecialchars($initials); ?>
                  </div>
                  <div>
                    <div class="post-meta-name"><?php echo htmlspecialchars($post['OrgName']); ?></div>
                    <div class="post-meta-time"><?php echo time_ago($post['Date']); ?></div>
                  </div>
                </div>
                <p class="post-text">
                  <strong><?php echo htmlspecialchars($post['Title']); ?></strong><br><?php echo htmlspecialchars($post['Content']); ?>
                </p>
                <div class="post-reactions"><span>❤️ —</span><span>💬 View replies</span><span>🔁 Share</span></div>
              </div>
            <?php endwhile; ?>
          <?php endif; ?>
        </div>
      </section>

      <footer>
        <div class="footer-grid">
          <div>
            <div style="font-size:2rem;">🐾</div>
            <div class="footer-brand-name">Furever Pet Home</div>
            <p class="footer-tagline">A compassionate digital hub for stray pet adoption and community care in Bandar
              Klang, Selangor.</p>
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

    <div class="welcome-ticker" id="ticker">
      <span>🐾</span>
      <div style="flex:1;">
        <div style="font-weight:500;font-size:0.85rem;">You have <?php echo $totalMessages; ?>
          message<?php echo $totalMessages != 1 ? 's' : ''; ?></div>
        <div style="font-size:0.75rem;color:rgba(255,255,255,0.55);">Check your inbox for updates.</div>
      </div>
      <button class="ticker-close" onclick="document.getElementById('ticker').remove()">✕</button>
    </div>

  </div>

  <!-- DENGAN ni: -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

  <script src="../js/HomePage_Registed.js"></script>
</body>

</html>
<?php mysqli_close($conn); ?>
