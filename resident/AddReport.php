<?php 
session_start(); 

require_once '../db_connect.php';

// Pastikan dah login
if (empty($_SESSION['residentID'])) {
    header("Location: ../User_Login.php");
    exit();
}
$residentID = $_SESSION['residentID'];

// Fetch nama untuk avatar initials
$stmtAvatar = $conn->prepare("SELECT FirstName, LastName FROM resident WHERE ResidentID = ?");
$stmtAvatar->bind_param('s', $residentID);
$stmtAvatar->execute();
$residentRow = $stmtAvatar->get_result()->fetch_assoc();
$stmtAvatar->close();

$firstName = $residentRow['FirstName'] ?? '';
$lastName  = $residentRow['LastName'] ?? '';
$avatarInitials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));

if (empty($_SESSION['residentID'])) {
    header('Location: ../User_Login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ms">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Tambah Laporan Baru - Pet Community">
  <title>Add Report - Pet Community</title>
  <link rel="stylesheet" href="../css/base.css"> 
  <link rel="stylesheet" href="../css/Report.css">
</head>

<body>

  <h1 class="page-title">REPORT</h1>

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
            <a href="../HomePage(registed).php" class="nav-tab">Home</a>
            <a href="inbox.php" class="nav-tab">Inbox</a>
            <a href="findapet.php" class="nav-tab">Find A Pet</a>
            <a href="pet_community.php" class="nav-tab">Pet Community</a>
            <a href="help_center.php" class="nav-tab">Help Center</a>
            <a href="Analytics.php" class="nav-tab">Analytics</a>
            <a href="Report.php" class="nav-tab active">Report</a>
        </div>
    </nav>

    <main>

      <?php if (!empty($_SESSION['report_errors'])): ?>
        <div class="form-errors" style="background:#fde2e2;color:#a33;padding:1rem;border-radius:10px;margin-bottom:1rem;">
          <ul style="margin:0;padding-left:1.2rem;">
            <?php foreach ($_SESSION['report_errors'] as $err): ?>
              <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php unset($_SESSION['report_errors']); ?>
      <?php endif; ?>

      <?php if (!empty($_SESSION['report_success'])): ?>
        <div class="form-success" style="background:#e2f7e2;color:#2a7a2a;padding:1rem;border-radius:10px;margin-bottom:1rem;">
          <?php echo htmlspecialchars($_SESSION['report_success']); ?>
        </div>
        <?php unset($_SESSION['report_success']); ?>
      <?php endif; ?>

      <?php $old = $_SESSION['report_old'] ?? []; unset($_SESSION['report_old']); ?>

      <form action="add_report_process.php" method="POST" enctype="multipart/form-data">
        
        <figure class="image-upload" aria-label="Muat Naik Gambar">
          <input type="file" id="reportPhoto" name="reportPhoto" accept="image/*" aria-label="Pilih Gambar">
        </figure>

        <section class="form-section">
          <fieldset>
            <legend></legend>
            <div>
              <label for="reportName">Name</label>
              <input type="text" id="reportName" name="reportName" placeholder="Masukkan nama"
                value="<?php echo htmlspecialchars($old['reportName'] ?? ''); ?>" required>
            </div>

            <div>
              <label for="reportDesc">Description</label>
              <textarea id="reportDesc" name="reportDesc" placeholder="Masukkan penerangan..." required><?php echo htmlspecialchars($old['reportDesc'] ?? ''); ?></textarea>
            </div>

            <div>
              <label for="reportLocation">Location</label>
              <input type="text" id="reportLocation" name="reportLocation" placeholder="Masukkan lokasi"
                value="<?php echo htmlspecialchars($old['reportLocation'] ?? ''); ?>" required>
            </div>
          </fieldset>

          <div class="action-buttons">
            <button type="submit" class="btn-begin">Add</button>
            <button type="button" class="btn-cancel" onclick="window.location.href='Report.php'">Cancel</button>
          </div>

        </section>
      </form>
    </main>

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
  </div>
</body>

</html>
