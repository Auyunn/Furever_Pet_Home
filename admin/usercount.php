<?php
session_start();
require_once "../db_connect.php"; 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_resident') {
    $residentID = $_POST['ResidentID'];
    $firstName  = trim($_POST['FirstName']);
    $lastName   = trim($_POST['LastName']);
    $phone      = trim($_POST['NumberPhone']);
    $email      = trim($_POST['Email']);
    $status     = $_POST['Status']; // "1" or "0"

    $stmt = $conn->prepare("UPDATE resident SET FirstName = ?, LastName = ?, NumberPhone = ?, Email = ?, Status = ? WHERE ResidentID = ?");
    $stmt->bind_param("sssssi", $firstName, $lastName, $phone, $email, $status, $residentID);
    $stmt->execute();
    $stmt->close();

    header("Location: usercount.php?tab=residents");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_resident') {
    $residentID = $_POST['ResidentID'];

    $stmt = $conn->prepare("DELETE FROM resident WHERE ResidentID = ?");
    $stmt->bind_param("s", $residentID);
    $stmt->execute();
    $stmt->close();

    header("Location: usercount.php?tab=residents");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_org') {
    $orgID   = $_POST['OrgID'];
    $orgName = trim($_POST['OrgName']);
    $phone   = trim($_POST['NumberPhone']);
    $email   = trim($_POST['Email']);

    // NOTE: organization table has no Status column - not saved here.
    // See comment near the NGO table below for details.
    $stmt = $conn->prepare("UPDATE organization SET OrgName = ?, NumberPhone = ?, Email = ? WHERE OrgID = ?");
    $stmt->bind_param("ssss", $orgName, $phone, $email, $orgID);
    $stmt->execute();
    $stmt->close();

    header("Location: usercount.php?tab=ngos");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_org') {
    $orgID = $_POST['OrgID'];

    $stmt = $conn->prepare("DELETE FROM organization WHERE OrgID = ?");
    $stmt->bind_param("s", $orgID);
    $stmt->execute();
    $stmt->close();

    header("Location: usercount.php?tab=ngos");
    exit();
}

$residents = [];
$result = $conn->query("SELECT ResidentID, FirstName, LastName, NumberPhone, Email, Status FROM resident");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
    }
}

$organizations = [];
$result = $conn->query("SELECT OrgID, OrgName, NumberPhone, Email FROM organization");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $organizations[] = $row;
    }
}

$activeTab = isset($_GET['tab']) && $_GET['tab'] === 'ngos' ? 'ngos' : 'residents';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Users/NGOs</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/usercount.css">
</head>
<body>

<!-- navigation bar -->
<nav class="navbar" id="navbar">
  <div class="navbar-top">
    <a href="#" class="nav-logo">
      <img src="../image/icons/logo.png" alt="Furever Pet Home">
      <span>Furever Pet Home</span>
    </a>
    <div class="nav-right">
      <button class="notif-btn" title="Notifications" onclick="window.location.href='resident/inbox.php';">🔔<span class="notif-dot"></span></button>
      <div class="avatar" title="My Profile">AT</div>
    </div>
  </div>
  <div class="nav-links">
    <a href="dashboard.php" class="nav-tab">🏠 Dashboard</a>
    <a href="usercount.php" class="nav-tab active">✉️ Users/NGOs</a>
    <a href="report.html" class="nav-tab">🚨 Report</a>
    <a href="../Analytics.html" class="nav-tab">📊 Analytics</a>
    <a href="pet_communityadmin.html" class="nav-tab">🐾 Pet Community</a>
    <a href="help_center.html" class="nav-tab">❓ Help Center</a>
  </div>
</nav>

<!--Users | NGOs -->
<div class="tab-box">
  <button class="tab-btn <?php echo $activeTab === 'residents' ? 'active' : ''; ?>" onclick="showTable('residents')">Users</button>
  <button class="tab-btn <?php echo $activeTab === 'ngos' ? 'active' : ''; ?>" onclick="showTable('ngos')">NGOs</button>
</div>

