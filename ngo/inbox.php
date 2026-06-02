<?php
session_start();

$conn = new mysqli("localhost", "root", "", "furever_pet_home");
if ($conn->connect_error) {
    die("connection failed: " . $conn->connect_error);
}

/* =========================
   TEST AS NGO (TEMPORARY SIMULATION)
========================= */
if (!isset($_SESSION['orgID'])) {
    $_SESSION['orgID'] = "ORG01"; 
}

$org_id = $_SESSION['orgID'];

if (!$org_id) {
    die("Unauthorized: NGO not logged in.");
}

/* =========================
   FILTER VALIDATION
========================= */
$allowed = ['today', 'yesterday', 'this_week', 'this_month', 'this_year'];

$filter = $_GET['filter'] ?? 'today';
$filter = strtolower($filter);

if (!in_array($filter, $allowed)) {
    $filter = 'today';
}

/* =========================
   BUILD QUERY
========================= */
$where = " WHERE p.OrgID = ? ";
$params = [$org_id];
$types = "s";

/* =========================
   DATE FILTER
========================= */
switch ($filter) {

    case 'yesterday':
        $where .= " AND DATE(a.RequestDate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        break;

    case 'this_week':
        $where .= " AND YEARWEEK(a.RequestDate, 1) = YEARWEEK(CURDATE(), 1)";
        break;

    case 'this_month':
        $where .= " AND MONTH(a.RequestDate) = MONTH(CURDATE())
                    AND YEAR(a.RequestDate) = YEAR(CURDATE())";
        break;

    case 'this_year':
        $where .= " AND YEAR(a.RequestDate) = YEAR(CURDATE())";
        break;

    default:
        $where .= " AND DATE(a.RequestDate) = CURDATE()";
}

/* =========================
   SQL QUERY
========================= */
$sql = "
SELECT 
    a.AdoptionID,
    a.Status,
    a.RequestDate,
    p.PetID,
    p.PetName,
    r.ResidentID,
    r.FirstName,
    r.LastName
FROM adopt_application a
JOIN pet p ON a.PetID = p.PetID
JOIN resident r ON a.ResidentID = r.ResidentID
$where
ORDER BY a.RequestDate DESC
";

$stmt = $conn->prepare($sql);

/* =========================
   BIND PARAM (SAFE)
========================= */
$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Inbox</title>
    <link rel="stylesheet" href="../css/style.css">
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
            <div class="avatar" title="My Profile" onclick="window.location.href='User Login.html';">AT</div>
            </div>
        </div>

        <!-- NAVIGATION -->
        <div class="nav-links">
            <a href="../HomePage(registed).html" class="nav-tab">🏠 Home</a>
            <a href="inbox.php" class="nav-tab">✉️ Inbox</a>
            <a href="findapet.html" class="nav-tab">🔍 Find A Pet</a>
            <a href="pet_community.html" class="nav-tab"> 🐾Pet Community</a>
            <a href="help_center.php" class="nav-tab">❓ Help Center</a>
            <a href="../Analytics.html" class="nav-tab">📊 Analytics</a>
            <a href="Report.html" class="nav-tab">🚨 Report</a>
        </div>
               
        </nav>


<div class="filter-box">
    <label for="filter">Show:</label>

    <select id="filter" class="select-filter" onchange="applyFilter()">

        <option value="today" <?php echo ($filter=='today') ? 'selected' : ''; ?>>
            Today
        </option>

        <option value="yesterday" <?php echo ($filter=='yesterday') ? 'selected' : ''; ?>>
            Yesterday
        </option>

        <option value="this_week" <?php echo ($filter=='this_week') ? 'selected' : ''; ?>>
            This Week
        </option>

        <option value="this_month" <?php echo ($filter=='this_month') ? 'selected' : ''; ?>>
            This Month
        </option>

        <option value="this_year"  <?php echo ($filter == 'this_year') ? 'selected' : ''; ?>>
            This Year
        </option>

    </select>
</div>

<div class="ngo-inbox-container" >

    <div class="inbox-table-wrap">

        <table class="inbox-table">

            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Pet Name</th>
                    <th>Adopter Name</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php while($row = $result->fetch_assoc()) { 

                $status = isset($row['Status']) ? $row['Status'] : 'Pending';

                if($status == 'Approved')
                {
                    $badgeClass = 'badge_approved';
                }
                elseif($status == 'Rejected')
                {
                    $badgeClass = 'badge_rejected';
                }
                else
                {
                    $badgeClass = 'badge_pending';
                }

                if(isset($row['RequestDate']))
                {
                    $requestDate = date("d/m/Y H:i", strtotime($row['RequestDate']));
                }
                else
                {
                    $requestDate = '-';
                }
            ?>

                <tr id="row-<?php echo htmlspecialchars($row['AdoptionID']); ?>"
                    data-petid="<?php echo htmlspecialchars($row['PetID']); ?>">

                    <td>
                        <?php echo htmlspecialchars($row['AdoptionID']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['PetName']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?>
                    </td>

                    <td>
                        <?php echo $requestDate; ?>
                    </td>

                    <td>
                        <span class="<?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($status); ?>
                        </span>
                    </td>

                    <td class="action-btns">

                        <button class="btn-approve"
                            onclick="updateStatus('<?php echo htmlspecialchars($row['AdoptionID']); ?>','Approved')">
                            Approve
                        </button>

                        <button class="btn-reject"
                            onclick="updateStatus('<?php echo htmlspecialchars($row['AdoptionID']); ?>','Rejected')">
                            Reject
                        </button>

                        <button class="btn-view"
                            onclick="viewApp('<?php echo htmlspecialchars($row['AdoptionID']); ?>')">
                            View
                        </button>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

            <div class="inbox-right" id="side-panel">
                <div id="panel-content" class="panel-empty">
                    Click "View" on a request to see details here.
                </div>
            </div>

    </div>

</div>

<div class="inbox-container">

    <div class="inbox-left">
        <div class="inbox-table-wrap">

            <table class="inbox-table">
                </table>

        </div>
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

<script src="../js/script.js"></script>

</body>
</html>
