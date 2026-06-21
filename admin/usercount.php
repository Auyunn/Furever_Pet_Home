<?php
session_start();
require_once "../db_connect.php"; // usercount.php is in /admin, db_connect.php is one level up in project root

/* ============================================================
   HANDLE FORM ACTIONS (update, delete) for resident & organization
   Runs before any HTML output, then redirects back to this page.
============================================================ */

// ---- UPDATE RESIDENT ----
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

// ---- DELETE RESIDENT (permanent: cascades through every table that
//      references this resident, in dependency order) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_resident') {
    $residentID = $_POST['ResidentID'];

    // Order matters - child tables must be cleared before the resident row
    // itself can be removed, and inbox must go before adopt_application/report
    // since inbox references those (even though inbox has ON DELETE CASCADE
    // already, deleting it explicitly here keeps the transaction order clear
    // and works the same either way).
    //
    // comment is self-referencing via ReplyID (a reply points to the comment
    // it replies to). Deleting all of this resident's comments could leave
    // OTHER residents' replies pointing at a now-deleted CommentID, which
    // would violate comment_ibfk_3. To handle this safely, we delete any
    // comments (from anyone) that reply to this resident's comments first,
    // then delete this resident's own comments.

    $conn->begin_transaction();
    try {
        // 1. Inbox entries tied to this resident's adoption applications or reports
        $stmt = $conn->prepare("
            DELETE inbox FROM inbox
            LEFT JOIN adopt_application ON inbox.AdoptionID = adopt_application.AdoptionID
            LEFT JOIN report ON inbox.ReportID = report.ReportID
            WHERE adopt_application.ResidentID = ? OR report.ResidentID = ?
        ");
        $stmt->bind_param("ss", $residentID, $residentID);
        $stmt->execute();
        $stmt->close();

        // 2. Adoption applications
        $stmt = $conn->prepare("DELETE FROM adopt_application WHERE ResidentID = ?");
        $stmt->bind_param("s", $residentID);
        $stmt->execute();
        $stmt->close();

        // 3. Reports filed by this resident
        $stmt = $conn->prepare("DELETE FROM report WHERE ResidentID = ?");
        $stmt->bind_param("s", $residentID);
        $stmt->execute();
        $stmt->close();

        // 4. Replies (by anyone) to this resident's own comments
        $stmt = $conn->prepare("
            DELETE c2 FROM comment c2
            INNER JOIN comment c1 ON c2.ReplyID = c1.CommentID
            WHERE c1.ResidentID = ?
        ");
        $stmt->bind_param("s", $residentID);
        $stmt->execute();
        $stmt->close();

        // 5. This resident's own comments
        $stmt = $conn->prepare("DELETE FROM comment WHERE ResidentID = ?");
        $stmt->bind_param("s", $residentID);
        $stmt->execute();
        $stmt->close();

        // 6. The resident row itself
        $stmt = $conn->prepare("DELETE FROM resident WHERE ResidentID = ?");
        $stmt->bind_param("s", $residentID);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        die("Delete failed - nothing was removed. Error: " . htmlspecialchars($e->getMessage()));
    }

    header("Location: usercount.php?tab=residents");
    exit();
}

// ---- UPDATE ORGANIZATION ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_org') {
    $orgID   = $_POST['OrgID'];
    $orgName = trim($_POST['OrgName']);
    $phone   = trim($_POST['NumberPhone']);
    $email   = trim($_POST['Email']);
    $status  = $_POST['Status']; // "1" or "0"

    $stmt = $conn->prepare("UPDATE organization SET OrgName = ?, NumberPhone = ?, Email = ?, Status = ? WHERE OrgID = ?");
    $stmt->bind_param("sssis", $orgName, $phone, $email, $status, $orgID);
    $stmt->execute();
    $stmt->close();

    header("Location: usercount.php?tab=ngos");
    exit();
}