<!-- resident table -->
<div id="residents" class="table-section" style="<?php echo $activeTab === 'residents' ? '' : 'display:none;'; ?>">
  <h2>Resident List</h2>
  <table class="admin-table">
    <thead>
      <tr>
        <th>User Name</th>
        <th>Mobile</th>
        <th>Email</th>
        <th>Status</th>
        <th>Operation</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($residents)): ?>
        <tr><td colspan="6">No residents found.</td></tr>
      <?php else: ?>
        <?php foreach ($residents as $r): ?>
          <?php
            $rowId = "resident-" . htmlspecialchars($r['ResidentID']);
            $editFormId = "editform-" . htmlspecialchars($r['ResidentID']);
            $delFormId  = "delform-" . htmlspecialchars($r['ResidentID']);
          ?>

          <form id="<?php echo $editFormId; ?>" method="POST" action="usercount.php">
            <input type="hidden" name="action" value="update_resident">
            <input type="hidden" name="ResidentID" value="<?php echo htmlspecialchars($r['ResidentID']); ?>">
          </form>
          <form id="<?php echo $delFormId; ?>" method="POST" action="usercount.php" onsubmit="return confirmDelete('this resident account');">
            <input type="hidden" name="action" value="delete_resident">
            <input type="hidden" name="ResidentID" value="<?php echo htmlspecialchars($r['ResidentID']); ?>">
          </form>

          <tr id="<?php echo $rowId; ?>">
              <td class="view-mode">
                <?php echo htmlspecialchars($r['FirstName'] . ' ' . $r['LastName']); ?>
              </td>
              <td class="view-mode"><?php echo htmlspecialchars($r['NumberPhone']); ?></td>
              <td class="view-mode"><?php echo htmlspecialchars($r['Email']); ?></td>
              <td class="view-mode">
                <?php echo ((int)$r['Status'] === 1) ? 'Active' : 'Inactive'; ?>
              </td>

              <!-- EDIT MODE (hidden inputs shown via JS when pencil clicked) -->
              <td class="edit-mode" style="display:none;">
                <input type="text" name="FirstName" form="<?php echo $editFormId; ?>" value="<?php echo htmlspecialchars($r['FirstName']); ?>" size="8">
                <input type="text" name="LastName" form="<?php echo $editFormId; ?>" value="<?php echo htmlspecialchars($r['LastName']); ?>" size="8">
              </td>
              <td class="edit-mode" style="display:none;">
                <input type="text" name="NumberPhone" form="<?php echo $editFormId; ?>" value="<?php echo htmlspecialchars($r['NumberPhone']); ?>">
              </td>
              <td class="edit-mode" style="display:none;">
                <input type="email" name="Email" form="<?php echo $editFormId; ?>" value="<?php echo htmlspecialchars($r['Email']); ?>">
              </td>
              <td class="edit-mode" style="display:none;">
                <select name="Status" form="<?php echo $editFormId; ?>">
                  <option value="1" <?php echo ((int)$r['Status'] === 1) ? 'selected' : ''; ?>>Active</option>
                  <option value="0" <?php echo ((int)$r['Status'] === 0) ? 'selected' : ''; ?>>Inactive</option>
                </select>
              </td>

              <td>
                <button type="button" onclick="editAccount('<?php echo $rowId; ?>')">✏️</button>
              </td>
              <td>
                <button type="submit" form="<?php echo $editFormId; ?>" class="save-btn edit-mode" style="display:none;">💾 Save</button>
                <button type="submit" form="<?php echo $delFormId; ?>">🗑️</button>
              </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- NGOs Table -->
<div id="ngos" class="table-section" style="<?php echo $activeTab === 'ngos' ? '' : 'display:none;'; ?>">
  <h2>NGO List</h2>
  <table class="admin-table">
    <thead>
      <tr>
        <th>Organization Name</th>
        <th>Mobile</th>
        <th>Email</th>
        <th>Status</th>
        <th>Operation</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($organizations)): ?>
        <tr><td colspan="6">No NGOs found.</td></tr>
      <?php else: ?>
        <?php foreach ($organizations as $o): ?>
          <?php
            $rowId = "ngo-" . htmlspecialchars($o['OrgID']);
            $editFormId = "editform-" . htmlspecialchars($o['OrgID']);
            $delFormId  = "delform-" . htmlspecialchars($o['OrgID']);
          ?>

          <form id="<?php echo $editFormId; ?>" method="POST" action="usercount.php">
            <input type="hidden" name="action" value="update_org">
            <input type="hidden" name="OrgID" value="<?php echo htmlspecialchars($o['OrgID']); ?>">
          </form>
          <form id="<?php echo $delFormId; ?>" method="POST" action="usercount.php" onsubmit="return confirmDelete('this NGO account');">
            <input type="hidden" name="action" value="delete_org">
            <input type="hidden" name="OrgID" value="<?php echo htmlspecialchars($o['OrgID']); ?>">
          </form>

          <tr id="<?php echo $rowId; ?>">
              <!-- VIEW MODE -->
              <td class="view-mode"><?php echo htmlspecialchars($o['OrgName']); ?></td>
              <td class="view-mode"><?php echo htmlspecialchars($o['NumberPhone']); ?></td>
              <td class="view-mode"><?php echo htmlspecialchars($o['Email']); ?></td>
              <td class="view-mode">
                Active
              </td>

              <td class="edit-mode" style="display:none;">
                <input type="text" name="OrgName" form="<?php echo $editFormId; ?>" value="<?php echo htmlspecialchars($o['OrgName']); ?>">
              </td>
              <td class="edit-mode" style="display:none;">
                <input type="text" name="NumberPhone" form="<?php echo $editFormId; ?>" value="<?php echo htmlspecialchars($o['NumberPhone']); ?>">
              </td>
              <td class="edit-mode" style="display:none;">
                <input type="email" name="Email" form="<?php echo $editFormId; ?>" value="<?php echo htmlspecialchars($o['Email']); ?>">
              </td>
              <td class="edit-mode" style="display:none;">
              </td>

              <td>
                <button type="button" onclick="editAccount('<?php echo $rowId; ?>')">✏️</button>
              </td>
              <td>
                <button type="submit" form="<?php echo $editFormId; ?>" class="save-btn edit-mode" style="display:none;">💾 Save</button>
                <button type="submit" form="<?php echo $delFormId; ?>">🗑️</button>
              </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

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
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Report Animal</a></li>
        <li><a href="#">Analytics</a></li>
        <li><a href="#">Pet Community</a></li>
        <li><a href="#">Help Center</a></li>
      </ul>
    </div>
    <div>
      <p class="footer-col-title">Account</p>
      <ul class="footer-links-list">
        <li><a href="#">My Profile</a></li>
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
    <span>©️ 2026 Furever Pet Home — Urban Pet Adoption & Community Management</span>
    <span>Made with ❤️ for Bandar Klang</span>
  </div>
</footer>

<script src="../js/usercount.js"></script>
</body>
</html>