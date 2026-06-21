<?php

session_start();
include('db_connect.php');

$selectedType = $_REQUEST['pet_type'] ?? '';
$selectedOrg  = $_REQUEST['shelter']  ?? '';

// --- Handle "Apply for Adoption" submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_pet_id'])) {

    if (!isset($_SESSION['residentID'])) {
        header("Location: User_Login.php");
        exit;
    }

    $residentID = $_SESSION['residentID'];
    $petID = $_POST['apply_pet_id'];

    // Don't allow a duplicate application while one is already pending or approved
    $checkSql = "SELECT AdoptionID FROM adopt_application WHERE ResidentID = ? AND PetID = ? AND Status IN ('Pending','Approved')";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $residentID, $petID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        $insertSql = "INSERT INTO adopt_application (ResidentID, PetID, Status, Reason, RequestDate) VALUES (?, ?, 'Pending', NULL, NOW())";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("ss", $residentID, $petID);
        $insertStmt->execute();
        $insertStmt->close();
    }
    $checkStmt->close();

    header("Location: findapet.php?applied=1&pet_type=" . urlencode($selectedType) . "&shelter=" . urlencode($selectedOrg));
    exit;
}

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

// --- Look up the logged-in resident's existing applications, so we know which
//     pets to show as "Pending" / "Already Adopted" instead of an Apply button ---
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

// --- Fetch nama untuk avatar initials ---
$avatarInitials = 'AT';
if (isset($_SESSION['residentID'])) {
    $avatarStmt = $conn->prepare("SELECT FirstName, LastName FROM resident WHERE ResidentID = ?");
    $avatarStmt->bind_param("s", $_SESSION['residentID']);
    $avatarStmt->execute();
    $avatarRow = $avatarStmt->get_result()->fetch_assoc();
    $avatarStmt->close();

    if ($avatarRow) {
        $avatarInitials = strtoupper(substr($avatarRow['FirstName'], 0, 1) . substr($avatarRow['LastName'], 0, 1));
    }
}

$photoFolder = "image/pets/";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title> Find A Pet </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/base.css">
        <link rel="stylesheet" href="css/findapet.css">
    </head>

    <body>
        <nav class="navbar" id="navbar">
        <div class="navbar-top">
            <a href="#" class="nav-logo">
            <img src="image/icons/logo.png" alt="Furever Pet Home">
            <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
            <button class="notif-btn" title="Notifications" onclick="window.location.href='resident/inbox.php';">🔔<span class="notif-dot"></span></button>
            <div class="avatar" title="My Profile">
                <?= htmlspecialchars($avatarInitials) ?>
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
            <div class="apply-banner">Your adoption application has been submitted! You'll be notified once it's reviewed.</div>
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
                            Image
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
                            <button type="button" class="apply-btn" onclick="window.location.href='User_Login.php';">Log In to Apply</button>
                        <?php else:
                            $appStatus = $myApplications[$pet['PetID']] ?? null;
                            if ($appStatus === 'Pending'): ?>
                                <button type="button" class="apply-btn" disabled>Application Pending</button>
                            <?php elseif ($appStatus === 'Approved'): ?>
                                <button type="button" class="apply-btn" disabled>Already Adopted</button>
                            <?php else: ?>
                                <form method="POST" action="findapet.php" onsubmit="return confirm('Apply to adopt <?= htmlspecialchars($pet['PetName']) ?>?');">
                                    <input type="hidden" name="apply_pet_id" value="<?= htmlspecialchars($pet['PetID']) ?>">
                                    <input type="hidden" name="pet_type" value="<?= htmlspecialchars($selectedType) ?>">
                                    <input type="hidden" name="shelter" value="<?= htmlspecialchars($selectedOrg) ?>">
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
    </body>
</html>
