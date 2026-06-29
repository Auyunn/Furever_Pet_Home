<?php
session_start();
include("../db_connect.php");

// Selesaikan ralat Undefined Variable untuk filter carian tanpa ubah UI
$selectedType = $_REQUEST['pet_type'] ?? '';
$selectedOrg  = $_REQUEST['shelter']  ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_pet_id'])) {

    $conn_direct = new mysqli("localhost", "root", "", "furever_pet_home");
    if ($conn_direct->connect_error) {
        die("Direct Connection Failed: " . $conn_direct->connect_error);
    }

    // 2. Semak session residentID 
    if (!isset($_SESSION['residentID'])) {
        die("Ralat: Kau tak login lagi bro! Session residentID tak wujud.");
    }

    $residentID = $_SESSION['residentID'];
    $petID      = $_POST['apply_pet_id'];

    // 3. Elakkan Duplicate Application
    $checkStmt = $conn_direct->prepare("SELECT AdoptionID FROM adopt_application WHERE ResidentID = ? AND PetID = ? AND Status IN ('Pending','Approved','Submit')");
    $checkStmt->bind_param("ss", $residentID, $petID);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        header("Location: findapet.php?already_applied=1");
        exit;
    }
    $checkStmt->close();

    // 4. JANA ADOPTIONID BARU 
    $result = $conn_direct->query("SELECT AdoptionID FROM adopt_application WHERE AdoptionID LIKE 'ADOP%' ORDER BY AdoptionID DESC LIMIT 1");
    $nextNum = 1;
    if ($result && $result->num_rows > 0) {
        $lastID = $result->fetch_assoc()['AdoptionID'];
        if (preg_match('/(\d+)$/', $lastID, $match)) {
            $nextNum = ((int)$match[1]) + 1;
        }
    }
    $newAdoptionID = "ADOP" . str_pad($nextNum, 2, "0", STR_PAD_LEFT);

    // INSERT INTO ADOPT_APPLICATION DAHULU
    $insertApp = $conn_direct->prepare("INSERT INTO adopt_application (AdoptionID, ResidentID, PetID, Status, Reason, RequestDate) VALUES (?, ?, ?, 'Pending', NULL, NOW())");
    $insertApp->bind_param("sss", $newAdoptionID, $residentID, $petID);
    
    if (!$insertApp->execute()) {
        die("Database Error dekat Table adopt_application: " . $insertApp->error);
    }
    $insertApp->close();

    // JANA INBOXID BARU 
    $inboxResult = $conn_direct->query("SELECT InboxID FROM inbox WHERE InboxID LIKE 'INB%' ORDER BY InboxID DESC LIMIT 1");
    $nextInboxNum = 1;
    if ($inboxResult && $inboxResult->num_rows > 0) {
        $lastInboxID = $inboxResult->fetch_assoc()['InboxID'];
        if (preg_match('/(\d+)$/', $lastInboxID, $match)) {
            $nextInboxNum = ((int)$match[1]) + 1;
        }
    }
    $newInboxID = "INB" . str_pad($nextInboxNum, 2, "0", STR_PAD_LEFT);

    // INSERT INTO INBOX 
    $title = "Application Submitted";
    $message = "Your pet adoption application has been submitted and is pending review.";

    $insertInbox = $conn_direct->prepare("INSERT INTO inbox (InboxID, AdoptionID, ReportID, Title, Message, DateTime, Type, Status) VALUES (?, ?, NULL, ?, ?, NOW(), 'Pet Adoption Application', 'Pending')");
    $insertInbox->bind_param("ssss", $newInboxID, $newAdoptionID, $title, $message);

    if (!$insertInbox->execute()) {
        die("Database Error dekat Table inbox: " . $insertInbox->error);
    }
    $insertInbox->close();
    $conn_direct->close();

    // Sukses! Redirect balik
    header("Location: findapet.php?applied=1");
    exit;
}

// Mengambil data senarai haiwan untuk dipaparkan
$sql = "SELECT p.PetID, p.PetName, p.PetType, p.Breed, p.Age, p.Location, p.Photo, p.Gender, o.OrgName
        FROM pet p
        LEFT JOIN organization o ON p.OrgID = o.OrgID
        WHERE p.IsAvailable = 1";

$types  = "";
$params = [];

if ($selectedType !== '') {
    $sql .= " AND p.PetType = ?";
    $types .= "s";
    $params[] = $selectedType;
}
if ($selectedOrg !== '') {
    $sql .= " AND p.OrgID = ?";
    $types .= "s";
    $params[] = $selectedOrg;
}
$sql .= " ORDER BY p.PetName ASC";

$stmt = $conn->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$pets = [];
while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
}
$stmt->close();

$petTypes = [];
$typeResult = $conn->query("SELECT DISTINCT PetType FROM pet ORDER BY PetType");
while ($row = $typeResult->fetch_assoc()) {
    $petTypes[] = $row['PetType'];
}

$shelters = [];
$shelterResult = $conn->query("SELECT OrgID, OrgName FROM organization ORDER BY OrgName");
while ($row = $shelterResult->fetch_assoc()) {
    $shelters[] = $row;
}

// Ambil rekod permohonan sedia ada untuk status butang
$myApplications = [];
if (isset($_SESSION['residentID'])) {
    $myAppStmt = $conn->prepare("SELECT PetID, Status FROM adopt_application WHERE ResidentID = ?");
    $myAppStmt->bind_param("s", $_SESSION['residentID']);
    $myAppStmt->execute();
    $myAppResult = $myAppStmt->get_result();
    while ($row = $myAppResult->fetch_assoc()) {
        $myApplications[$row['PetID']] = $row['Status'];
    }
    $myAppStmt->close();
}

