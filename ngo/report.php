<?php
session_start();

$conn = new mysqli("localhost", "root", "", "furever_pet_home");
if ($conn->connect_error) {
    die("connection failed: " . $conn->connect_error);
}

/*if (!isset($_SESSION['orgID'])) {
   if($_SERVER['REQUEST_METHOD']=='POST'){
    header('Content-Type: application/json');
    echo json_encode(['success'=> false, 'message'=> 'Unauthorized']);
    exit;
   }
   header("Location:../User_Login.php");
   exit;
}

$org_id = $_SESSION['orgID'];*/
$org_id = "ORG05"; // TEST
if($_SERVER['REQUEST_METHOD']==='POST'){
    header('Content-Type: application/json');
    $input    = json_decode(file_get_contents('php://input'), true);
    $action   = trim($input['action']   ?? '');
    $reportID = trim($input['reportID'] ?? '');
    $status   = trim($input['status']   ?? '');

    if($action !== 'update_status'){
        echo json_encode(['success' => false, 'message'=> 'Invalid action']);
        exit;
    }
    
    $allow = ['Pending', 'In Progress', 'Resolved'];
    if (empty($reportID) || !in_array($status, $allow)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $stmt= $conn -> prepare(" UPDATE report 
                              SET Status = ? WHERE ReportID = ? AND OrgID = ?");
    $stmt->bind_param("sss", $status, $reportID, $org_id);
    $stmt->execute();
 
    if($stmt -> affected_rows >0){
        echo json_encode(['success' => true]);
    }else{
         echo json_encode(['success' => false, 'message' => 'No record updated.']);
    }
    $stmt->close();
    $conn->close();
    exit;
}

/* FILTER TIME*/

$allowed = ['today', 'yesterday', 'this_week', 'this_month', 'this_year'];

$filter = $_GET['filter'] ?? 'today';
$filter = strtolower($filter);

if (!in_array($filter, $allowed)) {
    $filter = 'today';
}

$where = " WHERE r.OrgID = ? ";
$params = [$org_id];
$types = "s";

switch ($filter) {

    case 'yesterday':
        $where .= " AND DATE(i.DateTime) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        break;

    case 'this_week':
        $where .= " AND YEARWEEK(i.DateTime, 1) = YEARWEEK(CURDATE(), 1)";
        break;

    case 'this_month':
        $where .= " AND MONTH(i.DateTime) = MONTH(CURDATE())
                    AND YEAR(i.DateTime) = YEAR(CURDATE())";
        break;

    case 'this_year':
        $where .= " AND YEAR(i.DateTime) = YEAR(CURDATE())";
        break;

    default:
        $where .= " AND DATE(i.DateTime) = CURDATE()";
}

$sql = "
    SELECT
        r.ReportID,
        r.PetName,
        r.Location,
        r.Description,
        i.DateTime AS DateReported,
        r.Status,
        r.Photo
    FROM report r
    LEFT JOIN inbox i ON i.ReportID = r.ReportID
    $where
    ORDER BY i.DateTime DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGO Report</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/ngo_report.css">
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
            <div class="avatar" title="My Profile"> AT</div>
            </div>
        </div>

        <!-- NAVIGATION -->
        <div class="nav-links">
            <a href="../HomePage(registed).html" class="nav-tab"> Home</a>
            <a href="inbox.php" class="nav-tab"> Inbox</a>
            <a href="findapet.html" class="nav-tab"> Find A Pet</a>
            <a href="pet_community.html" class="nav-tab"> Pet Community</a>
            <a href="help_center.php" class="nav-tab"> Help Center</a>
            <a href="../Analytics.html" class="nav-tab"> Analytics</a>
            <a href="report.php" class="nav-tab"> Report</a>
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
                    <th>Report ID</th>
                    <th>Pet Name</th>
                    <th>Location</th>
                    <th>Date Reported</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="reportTbody">
            <?php if(empty($rows)){?>
                <tr class="empty-row">
                    <td colspan="6">No reports found for this period.</td>
                </tr>
            <?php } else {?>
                <?php foreach($rows as $row){
                
                $status = isset($row['Status']) ? $row['Status'] : 'Pending';

                if($status == 'Resolved')
                {
                    $badgeClass = 'badge_resolved';
                }
                elseif($status == 'In Progress')
                {
                    $badgeClass = 'badge_inprogress';
                }
                elseif($status == 'Submit'){
                    $badgeClass = 'badge_submit';
                }
                else
                {
                    $badgeClass = 'badge_pending';
                }

                if(isset($row['DateReported']))
                {
                    $dateFormatted = date("d/m/Y H:i", strtotime($row['DateReported']));
                }
                else
                {
                    $dateFormatted = '-';
                }
                ?>

        <tr id="row-<?php echo htmlspecialchars($row['ReportID']); ?>"
                    data-id="<?php echo htmlspecialchars($row['ReportID']); ?>"
                    data-pet="<?php echo htmlspecialchars($row['PetName']); ?>"
                    data-loc="<?php echo htmlspecialchars($row['Location']); ?>"
                    data-desc="<?php echo htmlspecialchars($row['Description']); ?>"
                    data-date="<?php echo htmlspecialchars($dateFormatted); ?>"
                    data-status="<?php echo htmlspecialchars($status); ?>"
                    data-photo="<?php echo htmlspecialchars($row['Photo'] ?? ''); ?>">
 
                    <td>
                        <?php echo htmlspecialchars($row['ReportID']); ?>
                    </td>
 
                    <td>
                        <?php echo htmlspecialchars($row['PetName']); ?>
                    </td>
 
                    <td>
                        <?php echo htmlspecialchars($row['Location']); ?>
                    </td>
 
                    <td>
                        <?php echo $dateFormatted; ?>
                    </td>
 
                    <td>
                       <span class="<?php echo $badgeClass; ?>" id="badge-<?php echo htmlspecialchars($row['ReportID']); ?>">
                        <?php echo htmlspecialchars($status); ?>
                        </span>
                    </td>
 

                   <td class="action-btns">

                        <button class="btn-solve"
                            onclick="updateStatus('<?php echo htmlspecialchars($row['ReportID']); ?>','Resolved')">
                            Solve
                        </button>

                        <button class="btn-inprogress"
                            onclick="updateStatus('<?php echo htmlspecialchars($row['ReportID']); ?>','Pending')">
                            In Progress
                        </button>

                        <button class="btn-view-report"
                            onclick="viewReport('<?php echo htmlspecialchars($row['ReportID']); ?>')">
                            View
                        </button>

                    </td>

                </tr>

            <?php } ?>
        <?php } ?>

            </tbody>

        </table>

        <div class="inbox-right" id="sidePanel">
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
