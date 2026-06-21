<?php
session_start();
include('../db_connect.php');


$selectedType = $_GET['pet_type'] ?? '';
$selectedOrg  = $_GET['shelter']  ?? '';

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

    <!--body -->
    <body>
        <nav class="navbar" id="navbar">
        <!--logo and profile-->
        <div class="navbar-top">
            <a href="#" class="nav-logo">
            <img src="../image/icons/logo.png" alt="Furever Pet Home">
            <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
            <button class="notif-btn" title="Notifications" onclick="window.location.href='resident/inbox.php';">🔔<span class="notif-dot"></span></button>
            <div class="avatar" title="My Profile" onclick="window.location.href='User Login.html';">
                <?= isset($_SESSION['username']) ? htmlspecialchars(strtoupper(substr($_SESSION['username'], 0, 2))) : 'AT' ?>
            </div>
            </div>
        </div>

        <!---Tab Navigation-->
        <div class="nav-links">
            <a href="Pet_listing.php" class="nav-tab"> Home</a>
            <a href="inbox.php" class="nav-tab"> Inbox</a>
            <a href="findapet.php" class="nav-tab"> Find A Pet</a>
            <a href="pet_community.html" class="nav-tab"> Pet Community</a>
            <a href="helpcenter_ngo.php" class="nav-tab"> Help Center</a>
            <a href="Analytics.html" class="nav-tab"> Analytics</a>
            <a href="report..php" class="nav-tab"> Report</a>
        </div>
        </nav>

        <!--wrapper pushes all page content below the fixed navbar-->
        <div class="wrapper">

        <!--search-->
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

        <!--pet list-->
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
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

    <!--Footer-->
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

        </div><!--/wrapper-->
    </body>
</html>