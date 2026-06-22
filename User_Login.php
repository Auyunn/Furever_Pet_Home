<?php
    session_start();
    
    $conn = new mysqli("localhost", "root", "", "furever_pet_home");
    if($conn->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    // logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $error = "";
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === "" || $password === "") {
            $error = "Please enter both email and password.";
        } else {
            // 1. IDENTIFY USER TYPE BASED ON EMAIL DOMAIN
            if (str_contains($email, 'furever.com')) {
                // Admin Account
                $sql = "SELECT AdminID, FirstName, Password FROM admin WHERE Email = ?";
                $role = 'admin';
                $redirect = "admin/dashboard.php"; 
            } 
            elseif (str_contains($email, 'ngo.com')) {
                // NGO / Organization Account
                $sql = "SELECT OrgID, OrgName, Password FROM organization WHERE Email = ?";
                $role = 'ngo';
                $redirect = "ngo/Pet_listing.php"; 
            } 
            else {
                // User / Resident
                $sql = "SELECT ResidentID, FirstName, Password FROM resident WHERE Email = ? AND Status = 1";
                $role = 'user';
                $redirect = "resident/HomePage(registed).php"; 
            }

            // 2. Base on role
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if ($password === $row['Password']) {
                    
                    $_SESSION['loggedin'] = true;
                    $_SESSION['email'] = $email;
                    $_SESSION['name'] = $row['FirstName'];
                    $_SESSION['role'] = $role;

                    // 3. Store ID base on role
                    if ($role === 'admin') {
                        $_SESSION['adminID'] = $row['AdminID'];
                    } elseif ($role === 'ngo') {
                        $_SESSION['orgID'] = $row['OrgID'];
                    } else {
                        $_SESSION['residentID'] = $row['ResidentID'];
                    }

                    // Redirect ke page yang sepatutnya
                    header("Location: " . $redirect);
                    exit;

                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "No account found with that email.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Furever Pet Home</title>
  <link rel="stylesheet" href="css/base.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        
        <div class="login-header">
        <img src="image/icons/logo.png" alt="logo">
        <span>Furever Pet Home</span>
        </div>

        <?php if (!empty($_SESSION['loggedin'])): ?>
            <div class="welcome-box">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
                <p>You are logged in as <strong><?php echo strtoupper($_SESSION['role']); ?></strong>.</p>
                <p style="font-size: 0.85rem; color: #666;">ID: <?php echo htmlspecialchars($_SESSION['adminID'] ?? $_SESSION['orgID'] ?? $_SESSION['residentID']); ?></p>
                <a href="?logout=1" class="logout-btn">Logout</a>
            </div>

        <?php else: ?>
            <div class="login-header" style="margin-bottom: 1rem;">
                <h2 style="margin: 0;">Login</h2>
            </div>

            <?php if ($error): ?>
                <p class="error" style="color: #d43f3a;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form method="post" action="">
                
                <div class="login-field">
                    <input type="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <div class="login-field">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="login-nav">
                    <a href="Sign_Up.php">Don't have an account?</a>
                    <a href="ForgotPassword.php">Forgot Password?</a>
                </div>

                <div class="login-btn-wrap">
                    <input type="submit" class="login-btn" value="Login">
                </div>

            </form>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
