<?php

session_start();
require_once '../db_connect.php';
/** @var mysqli $conn */

// Pastikan dah login
if (empty($_SESSION['residentID'])) {
    header("Location: ../User_Login.php");
    exit();
}
$residentID = $_SESSION['residentID'];
if (isset($_POST['delete_report'])) {

    $reportID = $_POST['reportID'];

    $stmtDelete = $conn->prepare(
        "DELETE FROM report 
         WHERE ReportID = ? 
         AND ResidentID = ?"
    );

    $stmtDelete->bind_param("ss", $reportID, $residentID);

    if ($stmtDelete->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    exit();
}

// Fetch nama untuk avatar initials
$stmtAvatar = $conn->prepare("SELECT FirstName, LastName FROM resident WHERE ResidentID = ?");
$stmtAvatar->bind_param('s', $residentID);
$stmtAvatar->execute();
$residentRow = $stmtAvatar->get_result()->fetch_assoc();
$stmtAvatar->close();

$firstName = $residentRow['FirstName'] ?? '';
$lastName  = $residentRow['LastName'] ?? '';
$avatarInitials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));


// Pastikan user dah login, kalau tak ada session, redirect ke login page
if (!isset($_SESSION['residentID'])) {
    header("Location: ../User_Login.php"); 
    exit();
}

$residentID = $_SESSION['residentID'];

$sql = "
    SELECT
        r.ReportID,
        r.PetName,
        r.Location,
        r.Description  AS ReportDescription,
        r.Status        AS ReportStatus,
        r.Photo,
        i.Status         AS InboxStatus,
        i.Message        AS InboxMessage,
        i.DateTime       AS InboxDateTime
    FROM report r
    LEFT JOIN inbox i
        ON i.InboxID = (
            SELECT i2.InboxID
            FROM inbox i2
            WHERE i2.ReportID = r.ReportID
              AND i2.Type = 'Pet Report'
            ORDER BY i2.DateTime DESC
            LIMIT 1
        )
    WHERE r.ResidentID = ?
    ORDER BY r.ReportID DESC
";

try {
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param('s', $residentID);
    $stmt->execute();
    $result = $stmt->get_result();

    $reports = [];
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }

} catch (Exception $e) {
    header("Location: error.php");
    exit();
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
}

// --- Helpers ------------------------------------------------------

/**
 * Collapse inbox.Status / report.Status into one of: pending, reviewing, resolved
 */
function mapStatus(array $row): string
{
    if (!empty($row['InboxStatus'])) {
        switch ($row['InboxStatus']) {
            case 'Resolve':
                return 'resolved';
            case 'In Progress':
                return 'reviewing';
            case 'Pending':
                return 'pending';
        }
    }
    // No inbox entry yet — fall back to the report's own status.
    return $row['ReportStatus'] === 'Resolved' ? 'resolved' : 'pending';
}

/** Badge label + icon for a status key */
function statusBadge(string $status): array
{
    return match ($status) {
        'resolved' => ['✓ Resolved', 'resolved'],
        'reviewing' => ['🔍 Reviewing', 'reviewing'],
        default => ['⏳ Pending', 'pending'],
    };
}

/** Safely encode a PHP value for use inside an inline onclick="" JS call */
function jsStr(?string $val): string
{
    return htmlspecialchars(json_encode($val ?? ''), ENT_QUOTES, 'UTF-8');
}



const REPORT_PHOTO_DIR = '../image/report/';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - Furever Pet Home</title>
    <link rel="stylesheet" href="../css/ReportStatus.css">
    <link rel="stylesheet" href="../css/base.css">
    <script src="../js/Report_Status.js"></script>
</head>

