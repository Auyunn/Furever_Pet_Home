
<?php
    session_start();

    $resident_id = $_SESSION['resident_id'] ?? null; 

    $conn = new mysqli("localhost", "root", "", "Furever_Pet_Home");

    if($conn->connect_error)
        {
            die("DB connection failed: " . $conn->connect_error);
        }

    //query notification
    $sql = "
        SELECT InboxID, Title, Message, Status, CreatedAt
        FROM inbox
        WHERE ResidentID = ?
        ORDER BY CreatedAt DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resident_id);
    $stmt->execute();
    $result = $stmt->get_result();

    //day
    $today = [];
    $yesterday = [];
    $week = [];

    while($row = $result->fetch_assoc())
        {
            $date = date("Y-m-d", strtotime($row['CreatedAt']));
            if ($date == date('Y-m-d'))
                {
                    $today[] = $row;
                }
            elseif($date == date("Y-m-d", strtotime("-1 day"))) 
                {
                    $yesterday[] = $row;

                }
            else
                {
                    $week[] = $row;
                }
        }

       $stmt->close();

                 
?>

<!Doctype html>
<html lang = "en">
    <head>
        <meta charset = "UTF-8">
        <meta name = "viewport" content="width=device-width, initial-scale=1.0">
        <title>Resident Inbox</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <script>
        window.notifData = {
            today: <?= json_encode($today)?>,
            yesterday: <?= json_encode($yesterday) ?>,
            week: <?= json_encode($week) ?>
        };
    </script>
    <body>
        <!--Logo-->
    <div class="bar">
        <div class="logo">
            <img src="..\image\icons\logo.png" alt="Logo">
            <span>Furever Pet Home</span>
        </div>

        <div class="bar">
            <div class="login">
                <a href="Profile.html">Profile</a>
            </div>
        </div>

        <!--Navigation-->
        <div class="nav">
            <a href="HomePage.html" class="active">HOME</a>
            <a href="Inbox.html">INBOX</a>
            <a href="FindPet.html">FIND A PET</a>
            <a href="PetCommunity.html">PET COMMUNITY</a>
            <a href="RegisterPage.html">HELP CENTER</a>
            <a href="Analytics.html">ANALYTICS</a>
            <a href="Report.html">REPORT</a>
        </div>
    </div>

    <!--Notifications-->
    <div class="notif-container">

        <!--Left: Notification List-->
        <div class="notif-list">

            <!--Today-->
            <div class="notif-group">
                <div class="notif-group-header" onclick="toggleGroup('today')">
                    <span>Today</span>
                    <span class="arrow" id="arrow-today">▾</span>
                </div>

                <div class="notif-items" id="today">
                    <?php foreach ($today as $i=>$n): ?>
                    <div class="notif-item" onclick="openNotif(event, <?= $i ?>, 'today')">
                        <div class="notif-icon">🐾</div>
                        <div class="notif-info">
                            <div class="notif-title"><?= htmlspecialchars($n['Title']) ?></div>
                            <div class="notif-preview"><?= htmlspecialchars($n['Message']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>


            <!--Yesterday-->
            <div class="notif-group">
                <div class="notif-group-header" onclick="toggleGroup('yesterday')">
                    <span>Yesterday</span>
                    <span class="arrow" id="arrow-yesterday">▾</span>
                </div>
                <div class="notif-items" id="yesterday">
                    <?php foreach($yesterday as $i=>$n): ?>
                        <div class="notif-item" onclick="openNotif(event, <?= $i ?>, 'yesterday')">
                            <div class="notif-icon">🐕</div>
                            <div class="notif-info">
                                <div class="notif-title"><?= htmlspecialchars($n['Title'])?></div>
                                <div class="notif-preview"><?= htmlspecialchars($n['Message'])?></div>
                     </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
            <!--This Week-->
            <div class="notif-group">
                <div class="notif-group-header" onclick="toggleGroup('week')">
                    <span>This Week</span>
                    <span class="arrow" id="arrow-week">▸</span>
                </div>

                <div class="notif-items" id="week" style="display:none;">
                    <?php foreach ($week as $i=>$n): ?>
                        <div class="notif-item" onclick="openNotif(event, <?= $i ?>, 'week')">
                            <div class="notif-icon">🐕</div>
                            <div class="notif-info">
                                <div class="notif-title"><?= htmlspecialchars($n['Title'])?></div>
                                <div class="notif-preview"><?= htmlspecialchars($n['Message'])?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
        <!--End-->

        <!--Right: Content-->
        <div class="notif-content" id="notif-content">
            <div class="content-empty">Select a notification to view</div>
        </div>

    </div>

    <!--Footer-->
    <footer>
    <div class="footer-top">
        <div class="logo">
            <img src="..\image\icons\logo.png" alt="Furever Pet Home Logo">
            Furever Pet Home
        </div>

        <div class="footer-mid">
            <p>41700 Bandar Klang, Selangor, Malaysia</p>
            <p><a href="mailto:infor@FureverPetHome.com">infor@FureverPetHome.com</a></p>
            <p>+60 123-456-7890</p>
        </div>

        <div class="footer-links">
            <p><strong>Follow Us</strong></p>
            <p>
                <a href="https://www.facebook.com/FureverPetHome"><img src="..\image\icons\facebook.png" alt="Facebook">Facebook</a>
                <a href="https://www.instagram.com/FureverPetHome"><img src="..\image\icons\instagram.png" alt="Instagram">Instagram</a>
                <a href="https://www.x.com/FureverPetHome"><img src="..\image\icons\x.png" alt="Twitter">X</a>
            </p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© 2026 FureverHome | Urban Pet Adoption & Community Management</p>
    </div>
    </footer>
    <script src="js/script.js"></script>
    </body>
</html>