// ---- DELETE ORGANIZATION (permanent: cascades through every table that
//      references this NGO, in dependency order) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_org') {
    $orgID = $_POST['OrgID'];

    // This NGO's pets may have adoption applications, which may have inbox
    // entries - all of that has to go before the pets themselves can be
    // deleted. Likewise, this NGO's community_board posts may have comments,
    // which have to go before the posts can be deleted.

    $conn->begin_transaction();
    try {
        // 1. Inbox entries for adoption applications on this NGO's pets
        $stmt = $conn->prepare("
            DELETE inbox FROM inbox
            INNER JOIN adopt_application ON inbox.AdoptionID = adopt_application.AdoptionID
            INNER JOIN pet ON adopt_application.PetID = pet.PetID
            WHERE pet.OrgID = ?
        ");
        $stmt->bind_param("s", $orgID);
        $stmt->execute();
        $stmt->close();

        // 2. Adoption applications for this NGO's pets
        $stmt = $conn->prepare("
            DELETE adopt_application FROM adopt_application
            INNER JOIN pet ON adopt_application.PetID = pet.PetID
            WHERE pet.OrgID = ?
        ");
        $stmt->bind_param("s", $orgID);
        $stmt->execute();
        $stmt->close();

        // 3. This NGO's pets
        $stmt = $conn->prepare("DELETE FROM pet WHERE OrgID = ?");
        $stmt->bind_param("s", $orgID);
        $stmt->execute();
        $stmt->close();

        // 4. Comments on this NGO's community board posts
        $stmt = $conn->prepare("
            DELETE comment FROM comment
            INNER JOIN community_board ON comment.BoardID = community_board.BoardID
            WHERE community_board.OrgID = ?
        ");
        $stmt->bind_param("s", $orgID);
        $stmt->execute();
        $stmt->close();

        // 5. This NGO's community board posts
        $stmt = $conn->prepare("DELETE FROM community_board WHERE OrgID = ?");
        $stmt->bind_param("s", $orgID);
        $stmt->execute();
        $stmt->close();

        // 6. This NGO's FAQs and guidelines (no further dependents)
        $stmt = $conn->prepare("DELETE FROM faq WHERE OrgID = ?");
        $stmt->bind_param("s", $orgID);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM guidelines WHERE OrgID = ?");
        $stmt->bind_param("s", $orgID);
        $stmt->execute();
        $stmt->close();

        // 7. Reports assigned to this NGO (report.OrgID also has a foreign key)
        $stmt = $conn->prepare("DELETE FROM report WHERE OrgID = ?");
        $stmt->bind_param("s", $orgID);
        $stmt->execute();
        $stmt->close();

        // 8. The organization row itself
        $stmt = $conn->prepare("DELETE FROM organization WHERE OrgID = ?");
        $stmt->bind_param("s", $orgID);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        die("Delete failed - nothing was removed. Error: " . htmlspecialchars($e->getMessage()));
    }

    header("Location: usercount.php?tab=ngos");
    exit();
}


/* ============================================================
   FETCH RESIDENTS
   resident columns: ResidentID, FirstName, LastName, NumberPhone,
                      Email, Password, Address, Status, Salary
   Shows all residents regardless of Status. Status can still be
   toggled Active/Inactive via the edit dropdown. Delete (below)
   is a real, permanent delete - separate from Status.
============================================================ */
$residents = [];
$result = $conn->query("SELECT ResidentID, FirstName, LastName, NumberPhone, Email, Status FROM resident");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
    }
}


/* ============================================================
   FETCH ORGANIZATIONS (NGOs)
   organization columns: OrgID, OrgName, NumberPhone, OrgAddress,
                          Email, Password, Description, Status
============================================================ */
$organizations = [];
$result = $conn->query("SELECT OrgID, OrgName, NumberPhone, Email, Status FROM organization");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $organizations[] = $row;
    }
}

// Which tab to show first on page load (after a redirect, e.g. ?tab=ngos)
$activeTab = isset($_GET['tab']) && $_GET['tab'] === 'ngos' ? 'ngos' : 'residents';

// All the small edit/delete forms are collected here as HTML strings and
// printed together at the very end of the page, completely outside the
// <table> elements. Browsers apply "foster parenting" rules to elements
// placed directly between/inside table rows that aren't valid table
// children (like <form>), which can silently relocate or break them.
// Keeping forms outside the table entirely avoids that.
$pendingForms = [];
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

<!-- Navbar -->
<nav class="navbar" id="navbar">
  <div class="navbar-top">
    <a href="#" class="nav-logo">
      <img src="../image/icons/logo.png" alt="Furever Pet Home">
      <span>Furever Pet Home</span>
    </a>
    <div class="nav-right">
      <div class="avatar" title="My Profile">A</div>
    </div>
  </div>
  <div class="nav-links">
    <a href="dashboard.php" class="nav-tab"> Dashboard</a>
                <a href="usercount.php" class="nav-tab"> Users/NGOs</a>
                <a href="Add_Report.php" class="nav-tab"> Report</a>
                <a href="analytics_admin.php" class="nav-tab"> Analytics</a>
                <a href="pet_communityadmin.php" class="nav-tab"> Pet Community</a>
                <a href="help_center.php" class="nav-tab"> Help Center</a> 
  </div>
</nav>

