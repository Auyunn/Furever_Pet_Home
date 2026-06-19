<?php
session_start();
require_once "../db_connect.php"; // addboard.php is in /ngo, db_connect.php is one level up in project root

/* ============================================================
   CURRENT NGO (logged-in organization)
   Same temporary fallback used in petcommunity.php.
   TODO: remove this once login sets $_SESSION['org_id'] for real.
============================================================ */
if (!isset($_SESSION['org_id'])) {
    $_SESSION['org_id'] = "ORG01"; // TEMP placeholder - remove after login is built
}
$currentOrgID = $_SESSION['org_id'];

$errors = [];

/* ============================================================
   HANDLE FORM SUBMISSION
============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['desc'] ?? '');

    if ($title === '') {
        $errors[] = "Title is required.";
    }
    if ($content === '') {
        $errors[] = "Description is required.";
    }

    // ---------------------------------------------------------
    // Handle the optional photo upload
    // ---------------------------------------------------------
    $photoFilename = null;
    if (!empty($_FILES['photo']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $fileType     = $_FILES['photo']['type'];
        $fileTmpPath  = $_FILES['photo']['tmp_name'];
        $fileError    = $_FILES['photo']['error'];

        if ($fileError !== UPLOAD_ERR_OK) {
            $errors[] = "There was a problem uploading the image.";
        } elseif (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Image must be a JPG, PNG, WEBP, or GIF file.";
        } else {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            // unique filename so two NGOs uploading "photo.jpg" never collide
            $photoFilename = 'board_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

            // NOTE: adjust this folder if your community photos live elsewhere
            $uploadPath = "../image/pet_community/" . $photoFilename;

            if (!move_uploaded_file($fileTmpPath, $uploadPath)) {
                $errors[] = "Failed to save the uploaded image.";
                $photoFilename = null;
            }
        }
    }

    // ---------------------------------------------------------
    // Insert into community_board if no errors
    // ---------------------------------------------------------
    if (empty($errors)) {
        // Generate the next BoardID, e.g. BOARD01, BOARD02...
        // NOTE: adjust the prefix/substring length below to match
        // your actual BoardID format if it differs from "BOARD##"
        $result_max = $conn->query("SELECT MAX(CAST(SUBSTRING(BoardID, 6) AS UNSIGNED)) AS maxNum FROM community_board");
        $row_max    = $result_max->fetch_assoc();
        $nextNum    = ($row_max['maxNum'] !== NULL) ? $row_max['maxNum'] + 1 : 1;
        $boardID    = "BOARD" . str_pad($nextNum, 2, "0", STR_PAD_LEFT);

        $stmt = $conn->prepare("INSERT INTO community_board (BoardID, OrgID, Title, Content, Photo, Date) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $boardID, $currentOrgID, $title, $content, $photoFilename);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: petcommunity.php");
            exit();
        } else {
            $errors[] = "Database error: " . $stmt->error;
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Post | Furever Pet Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/addboard.css">
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
      <button class="notif-btn" title="Notifications" onclick="window.location.href='inbox.php';">🔔<span class="notif-dot"></span></button>
      <div class="avatar" title="My Profile"><?= htmlspecialchars(strtoupper(substr($currentOrgID, 0, 2))) ?></div>
    </div>
  </div>
  <div class="nav-links">
    <a href="dashboard.php" class="nav-tab"> Home</a>
    <a href="inbox.php" class="nav-tab"> Inbox</a>
    <a href="../findapet.php" class="nav-tab"> Find A Pet</a>
    <a href="petcommunity.php" class="nav-tab active"> Pet Community</a>
    <a href="helpcenter_ngo.php" class="nav-tab"> Help Center</a>
    <a href="../Analytics.html" class="nav-tab"> Analytics</a>
  </div>
</nav>

<!-- Add Post Form -->
<div class="wrapper">
<div class="add-container">

    <?php if (!empty($errors)): ?>
        <div class="form-errors">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="preview">
        <p>Image Preview</p>
        <input type="file" id="imageUpload" name="photo" form="addPostForm" accept="image/*" onchange="previewImage(event)">
        <img id="previewImg" alt="Preview" />
    </div>

    <form id="addPostForm" method="POST" action="addboard.php" enctype="multipart/form-data" class="form-section">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>

        <label for="desc">Description</label>
        <textarea id="desc" name="desc" required><?= htmlspecialchars($_POST['desc'] ?? '') ?></textarea>

        <label>
            <input type="checkbox" id="enableComment" name="enableComment" checked>
            Enable Comment
        </label>

        <div class="buttons">
            <button type="reset">Reset Form</button>
            <button type="submit">Post Pet</button>
            <button type="button" onclick="goBack()">Cancel</button>
        </div>
    </form>

</div>
</div>

<!-- Footer -->
<footer>
  <div class="footer-bottom">
    <span>©️ 2026 Furever Pet Home — Urban Pet Adoption & Community Management</span>
    <span>Made with ❤️ for Bandar Klang</span>
  </div>
</footer>

<script src="../js/addboardngo.js"></script>
</body>
</html>