$photoFolder = "../image/pets/";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title> Find A Pet </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/base.css">
        <link rel="stylesheet" href="../css/findapet.css">
    </head>
    <body>
        <nav class="navbar" id="navbar">
            <div class="navbar-top">
                <a href="#" class="nav-logo">
                    <img src="../image/icons/logo.png" alt="Furever Pet Home">
                    <span>Furever Pet Home</span>
                </a>
               <div class="profile-dropdown">
                    <div class="avatar" title="My Profile" onclick="toggleProfileDropdown()" style="cursor:pointer;">
                        <?= isset($_SESSION['username']) ? htmlspecialchars(strtoupper(substr($_SESSION['username'], 0, 2))) : 'AT' ?>
                    </div>
                    <div class="dropdown-menu" id="profileDropdown">
                        <div class="dropdown-user-info">
                            <strong><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></strong>
                            <span>Resident Account</span>
                        </div>
                        <form method="post" action="../logout.php" style="margin:0;">
                            <button type="submit" class="logout-btn">🔒 Log Out</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="nav-links">
                <a href="HomePage(registed).php" class="nav-tab">Home</a>
                <a href="inbox.php" class="nav-tab">Inbox</a>
                <a href="findapet.php" class="nav-tab">Find A Pet</a>
                <a href="pet_community.php" class="nav-tab">Pet Community</a>
                <a href="help_center.php" class="nav-tab">Help Center</a>
                <a href="Analytics.php" class="nav-tab">Analytics</a>
                <a href="Report.php" class="nav-tab">Report</a>
            </div>
        </nav>

        <div class="wrapper">
            <section class="search-section">
                <form method="GET" action="findapet.php" class="search-form">
                    <select name="pet_type">
                        <option value="">Select Pet</option>
                        <?php foreach ($petTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>" <?= $selectedType === $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="shelter">
                        <option value="">Select Shelter</option>
                        <?php foreach ($shelters as $shelter): ?>
                            <option value="<?= htmlspecialchars($shelter['OrgID']) ?>" <?= $selectedOrg === $shelter['OrgID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($shelter['OrgName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="search-btn">SEARCH</button>
                </form>
            </section>

            <?php if (isset($_GET['applied'])): ?>
                <div class="apply-banner" style="background-color: #d4edda; color: #155724; padding: 15px; margin: 20px 0; text-align: center; border-radius: 5px;">
                    Your adoption application has been submitted! You'll be notified once it's reviewed.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['already_applied'])): ?>
                <div class="apply-banner" style="background-color: #fff3cd; color: #856404; padding: 15px; margin: 20px 0; text-align: center; border-radius: 5px;">
                    You have already applied for this pet.
                </div>
            <?php endif; ?>

            <section class="pet-grid">
                <?php if (empty($pets)): ?>
                    <p class="empty-state">No pets found.</p>
                <?php else: ?>
                    <?php foreach ($pets as $pet): ?>
                    <div class="pet-card">
                        <div class="pet-image">
                            <?php if (!empty($pet['Photo'])): ?>
                                <img src="<?= $photoFolder . htmlspecialchars($pet['Photo']) ?>" alt="<?= htmlspecialchars($pet['PetName']) ?>">
                            <?php else: ?>
                                Image Missing
                            <?php endif; ?>
                        </div>
                        <p class="pet-name"><?= htmlspecialchars($pet['PetName']) ?></p>
                        <ul class="pet-info">
                            <li><span class="pet-info-label">Type:</span> <?= htmlspecialchars($pet['PetType']) ?></li>
                            <li><span class="pet-info-label">Breed:</span> <?= htmlspecialchars($pet['Breed']) ?></li>
                            <li><span class="pet-info-label">Age:</span> <?= htmlspecialchars($pet['Age']) ?></li>
                            <li><span class="pet-info-label">Gender:</span> <?= htmlspecialchars($pet['Gender']) ?></li>
                            <li><span class="pet-info-label">Location:</span> <?= htmlspecialchars($pet['Location']) ?></li>
                        </ul>

                       <div class="pet-apply">
                            <?php if (!isset($_SESSION['residentID'])): ?>
                                <!-- Butang Log In -->
                                <button type="button" class="apply-btn" onclick="window.location.href='../User_Login.php';">Log In to Apply</button>
                            <?php else:
                                $appStatus = $myApplications[$pet['PetID']] ?? null;
                                if ($appStatus === 'Pending' || $appStatus === 'Submit'): ?>
                                    <!-- Butang Gagal/Pending - Dipaksa guna class apply-btn dan style inline supaya tak bertukar kelabu buruk -->
                                    <button type="button" class="apply-btn" disabled style="background-color: #cccccc !important; color: #666666 !important; cursor: not-allowed;">Application Pending</button>
                                <?php elseif ($appStatus === 'Approved'): ?>
                                    <!-- Butang Dah Adopt -->
                                    <button type="button" class="apply-btn" disabled style="background-color: #cccccc !important; color: #666666 !important; cursor: not-allowed;">Already Adopted</button>
                                <?php else: ?>
                                    <!-- Form Submit - Pastikan butang di dalam tag form ini mempunyai class="apply-btn" -->
                                    <form method="POST" action="findapet.php" onsubmit="return confirm('Apply to adopt <?= htmlspecialchars($pet['PetName']) ?>?');" style="margin: 0; padding: 0;">
                                        <input type="hidden" name="apply_pet_id" value="<?= htmlspecialchars($pet['PetID']) ?>">
                                        <button type="submit" class="apply-btn">Apply for Adoption</button>
                                    </form>
                                <?php endif;
                            endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

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
        <script src="../js/script.js"></script>
    </body>
</html>