<!-- Kotak Tab Users | NGOs -->
<div class="tab-box">
  <button class="tab-btn <?php echo $activeTab === 'residents' ? 'active' : ''; ?>" onclick="showTable('residents')">Users</button>
  <button class="tab-btn <?php echo $activeTab === 'ngos' ? 'active' : ''; ?>" onclick="showTable('ngos')">NGOs</button>
</div>

<!-- Residents Table -->
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

          <?php
            // Queue this resident's forms for later - NOT printed here,
            // since "here" is between table rows (invalid form placement).
            ob_start();
          ?>
          <form id="<?php echo $editFormId; ?>" method="POST" action="usercount.php">
            <input type="hidden" name="action" value="update_resident">
            <input type="hidden" name="ResidentID" value="<?php echo htmlspecialchars($r['ResidentID']); ?>">
          </form>
          <form id="<?php echo $delFormId; ?>" method="POST" action="usercount.php" onsubmit="return confirmDelete('this resident, including all of their adoption applications, comments, and reports');">
            <input type="hidden" name="action" value="delete_resident">
            <input type="hidden" name="ResidentID" value="<?php echo htmlspecialchars($r['ResidentID']); ?>">
          </form>
          <?php $pendingForms[] = ob_get_clean(); ?>

          <tr id="<?php echo $rowId; ?>">
              <!-- VIEW MODE -->
              <td class="view-mode">
                <?php echo htmlspecialchars($r['FirstName'] . ' ' . $r['LastName']); ?>
              </td>
              <td class="view-mode"><?php echo htmlspecialchars($r['NumberPhone']); ?></td>
              <td class="view-mode"><?php echo htmlspecialchars($r['Email']); ?></td>
              <td class="view-mode">
                <?php echo ((int)$r['Status'] === 1) ? 'Active' : 'Inactive'; ?>
              </td>

              <!-- EDIT MODE (hidden until pencil icon clicked) -->
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

          <?php ob_start(); ?>
          <form id="<?php echo $editFormId; ?>" method="POST" action="usercount.php">
            <input type="hidden" name="action" value="update_org">
            <input type="hidden" name="OrgID" value="<?php echo htmlspecialchars($o['OrgID']); ?>">
          </form>
          <form id="<?php echo $delFormId; ?>" method="POST" action="usercount.php" onsubmit="return confirmDelete('this NGO, including all of their posts, FAQs, guidelines, pets, and reports');">
            <input type="hidden" name="action" value="delete_org">
            <input type="hidden" name="OrgID" value="<?php echo htmlspecialchars($o['OrgID']); ?>">
          </form>
          <?php $pendingForms[] = ob_get_clean(); ?>

          <tr id="<?php echo $rowId; ?>">
              <!-- VIEW MODE -->
              <td class="view-mode"><?php echo htmlspecialchars($o['OrgName']); ?></td>
              <td class="view-mode"><?php echo htmlspecialchars($o['NumberPhone']); ?></td>
              <td class="view-mode"><?php echo htmlspecialchars($o['Email']); ?></td>
              <td class="view-mode">
                <?php echo ((int)$o['Status'] === 1) ? 'Active' : 'Inactive'; ?>
              </td>

              <!-- EDIT MODE -->
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
                <select name="Status" form="<?php echo $editFormId; ?>">
                  <option value="1" <?php echo ((int)$o['Status'] === 1) ? 'selected' : ''; ?>>Active</option>
                  <option value="0" <?php echo ((int)$o['Status'] === 0) ? 'selected' : ''; ?>>Inactive</option>
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
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="usercount.php">Users/NGOs</a></li>
                    <li><a href="Add_Report.php">Report</a></li>
                    <li><a href="analytics_admin.php">Analytics</a></li>
                    <li><a href="pet_communityadmin.php">Pet Community</a></li>
                    <li><a href="help_center.php">Help Center</a></li>
                </ul>
            </div>
            <div>
                  <p class="footer-col-title">Contact</p>
                  <ul class="footer-links-list">
                  <li>41700 Bandar Klang, Selangor</li>
                  <li>info@fureverpethome.com</li>
                  <li>+6012-456 7890</li>
                  <li>Facebook · Instagram · X</li>
                  </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2026 Furever Pet Home — Urban Pet Adoption & Community Management</span>
            <span>Made with ❤️ for Bandar Klang</span>
        </div>
    </footer>


<!-- All edit/delete forms for every row, printed here outside any table.
     Buttons inside the tables reference these by id via the form="" attribute. -->
<?php foreach ($pendingForms as $formHtml) { echo $formHtml; } ?>

<script src="../js/usercount.js"></script>
</body>
</html>
