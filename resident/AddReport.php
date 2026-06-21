<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ms">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Tambah Laporan Baru - Pet Community">
  <title>Add Report - Pet Community</title>
  <link rel="stylesheet" href="../css/Report.css">

</head>

<body>

  <!-- Tajuk halaman -->
  <h1 class="page-title">REPORT</h1>

  <div class="wrapper">

    <nav class="navbar" id="navbar">
      <!--logo and profile-->
      <div class="navbar-top">
        <a href="#" class="nav-logo">
          <img src="../image/icons/logo.png" alt="Furever Pet Home">
          <span>Furever Pet Home</span>
        </a>
        <div class="nav-right">
          <button class="notif-btn" title="Notifications" onclick="window.location.href='inbox.php';">🔔<span
              class="notif-dot"></span></button>
          <div class="avatar" title="My Profile">AT</div>
        </div>
      </div>

      <!---Tab Navigation-->
      <div class="nav-links">
        <a href="../HomePage(registed).php" class="nav-tab">🏠 Home</a>
        <a href="inbox.php" class="nav-tab">✉️ Inbox</a>
        <a href="findapet.php" class="nav-tab">🔍 Find A Pet</a>
        <a href="pet_community.php" class="nav-tab"> 🐾Pet Community</a>
        <a href="help_center.php" class="nav-tab">❓ Help Center</a>
        <a href="../Analytics.html" class="nav-tab">📊 Analytics</a>
        <a href="Report.php" class="nav-tab">🚨 Report</a>
      </div>
    </nav>

    <!-- Main Content -->
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

      <form action="add_report_process.php" method="POST">
        <!-- Kotak Gambar Upload -->
        <figure class="image-upload" aria-label="Muat Naik Gambar">
          <input type="file" accept="image/*" aria-label="Pilih Gambar">
        </figure>

        <!-- Form -->
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

          <!-- Butang Tindakan -->
          <div class="action-buttons">
            <button type="submit" class="btn-begin">Add</button>
            <button type="button" class="btn-cancel" onclick="window.location.href='AddReport.php'">Cancel</button>
        
          </div>

        </section>
      </form>
    </main>

    <!-- Footer -->
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