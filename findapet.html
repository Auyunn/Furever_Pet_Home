<?php
session_start();
include 'db_connect.php';

// Build query: join pet with organization to show shelter name
$sql = "SELECT p.PetID, p.PetName, p.PetType, p.Breed, p.Age, p.Location, 
               p.Gender, p.Photo, p.IsAvailable, o.OrgName
        FROM pet p
        JOIN organization o ON p.OrgID = o.OrgID
        ORDER BY p.PetID ASC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Find A Pet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/findapet.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="navbar-top">
            <a href="#" class="nav-logo">
                <img src="image/icons/logo.png" alt="Furever Pet Home">
                <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
                <button class="notif-btn" title="Notifications" onclick="window.location.href='resident/inbox.php';">🔔<span class="notif-dot"></span></button>
                <div class="avatar" title="My Profile" onclick="window.location.href='User Login.html';">AT</div>
            </div>
        </div>
        <div class="nav-links">
            <a href="HomePage(registed).html" class="nav-tab"> Home</a>
            <a href="resident/inbox.php" class="nav-tab"> Inbox</a>
            <a href="findapet.php" class="nav-tab"> Find A Pet</a>
            <a href="resident/pet_community.php" class="nav-tab"> Pet Community</a>
            <a href="resident/help_center.php" class="nav-tab"> Help Center</a>
            <a href="Analytics.html" class="nav-tab"> Analytics</a>
            <a href="resident/Report.html" class="nav-tab"> Report</a>
        </div>
    </nav>

    <!-- Search Filters -->
    <section class="search-section">
        <form method="GET" action="findapet.php">
            <select name="petType">
                <option value="">Select Pet</option>
                <option value="Dog">Dog</option>
                <option value="Cat">Cat</option>
            </select>

            <select name="shelter">
                <option value="">Select Shelter</option>
                <?php
                // Populate shelters dynamically
                $orgs = $conn->query("SELECT OrgID, OrgName FROM organization ORDER BY OrgName ASC");
                while($org = $orgs->fetch_assoc()){
                    echo "<option value='".htmlspecialchars($org['OrgName'])."'>".htmlspecialchars($org['OrgName'])."</option>";
                }
                ?>
            </select>

            <button type="submit" class="search-btn">SEARCH</button>
        </form>
    </section>

    <!-- Pet List -->
    <section class="pet-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="pet-card">
                    <div class="pet-image">
                        <?php if (!empty($row['Photo'])): ?>
                            <img src="image/pets/<?= htmlspecialchars($row['Photo']) ?>" 
                                 alt="<?= htmlspecialchars($row['PetName']) ?>">
                        <?php else: ?>
                            <span style="color:#aaa;">No photo</span>
                        <?php endif; ?>
                    </div>
                    <p class="pet-name"><?= htmlspecialchars($row['PetName']) ?> (<?= htmlspecialchars($row['PetType']) ?>)</p>
                    <p class="pet-info">Breed: <?= htmlspecialchars($row['Breed']) ?> | Age: <?= htmlspecialchars($row['Age']) ?> | Gender: <?= htmlspecialchars($row['Gender']) ?></p>
                    <p class="pet-shelter">Shelter: <?= htmlspecialchars($row['OrgName']) ?></p>
                    <p class="pet-status"><?= $row['IsAvailable'] ? "✅ Available" : "❌ Not Available" ?></p>
                    <button onclick="window.location.href='pet_details.php?id=<?= $row['PetID'] ?>'">DETAILS</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">No pets available at the moment.</p>
        <?php endif; ?>
    </section>

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
</body>
</html>
