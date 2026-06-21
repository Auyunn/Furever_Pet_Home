<?php
session_start();
require_once '../db_connect.php';
$residentID = $_SESSION['residentID'] ?? null;

// --- Fetch reports + latest inbox update for each -----------------
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
    ORDER BY r.ReportID DESC
";

// FIXED 1: Isytiharkan nama pemboleh ubah plural $reports yang betul
$reports = [];
$stmt = null;

try {
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    // FIXED 2: Gunakan huruf besar 'S' yang konsisten untuk mapStatus
    $mapped = mapStatus($row);
    // Tip: Gunakan 'calculatedstatus' (huruf kecil semua) agar mudah dibaca di JavaScript nanti
    $row['calculatedstatus'] = $mapped;
    $reports[] = $row;
  }

} catch (Exception $e) {
  header("Location: error.php");
  exit();
} finally {
  // FIXED 4: Semakan keselamatan untuk mengelakkan ralat penutupan objek kosong
  if ($stmt !== null) {
    $stmt->close();
  }
}

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
  return (isset($row['ReportStatus']) && $row['ReportStatus'] === 'Resolved') ? 'resolved' : 'pending';
}

$pendingCount = 0;
$resolveCount = 0;
$submitCount = 0;

foreach ($reports as $rep) {
  if ($rep["ReportStatus"] === "Resolved") {
    $resolveCount++;
  } else if($rep["ReportStatus"] === "Pending") {
    $pendingCount++;
  } else{
    $submitCount++;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Report Management</title>
  <link rel="stylesheet" href="../css/ad_Report.css">
</head>

<body>
  <header class="top-header">
    <nav class="navbar" id="navbar">
      <div class="navbar-top">
        <a href="#" class="nav-logo">
          <img src="../image/icons/logo.png" alt="Furever Pet Home">
          <span>Furever Pet Home</span>
        </a>
        <div class="nav-right">
          <div class="avatar" title="My Profile" onclick="window.location.href='';">AT</div>
        </div>
      </div>

      <div class="nav-links">
        <a href="dashboard.php" class="nav-tab">Dashboard</a>
         <a href="usercount.html" class="nav-tab"> Users/NGOs</a>
        <a href="pet_communityadmin.php" class="nav-tab"> Pet Community</a>
        <a href="help_center.php" class="nav-tab"> Help Center</a>
        <a href="../Analytics.html" class="nav-tab"> Analytics</a>
        <a href="Report.html" class="nav-tab active"> Report</a>
      </div>
    </nav>
  </header>

  <div class="page">
    <h1 class="page-heading">REPORT</h1>
    <p class="page-sub">View and manage all incoming animal reports</p>

    <div class="summary-row">
      <div class="sum-card">
        <span class="sum-label">Total Reports</span>
        <span class="sum-value" id="count-total"><?php echo count($reports); ?></span>
      </div>

      <div class="sum-card open">
        <span class="sum-label">Pending</span>
        <span class="sum-value" id="count-open"><?php echo $pendingCount; ?></span>
      </div>

      <div class="sum-card">
        <span class="sum-label">Submit</span>
        <span class="sum-value" id="count-review"><?php echo "$submitCount"; ?></span>
      </div>

      <div class="sum-card solved">
        <span class="sum-label">Resolved</span>
        <span class="sum-value" id="count-resolved"><?php echo $resolveCount; ?></span>
      </div>
    </div>
    
    <div class="filter-bar">
      <input type="text" id="search-input" placeholder="search by name or title.....">
      <select id="status-filter">
        <option value="">All Status</option>
        <option value="pending">Open / Pending</option>
        <option value="reviewing">Under Review</option>
        <option value="resolved">Resolved</option>
      </select>
    </div>
    
    <div class="table-card">
      <table>
        <thead>
          <tr>
            <th>ReportID</th>
            <th>Location</th>
            <th>Status</th>
            <th>Date</th>
            <th>Report Description</th>
            <th>Inbox Message</th>
          </tr>
        </thead>
        <tbody id="report-tbody">
        </tbody>

        <?php foreach($reports as $r) : ?>
          <tr>
            <td><?php echo $r['ReportID']; ?></td>
            <td><?php echo $r['Location']?></td>
            <td><?php echo $r['ReportStatus']?></td>
            <td><?php echo $r['InboxDateTime']?></td>
            <td><?php echo $r['ReportDescription']?></td>
            <td><?php echo $r['InboxMessage']?></td>

          </tr>

        <?php endforeach; ?>
      </table>

      <div class="pagination">
        <span id="pagination-info">Showing 0-0 of 0</span>
        <div class="page-btns" id="page-btns"></div>
      </div>
    </div>
  </div>
  
  <div class="modal-overlay" id="delete-modal">
    <div class="modal">
      <p class="modal-title">Delete Report</p>
      <p class="modal-body">
        Are you sure you want to delete the report by <strong id="delete-name"></strong>? This action cannot be undone.
      </p>
      <div class="modal-actions">
        <button class="btn-modal-cancel" onclick="closeModal('delete-modal')">Cancel</button>
        <button class="btn-confirm-delete" id="confirm-delete-btn">Delete</button>
      </div>
    </div>
  </div>
  
  <div class="modal-overlay" id="view-modal">
    <div class="modal">
      <p class="modal-title">Report Detailed</p>

      <div class="Detailed-field">
        <div class="detailed-label">Name</div>
        <div class="detailed-value" id="view-name"></div>
      </div>

      <div class="Detailed-field">
        <div class="detailed-label">Title</div>
        <div class="detailed-value" id="view-title"></div>
      </div>

      <div class="Detailed-field">
        <div class="detailed-label">Status</div>
        <div class="detailed-value" id="view-status"></div>
      </div>

      <div class="Detailed-field">
        <div class="detailed-label">Location</div>
        <div class="detailed-value" id="view-location"></div>
      </div>

      <div class="Detailed-field">
        <div class="detailed-label">Description</div>
        <div class="detailed-value" id="view-desc"></div>
      </div>

      <div class="Detailed-field">
        <div class="detailed-label">Date Submitted</div>
        <div class="detailed-value" id="view-date"></div>
      </div>

      <div class="modal-actions">
        <button class="btn-modal-cancel" onclick="closeModal('view-modal')">Close</button>
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

  <div class="toast" id="toast"></div>

  <script>
    const serverReportsData = <?php echo json_encode($reports); ?>;
  </script>
  <script src="admin_report.js"></script>
</body>
</html>