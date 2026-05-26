<?php
    include("../db_connect.php");

    $search = $_GET['search'] ?? '';

    $sql = "SELECT Question FROM faq WHERE Question LIKE ?";
    $stmt = $conn->prepare($sql);

    $like = "%$search%";
    $stmt->bind_param("s", $like);

    $stmt->execute();
    $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Furever Pet Home</h1>
    </div>

    <!-- Navigation -->
    <div class="nav">
        <a href="HomePage.php" class="active">HOME</a>
        <a href="Inbox.php">INBOX</a>
        <a href="FindPet.php">FIND A PET</a>
        <a href="PetCommunity.php">PET COMMUNITY</a>
        <a href="helpcenter.php">HELP CENTER</a>
        <a href="Analytics.php">ANALYTICS</a>
        <a href="Report.php">REPORT</a>
    </div>

    <!-- Search -->
    <div class="search-container">
        <form method="GET">
            <input type="text" name="search" placeholder="Search...">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Help Center -->
    <div class="help-center">
        <h2>Help Center</h2>

        <ul>
            <?php
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    echo "<li>" . htmlspecialchars($row['Question']) . "</li>";
                }
            } else {
                echo "<li>No FAQ available</li>";
            }
            ?>
        </ul>
    </div>

</div>

<footer>

    <div class="footer-top">
        <div class="logo">
            <img src="../image/icons/logo.png">
            Furever Pet Home
        </div>

        <div class="footer-mid">
            <p>41700 Bandar Klang, Selangor, Malaysia</p>
            <p><a href="mailto:infor@FureverPetHome.com">infor@FureverPetHome.com</a></p>
            <p>+60 123-456-7890</p>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© 2026 FureverHome</p>
    </div>

</footer>

<script src="../js/script.js"></script>

</body>
</html>
