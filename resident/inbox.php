<?php
    session_start();

    // Selaraskan sesi dengan residentID dari findapet.php
    $is_logged_in = isset($_SESSION['residentID']) && !empty($_SESSION['residentID']);

    if ($is_logged_in) {
        $resident_id = $_SESSION['residentID']; 
    } else {
        $resident_id = 'GUEST'; 
    }

    $conn = new mysqli("localhost", "root", "", "Furever_Pet_Home");

    if($conn->connect_error) {
        die("DB connection failed: " . $conn->connect_error);
    }

    // QUERY BARU: Memastikan permohonan baru yang berstatus 'Pending' terus masuk ke inbox user
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
        a.ResidentID = ? 
        OR r.ResidentID = ?
        OR (i.AdoptionID IS NULL AND i.ReportID IS NULL)
    ORDER BY i.DateTime DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $resident_id, $resident_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    $today = [];
    $yesterday = [];
    $week = [];
    $month = [];
    $year = [];

    $current_time = time();
    $today_date = date('Y-m-d', $current_time);
    $yesterday_date = date('Y-m-d', strtotime('-1 day', $current_time));
    $one_week_ago = strtotime('-7 days', $current_time);
    $current_month = date('Y-m', $current_time);
    $current_year = date('Y', $current_time);

    while($row = $result->fetch_assoc()) {

        $notif_time = strtotime($row['DateTime']);
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

        if ($notif_year === $current_year) {
            $year[] = $row;
        }

        if ($notif_month === $current_month) {
            $month[] = $row;
        }
    }
    $stmt->close();

    $conn->begin_transaction();

try {

    $stmt1 = $conn->prepare(
        "DELETE FROM inbox WHERE ReportID = ?"
    );
    $stmt1->bind_param("s", $reportID);
    $stmt1->execute();

    $stmt2 = $conn->prepare(
        "DELETE FROM report 
         WHERE ReportID = ? 
         AND ResidentID = ?"
    );
    $stmt2->bind_param("ss", $reportID, $residentID);
    $stmt2->execute();

    $conn->commit();

    echo "success";

} catch (Exception $e) {

    $conn->rollback();
    echo "error";
}
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
        <!-- LOGO and LOGIN -->
        <nav class="navbar" id="navbar">
        <div class ="navbar-top">
            <a href="#" class="nav-logo">
            <img src="../image/icons/logo.png" alt="Furever Pet Home">
            <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
            <button class="notif-btn" title="Notifications" onclick="window.location.href='resident/inbox.php';">🔔<span class="notif-dot"></span></button>
            <div class="avatar" title="My Profile">AT</div>
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

        <!--Notifications-->
        <div class="notif-container">

            <!--Left: Notification List-->
            <div class="notif-list">

                <!--Today-->
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

                <!--Yesterday-->
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

                <!--This Week-->
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

                <!--This Month-->
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

                <!--This Year-->
                <div class="notif-group" >
                    <div class="notif-group-header" onclick="toggleGroup('year')">
                        <span>This Year (<?= count($year) ?>)</span>
                        <span class="arrow" id="arrow-year">▸</span>
                    </div>
                    <div class="notif-items" id="year" style="display:none;">
                        <?php foreach ($year as $i=>$n): ?>
                        <div class="notif-item" onclick="openNotif(event, <?= $i ?>, 'year')" id="item-year-<?= $i ?>" >
                            <div class="notif-icon">🐾</div>
                            <div class="notif-info" >
                                <div class="notif-title"><?= htmlspecialchars($n['Title'])?></div>
                                <div class="notif-preview"><?= htmlspecialchars($n['Message'])?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!--Right: Content-->
            
            <div class="notif-content" id="notif-content">
                <div class="content-empty">Select a notification to view</div>
            </div>

        </div>

        <!--Footer-->
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
