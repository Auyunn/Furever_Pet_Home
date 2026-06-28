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
date_default_timezone_set('Asia/Kuala_Lumpur'); 

if (empty($_SESSION['loggedin']) || empty($_SESSION['residentID']) || ($_SESSION['role'] ?? '') !== 'user') {
    header('Location: ../User_Login.php');
    exit;
}

$residentID = $_SESSION['residentID'];
$is_logged_in = true;

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
$lastName  = $resident['LastName'] ?? '';
$avatarInitials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));

// ── FETCH INBOX ──
$sql = "
SELECT 
    i.InboxID, 
    i.Title, 
    i.Message, 
    i.Status, 
    i.Type,
    i.DateTime
FROM inbox i
LEFT JOIN adopt_application a ON i.AdoptionID = a.AdoptionID
LEFT JOIN report r ON i.ReportID = r.ReportID
WHERE 
    (i.ReportID IS NOT NULL AND r.ResidentID = ?)
    OR 
    (i.AdoptionID IS NOT NULL AND a.ResidentID = ?)
    OR 
    (i.ReportID IS NULL AND i.AdoptionID IS NULL)
ORDER BY i.DateTime DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $residentID, $residentID);
$stmt->execute();
$result = $stmt->get_result();

$today = [];
$yesterday = [];
$week = [];
$month = [];
$year = [];

$current_time    = time();
$today_date      = date('Y-m-d', $current_time);
$yesterday_date  = date('Y-m-d', strtotime('-1 day', $current_time));
$one_week_ago    = strtotime('-7 days', $current_time);
$current_month   = date('Y-m', $current_time);
$current_year    = date('Y', $current_time);

while ($row = $result->fetch_assoc()) {
    $notif_time  = strtotime($row['DateTime']);
    if (!$notif_time) continue;

    $notif_date  = date('Y-m-d', $notif_time);
    $notif_month = date('Y-m', $notif_time);
    $notif_year  = date('Y', $notif_time);

    if ($notif_date === $today_date) {
        $today[] = $row;
        continue;
    }
    if ($notif_date === $yesterday_date) {
        $yesterday[] = $row;
        continue;
    }
    if ($notif_time >= $one_week_ago) {
        $week[] = $row;
        continue;
    }
    if ($notif_month === $current_month) {
        $month[] = $row;
        continue;
    }
    if ($notif_year === $current_year) {
        $year[] = $row;
    }
}
$stmt->close();
?>
<!Doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Resident Inbox</title>
        <link rel="stylesheet" href="../css/style.css">
        <script src="../js/script.js" defer></script>
    </head>

    <body>
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
                        <button class="logout-btn" onclick="window.location.href='../Logout.php'">&#128274;Logout</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="nav-links">
            <?php if($is_logged_in): ?>
                <a href="HomePage(registed).php" class="nav-tab">Home</a>
            <?php else: ?>
                <a href="../HomePage_Unregistered.php" class="nav-tab">Home</a>
            <?php endif; ?>
            <a href="inbox.php" class="nav-tab">Inbox</a>
            <a href="findapet.php" class="nav-tab"> Find A Pet</a>
            <a href="pet_community.php" class="nav-tab"> Pet Community</a>
            <a href="help_center.php" class="nav-tab"> Help Center</a>
            <a href="Analytics.php" class="nav-tab">Analytics</a>
            <a href="Report.php" class="nav-tab">Report</a>
        </div>
        </nav>

        <div class="notif-container">

            <div class="notif-list">

                <!--today-->
                <div class="notif-group">
                    <div class="notif-group-header" onclick="toggleGroup('today')">
                        <span>Today (<?= count($today) ?>)</span>
                        <span class="arrow" id="arrow-today">▾</span>
                    </div>
                    <div class="notif-items" id="today">
                        <?php foreach ($today as $i=>$n): ?>
                        <div class="notif-item" onclick="openNotif(event, <?= $i ?>, 'today')" id="item-today-<?= $i ?>">
                            <div class="notif-icon">🐾</div>
                            <div class="notif-info">
                                <div class="notif-title"><?= htmlspecialchars($n['Title']) ?></div>
                                <div class="notif-preview"><?= htmlspecialchars($n['Message']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!--yesterday-->
                <div class="notif-group">
                    <div class="notif-group-header" onclick="toggleGroup('yesterday')">
                        <span>Yesterday (<?= count($yesterday) ?>)</span>
                        <span class="arrow" id="arrow-yesterday">▾</span>
                    </div>
                    <div class="notif-items" id="yesterday">
                        <?php foreach($yesterday as $i=>$n): ?>
                        <div class="notif-item" onclick="openNotif(event, <?= $i ?>, 'yesterday')" id="item-yesterday-<?= $i ?>">
                            <div class="notif-icon">🐕</div>
                            <div class="notif-info">
                                <div class="notif-title"><?= htmlspecialchars($n['Title'])?></div>
                                <div class="notif-preview"><?= htmlspecialchars($n['Message'])?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!--week-->
                <div class="notif-group">
                    <div class="notif-group-header" onclick="toggleGroup('week')">
                        <span>This Week (<?= count($week) ?>)</span>
                        <span class="arrow" id="arrow-week">▸</span>
                    </div>
                    <div class="notif-items" id="week" style="display:none;">
                        <?php foreach ($week as $i=>$n): ?>
                        <div class="notif-item" onclick="openNotif(event, <?= $i ?>, 'week')" id="item-week-<?= $i ?>">
                            <div class="notif-icon">🐕</div>
                            <div class="notif-info">
                                <div class="notif-title"><?= htmlspecialchars($n['Title'])?></div>
                                <div class="notif-preview"><?= htmlspecialchars($n['Message'])?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!--month-->
                <div class="notif-group">
                    <div class="notif-group-header" onclick="toggleGroup('month')">
                        <span>This Month (<?= count($month) ?>)</span>
                        <span class="arrow" id="arrow-month">▸</span>
                    </div>
                    <div class="notif-items" id="month" style="display:none;">
                        <?php foreach ($month as $i=>$n): ?>
                        <div class="notif-item" onclick="openNotif(event, <?= $i ?>, 'month')" id="item-month-<?= $i ?>">
                            <div class="notif-icon">🐕</div>
                            <div class="notif-info">
                                <div class="notif-title"><?= htmlspecialchars($n['Title'])?></div>
                                <div class="notif-preview"><?= htmlspecialchars($n['Message'])?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!--year-->
                <div class="notif-group">
                    <div class="notif-group-header" onclick="toggleGroup('year')">
                        <span>This Year (<?= count($year) ?>)</span>
                        <span class="arrow" id="arrow-year">▸</span>
                    </div>
                    <div class="notif-items" id="year" style="display:none;">
                        <?php foreach ($year as $i=>$n): ?>
                        <div class="notif-item" onclick="openNotif(event, <?= $i ?>, 'year')" id="item-year-<?= $i ?>">
                            <div class="notif-icon">🐾</div>
                            <div class="notif-info">
                                <div class="notif-title"><?= htmlspecialchars($n['Title'])?></div>
                                <div class="notif-preview"><?= htmlspecialchars($n['Message'])?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <div class="notif-content" id="notif-content">
                <div class="content-empty">Select a notification to view</div>
            </div>

        </div>

        <!--footer-->
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
        window.notifData = {
            today: <?= json_encode($today) ?>,
            yesterday: <?= json_encode($yesterday) ?>,
            week: <?= json_encode($week) ?>,
            month: <?= json_encode($month) ?>,
            year: <?= json_encode($year) ?>
        };
        </script>
    </body>
</html>
