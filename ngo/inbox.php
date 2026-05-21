<?php
    //start session
    session_start();
    $org_id = $_SESSION['orgID'] ?? null; 

    $conn = new mysqli("localhost", "root", "", "furever_pet_home");

    if($conn->connect_error)
    {
        die("connection failed." . $conn->connect_error);
    }

    //allowed filter, accept filter via GET param
    $allowed = array('today', 'yesterday', 'this_week');

    $filter = isset($_GET['filter']) ? strtolower($_GET['filter']) : 'today';

    if(!in_array($filter, $allowed))
    {
        $filter = 'today';
    }

    //build sql to restrict ngo pet
    $where = "";
    $param = array(); //to bind value
    $types = ""; //type string for bind

    //show pet owned by ngo (requested)
    if($org_id)
    {
        $where .= " AND p.OrgID = ?";
        $param[] = $org_id;
        $types .= "s";
    }

    //filter date
    if($filter === 'yesterday')
    {
        $where .= " AND DATE(a.RequestDate)= DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
    }
    elseif($filter === 'this_week')
    {
        //week, monday start
        $where .= " AND YEARWEEK(a.RequestDate, 1)= YEARWEEK(CURDATE(), 1)";
    }
    else
    {
        $where .= " AND DATE(a.RequestDate)= CURDATE()";
    }

    //query
    $sql = "
    SELECT 
        a.AdoptionID, 
        a.Status, 
        a.RequestDate, 
        p.PetID, 
        p.PetName, 
        r.ResidentID, 
        r.FirstName, 
        r.LastName
    FROM adopt_application a
    JOIN pet p ON a.PetID = p.PetID
    JOIN resident r ON a.ResidentID = r.ResidentID
    WHERE 1=1 $where
    ORDER BY a.RequestDate DESC
    ";

    //bind param only if have any
    $stmt = $conn->prepare($sql);

    if($types != "")
    {
        call_user_func_array(array($stmt, 'bind_param'), array_merge(array($types), $param));
    }

    $stmt->execute();
    $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Inbox</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<!--Logo-->
<div class="bar">

    <div class="logo">
        <img src="../image/icons/logo.png" alt="Logo">
        <span>Furever Pet Home</span>
    </div>

    <div class="login">
        <a href="Profile.html">Profile</a>
    </div>

    <!--Navigation-->
    <div class="nav">
        <a href="HomePage.html" class="active">Home</a>
        <a href="Inbox.html">Inbox</a>
        <a href="FindPet.html">Find A Pet</a>
        <a href="PetCommunity.html">Pet Community</a>
        <a href="RegisterPage.html">Help Center</a>
        <a href="Analytics.html">Analytics</a>
        <a href="Report.html">Report</a>
    </div>

</div>

<!--Filter box-->
<div class="filter-box">
    <label for="filter">Show:</label>

    <select id="filter" class="select-filter" onchange="applyFilter()">

        <option value="today" <?php echo ($filter=='today') ? 'selected' : ''; ?>>
            Today
        </option>

        <option value="yesterday" <?php echo ($filter=='yesterday') ? 'selected' : ''; ?>>
            Yesterday
        </option>

        <option value="this_week" <?php echo ($filter=='this_week') ? 'selected' : ''; ?>>
            This Week
        </option>

    </select>
</div>

<div class="ngo-inbox-container">

    <div class="inbox-table-wrap">

        <table class="inbox-table">

            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Pet Name</th>
                    <th>Adopter Name</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>


            <?php while($row = $result->fetch_assoc()) { 

                $status = isset($row['Status']) ? $row['Status'] : 'Pending';

                if($status == 'Approved')
                {
                    $badgeClass = 'badge_approved';
                }
                elseif($status == 'Rejected')
                {
                    $badgeClass = 'badge_rejected';
                }
                else
                {
                    $badgeClass = 'badge_pending';
                }

                if(isset($row['RequestDate']))
                {
                    $requestDate = date("d/m/Y H:i", strtotime($row['RequestDate']));
                }
                else
                {
                    $requestDate = '-';
                }
            ?>

                <tr id="row-<?php echo htmlspecialchars($row['AdoptionID']); ?>"
                    data-petid="<?php echo htmlspecialchars($row['PetID']); ?>">

                    <td>
                        <?php echo htmlspecialchars($row['AdoptionID']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['PetName']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?>
                    </td>

                    <td>
                        <?php echo $requestDate; ?>
                    </td>

                    <td>
                        <span class="<?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($status); ?>
                        </span>
                    </td>

                    <td class="action-btns">

                        <button class="btn-approve"
                            onclick="updateStatus('<?php echo htmlspecialchars($row['AdoptionID']); ?>','Approved')">
                            Approve
                        </button>

                        <button class="btn-reject"
                            onclick="updateStatus('<?php echo htmlspecialchars($row['AdoptionID']); ?>','Rejected')">
                            Reject
                        </button>

                        <button class="btn-view"
                            onclick="viewApp('<?php echo htmlspecialchars($row['AdoptionID']); ?>')">
                            View
                        </button>

                        

                    </td>




                </tr>

            <?php } ?>

            </tbody>

        </table>

            <!-- RIGHT: DETAILS -->
            <div class="inbox-right" id="side-panel">
                <div id="panel-content" class="panel-empty">
                    Click "View" on a request to see details here.
                </div>
            </div>


    </div>

</div>

<div class="inbox-container">

    <!-- LEFT: TABLE -->
    <div class="inbox-left">
        <div class="inbox-table-wrap">

            <table class="inbox-table">
                ...
            </table>

        </div>
    </div>

</div>
<!--Footer-->
<footer>

    <div class="footer-top">

        <div class="logo">
            <img src="../image/icons/logo.png" alt="Furever Pet Home Logo">
            Furever Pet Home
        </div>

        <div class="footer-mid">
            <p>41700 Bandar Klang, Selangor, Malaysia</p>

            <p>
                <a href="mailto:infor@FureverPetHome.com">
                    infor@FureverPetHome.com
                </a>
            </p>

            <p>+60 123-456-7890</p>
        </div>

        <div class="footer-links">

            <p><strong>Follow Us</strong></p>

            <p>
                <a href="https://www.facebook.com/FureverPetHome">
                    <img src="../image/icons/facebook.png" alt="Facebook">
                    Facebook
                </a>

                <a href="https://www.instagram.com/FureverPetHome">
                    <img src="../image/icons/instagram.png" alt="Instagram">
                    Instagram
                </a>

                <a href="https://www.x.com/FureverPetHome">
                    <img src="../image/icons/x.png" alt="Twitter">
                    X
                </a>
            </p>

        </div>

    </div>

    <div class="footer-bottom">
        <p>© 2026 FureverHome | Urban Pet Adoption & Community Management</p>
    </div>

</footer>

<script src="../js/script.js"></script>

</body>
</html>
