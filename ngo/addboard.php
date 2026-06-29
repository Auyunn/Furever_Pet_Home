<?php
session_start();
require_once "../db_connect.php"; 

if (!isset($_SESSION['org_id'])) {
    $_SESSION['org_id'] = "ORG01"; 
}
$currentOrgID = $_SESSION['org_id'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['desc'] ?? '');

    if ($title === '') {
        $errors[] = "Title is required.";
    }
    if ($content === '') {
        $errors[] = "Description is required.";
    }

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
            $photoFilename = 'board_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

            $uploadPath = "../image/pet_community/" . $photoFilename;

            if (!move_uploaded_file($fileTmpPath, $uploadPath)) {
                $errors[] = "Failed to save the uploaded image.";
                $photoFilename = null;
            }
        }
    }

    if (empty($errors)) {

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

    <nav class="navbar" id="navbar">
    <div class="navbar-top">
        <a href="#" class="nav-logo">
        <img src="../image/icons/logo.png" alt="Furever Pet Home">
        <span>Furever Pet Home</span>
        </a>
        <div class="nav-right">
        <button class="notif-btn" title="Notifications" onclick="window.location.href='inbox.php';">🔔<span class="notif-dot"></span></button>
        <div class="avatar" title="My Profile" onclick="window.location.href='profile.php';">
            <?= htmlspecialchars(strtoupper(substr($currentOrgID, 0, 2))) ?>
        </div>
        </div>
    </div>

    <!---navigation bar-->
    <div class="nav-links">
            <a href="Pet_listing.php" class="nav-tab"> Home</a>
            <a href="inbox.php" class="nav-tab"> Inbox</a>
            <a href="findapet.html" class="nav-tab"> Find A Pet</a>
            <a href="pet_community.html" class="nav-tab"> Pet Community</a>
            <a href="helpcenter_ngo.php" class="nav-tab"> Help Center</a>
            <a href="Analytics.html" class="nav-tab"> Analytics</a>
            <a href="report..php" class="nav-tab"> Report</a>
    </div>
    </nav>

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
                <button type="button" onclick="window.location.href='petcommunity.php';">Cancel</button>
            </div>
        </form>

    </div>
    </div><!--/wrapper-->

    <!--footer-->
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
                    <li><a href="Pet_listing.php">Home</a></li>
                    <li><a href="inbox.php">Inbox</a></li>
                    <li><a href="findapet.php">Find A Pet</a></li>
                    <li><a href="petcommunity.php">Pet Community</a></li>
                    <li><a href="Analytics.php">Analytics</a></li>
                    <li><a href="Report.php">Report Animal</a></li>
                </ul>
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

<script src="../js/addboardngo.js"></script>
</body>
</html>
