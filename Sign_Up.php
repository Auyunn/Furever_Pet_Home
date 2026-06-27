<?php
    //session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $con = new mysqli("localhost", "root", "", "furever_pet_home");
    if($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    $error = "";
    $success = "";

    // set variable
    $step = 1;
    $detected_role = "user";

    // set string to zero if not exist
    $session_email = isset($_SESSION['reg_email']) ? $_SESSION['reg_email'] : "";
    $session_password = isset($_SESSION['reg_password']) ? $_SESSION['reg_password'] : "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        // ================= BUTTON NEXT =================
        if (isset($_POST['action_next'])) {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($email === "" || $password === "") {
                $error = "Please fill in both Email and Password.";
            } else {
                //check if email exist
                $check_admin = $con->prepare("SELECT Email FROM admin WHERE Email = ?");
                $check_admin->bind_param("s", $email); $check_admin->execute(); $res_admin = $check_admin->get_result();

                $check_org = $con->prepare("SELECT Email FROM organization WHERE Email = ?");
                $check_org->bind_param("s", $email); $check_org->execute(); $res_org = $check_org->get_result();

                $check_res = $con->prepare("SELECT Email FROM resident WHERE Email = ?");
                $check_res->bind_param("s", $email); $check_res->execute(); $res_res = $check_res->get_result();

                if ($res_admin->num_rows > 0 || $res_org->num_rows > 0 || $res_res->num_rows > 0) {
                    $error = "This email address is already registered. Please use another email.";
                } else {
                    // store data to temporary session
                    $_SESSION['reg_email'] = $email;
                    $_SESSION['reg_password'] = $password;
                    
                    
                    $session_email = $email;
                    $session_password = $password;

                    // decide role
                    if (str_ends_with($email, '@furever.com')) {
                        $detected_role = "admin";
                    } elseif (str_ends_with($email, '@ngo.com')) {
                        $detected_role = "ngo";
                    } else {
                        $detected_role = "user";
                    }
                    
                    $step = 2; //go to next form
                }
            }
        }
        
        // ================= SUBMIT & REGISTER =================
        elseif (isset($_POST['action_register'])) {
            $email = $_SESSION['reg_email'] ?? '';
            $password = $_SESSION['reg_password'] ?? '';
            $detected_role = $_POST['detected_role'] ?? 'user';

            if ($detected_role === 'admin') {
                $fname = trim($_POST['FirstName'] ?? '');
                $lname = trim($_POST['LastName'] ?? '');
                $phone = trim($_POST['NumberPhone'] ?? '');

                //count sql data 
                $result = $con->query("SELECT COUNT(*) as total FROM admin");
                $row = $result->fetch_assoc();
                $next_num = $row['total'] + 1;
                $unique_id = "ADM" . $next_num;

                $sql = "INSERT INTO admin (AdminID, FirstName, LastName, Email, NumberPhone, Password) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ssssss", $unique_id, $fname, $lname, $email, $phone, $password);

            } elseif ($detected_role === 'ngo') {
                $org_name = trim($_POST['OrgName'] ?? '');
                $phone = trim($_POST['NumberPhone'] ?? '');
                $org_address = trim($_POST['OrgAddress'] ?? '');

                // count sql ngo data
                $result = $con->query("SELECT COUNT(*) as total FROM organization");
                $row = $result->fetch_assoc();
                $next_num = $row['total'] + 1;
                $unique_id = "ORG" . sprintf("%02d", $next_num); //create id

                //insert data
                $sql = "INSERT INTO organization (OrgID, OrgName, NumberPhone, OrgAddress, Email, Password, Description) VALUES (?, ?, ?, ?, ?, ?, '')";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ssssss", $unique_id, $org_name, $phone, $org_address, $email, $password);

            } else {
                $fname = trim($_POST['FirstName'] ?? '');
                $lname = trim($_POST['LastName'] ?? '');
                $phone = trim($_POST['NumberPhone'] ?? '');
                $address = trim($_POST['Address'] ?? '');
                $status = 1;

                // count id in sql
                $result = $con->query("SELECT COUNT(*) as total FROM resident");
                $row = $result->fetch_assoc();
                $next_num = $row['total'] + 1;
                $unique_id = "R" . sprintf("%04d", $next_num); //create user id format

                $sql = "INSERT INTO resident (ResidentID, FirstName, LastName, NumberPhone, Email, Password, Address, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("sssssssi", $unique_id, $fname, $lname, $phone, $email, $password, $address, $status);
            }

            if ($stmt && $stmt->execute()) {
                
                //delete temporary session
                unset($_SESSION['reg_email']);
                unset($_SESSION['reg_password']);
                echo "Register Successful!<br>Please log in into our system.";
                
                echo "<script>
                        alert('Register Successful!\\nPlease log in into our system.');
                        window.location.href = 'User_Login.php';
                      </script>";
                exit();
            } else {
                $error = "Database registration failed: " . $con->error;
                $step = 2; // stay in step 2
            }
        }
        
        // ================= ACTION BUTTON BACK =================
        elseif (isset($_POST['action_back'])) {
            $step = 1;
            // set state back to user
            $detected_role = $_POST['detected_role'] ?? 'user';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Furever Pet Home</title>
  <link rel="stylesheet" href="css/base.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="signup-wrapper">
    <div class="signup-card">
        
        <div class="signup-header">
            <h2>Sign Up</h2>
            <p>Step <?php echo $step; ?> of 2</p>
        </div>

        <?php if ($error): ?>
            <p class="error" style="color: red; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- show form 1 -->
        <?php if ($step === 1): ?>
            <form method="post" action="">
                <div class="signup-field">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="example@domain.com" required value="<?php echo htmlspecialchars($session_email); ?>">
                </div>

                <div class="signup-field">
                    <label>Password</label>
                    <input type="password" name="password" required value="<?php echo htmlspecialchars($session_password); ?>">
                </div>

                <div class="signup-btn-wrap">
                    <button type="submit" name="action_next" class="signup-btn">Next</button>
                </div>
            </form>

        <?php else: ?>
            <form method="post" action="">
                <!-- detect ngo-->
                <input type="hidden" name="detected_role" value="<?php echo $detected_role; ?>">

                <?php if ($detected_role === 'ngo'): ?>
                    <p style="font-size:0.9rem; margin-bottom:1rem; color:orange; font-weight:bold;">Account Type: NGO / Organization</p>
                    
                    <div class="signup-field">
                        <label>Organization Name (OrgName)</label>
                        <input type="text" name="OrgName" required placeholder="Enter organization name">
                    </div>
                    <div class="signup-field">
                        <label>Number Phone</label>
                        <input type="text" name="NumberPhone" required placeholder="Enter phone number">
                    </div>
                    <div class="signup-field">
                        <label>Organization Address (OrgAddress)</label>
                        <input type="text" name="OrgAddress" required placeholder="Enter office address">
                    </div>

                    <!--detect admin-->
                <?php elseif ($detected_role === 'admin'): ?>
                    <p style="font-size:0.9rem; margin-bottom:1rem; color:brown; font-weight:bold;">Account Type: Internal Admin</p>
                    
                    <div class="signup-field">
                        <label>First Name</label>
                        <input type="text" name="FirstName" required placeholder="Enter admin first name">
                    </div>
                    <div class="signup-field">
                        <label>Last Name</label>
                        <input type="text" name="LastName" required placeholder="Enter admin last name">
                    </div>
                    <div class="signup-field">
                        <label>Number Phone</label>
                        <input type="text" name="NumberPhone" required placeholder="Enter phone number">
                    </div>

                <?php else: ?>
                    <!-- detect user-->
                    <p style="font-size:0.9rem; margin-bottom:1rem; color:pink; font-weight:bold;">Account Type: Public User / Resident</p>
                    
                    <div class="signup-field">
                        <label>First Name</label>
                        <input type="text" name="FirstName" required placeholder="Enter first name">
                    </div>
                    <div class="signup-field">
                        <label>Last Name</label>
                        <input type="text" name="LastName" required placeholder="Enter last name">
                    </div>
                    <div class="signup-field">
                        <label>Number Phone</label>
                        <input type="text" name="NumberPhone" required placeholder="Enter phone number">
                    </div>
                    <div class="signup-field">
                        <label>Address</label>
                        <input type="text" name="Address" required placeholder="Enter resident address">
                    </div>
                <?php endif; ?>

                <div class="signup-btn-wrap" style="display:flex; gap:10px; margin-top:1.5rem;">
                    <button type="submit" name="action_back" class="signup-btn btn-secondary" style="background:#ddd; color:#333; padding: 5px 10px; border:none; cursor:pointer;">Back</button>
                    <button type="submit" name="action_register" class="signup-btn">Submit & Register</button>
                </div>
            </form>
        <?php endif; ?>

        <div class="signup-nav" style="margin-top: 1.5rem;">
            <a href="User_Login.php">Already have an account? Login here</a>
        </div>

    </div>
</div>

</body>
</html>