<body>

    <div class="wrapper">

        <nav class="navbar scrolled" id="navbar"> 
            <div class="navbar-top">
                <a href="#" class="nav-logo">
                    <img src="../image/icons/logo.png" alt="Furever Pet Home">
                    <span>Furever Pet Home</span>
                </a>
                <div class="nav-right">
                    <button class="notif-btn" title="Notifications" onclick="window.location.href='inbox.php';">🔔<span class="notif-dot"></span></button>
                     <div class="avatar" title="My Profile"><?php echo htmlspecialchars($avatarInitials); ?></div>
                </div>
            </div>

            <div class="nav-links">
                <a href="HomePage(registed).php" class="nav-tab">Home</a>
                <a href="inbox.php" class="nav-tab">Inbox</a>
                <a href="findapet.php" class="nav-tab">Find A Pet</a>
                <a href="pet_community.php" class="nav-tab">Pet Community</a>
                <a href="help_center.php" class="nav-tab">Help Center</a>
                <a href="Analytics.php" class="nav-tab">Analytics</a>
                <a href="Report.php" class="nav-tab active">Report</a>
            </div>
        </nav>

        <h1 class="page-title">REPORT</h1>

        <main>

            <div class="report-topbar">
                <div class="filter-tabs">
                    <button class="filter-btn active" onclick="filterReports('all', this)">All</button>
                    <button class="filter-btn" onclick="filterReports('resolved', this)">Resolved</button>
                    <button class="filter-btn" onclick="filterReports('pending', this)">Pending</button>
                    <button class="filter-btn" onclick="filterReports('reviewing', this)">Reviewing</button>
                </div>
                <button class="btn-add" onclick="window.location.href='AddReport.php'">＋ Add Report</button>
            </div>

            <div class="report-list" id="reportList" <?= empty($reports) ? 'style="display:none;"' : '' ?>>

                <?php foreach ($reports as $row): ?>
                    <?php
                    $status = mapStatus($row);
                    [$badgeLabel, $badgeClass] = statusBadge($status);

                    $cardDesc = $row['ReportDescription'] ?? '';
                    $modalDesc = !empty($row['InboxMessage']) ? $row['InboxMessage'] : $cardDesc;

                    $dateFiled = !empty($row['InboxDateTime'])
                        ? date('j M Y', strtotime($row['InboxDateTime']))
                        : 'Pending Review';

                    $hasPhoto = !empty($row['Photo']);
                    $photoPath = $hasPhoto ? REPORT_PHOTO_DIR . htmlspecialchars($row['Photo']) : '';
                    ?>
                    <article class="report-card" data-status="<?= htmlspecialchars($status) ?>">
                        <?php if ($hasPhoto): ?>
                            <div class="card-image">
                                <img src="<?= $photoPath ?>" alt="<?= htmlspecialchars($row['PetName']) ?>"
                                    onerror="this.parentElement.classList.add('no-img')">
                            </div>
                        <?php else: ?>
                            <div class="card-image pending-img">
                                <span class="pending-icon">🕐</span>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <div class="card-header">
                                <span class="card-title"><?= htmlspecialchars($row['PetName']) ?></span>
                                <span class="status-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                            </div>
                            <p class="card-desc"><?= htmlspecialchars($cardDesc) ?></p>
                            <div class="card-meta">
                                <span class="meta-item">📍 <?= htmlspecialchars($row['Location']) ?></span>
                                <span class="meta-item">🗓 <?= htmlspecialchars($dateFiled) ?></span>
                            </div>
                            <div class="card-footer">
                                <span class="report-id"><?= htmlspecialchars($row['ReportID']) ?></span>
                                <div class="card-actions">
                                    <button class="btn-action btn-view"
                                        onclick="openModal(<?= jsStr($status) ?>, <?= jsStr($row['PetName']) ?>, <?= jsStr($modalDesc) ?>, <?= jsStr($row['Location']) ?>, <?= jsStr($dateFiled) ?>, <?= jsStr($row['ReportID']) ?>)">View
                                        Details</button>
                                    <button class="btn-action btn-remove" onclick="deleteReport('<?= $row['ReportID'] ?>', this)">Remove</button>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>

            </div><div class="empty-state" id="emptyState" style="<?= empty($reports) ? '' : 'display:none;' ?>">
                <span class="empty-icon">📋</span>
                <p>ADD A REPORT NOW!</p>
            </div>

        </main>

        <div class="modal-backdrop" id="modalBackdrop" onclick="closeModal()">
            <div class="modal" onclick="event.stopPropagation()">
                <button class="modal-close" onclick="closeModal()">✕</button>
                <div class="modal-status-bar" id="modalStatusBar"></div>
                <h2 class="modal-title" id="modalTitle"></h2>
                <p class="modal-desc" id="modalDesc"></p>
                <div class="modal-meta">
                    <div class="modal-meta-row"><span class="modal-meta-label">Location</span><span
                            id="modalLocation"></span></div>
                    <div class="modal-meta-row"><span class="modal-meta-label">Date Filed</span><span
                            id="modalDate"></span></div>
                    <div class="modal-meta-row"><span class="modal-meta-label">Report ID</span><span
                            id="modalId"></span></div>
                    <div class="modal-meta-row"><span class="modal-meta-label">Status</span><span
                            class="modal-status-text" id="modalStatusText"></span></div>
                </div>
                <div class="modal-timeline" id="modalTimeline"></div>
            </div>
        </div>
  
        <footer>
            <div class="footer-grid">
                <div>
                    <div style="font-size:2rem;">🐾</div>
                    <div class="footer-brand-name">Furever Pet Home</div>
                    <p class="footer-tagline">A compassionate digital hub for stray pet adoption and community care
                        in Bandar Klang, Selangor.</p>
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
        
    </div>
<script src="../js/script.js?v=<?= time(); ?>"></script>
</body>
</html>
<?php $conn->close(); ?>
