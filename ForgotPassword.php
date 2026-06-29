<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "furever_pet_home");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Reset session on fresh page load (not POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['fp_step'] = 1;
    unset($_SESSION['fp_email']);
    unset($_SESSION['fp_role']);
}

if (!isset($_SESSION['fp_step'])) {
    $_SESSION['fp_step'] = 1;
}

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ---------- Step 1: Verify Email ---------- */
    if ($_POST['action'] === 'check_email') {

        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $errorMsg = "Please enter your email address.";
        } else {
            $found = null;

            // Check Resident table
            $stmt = mysqli_prepare($conn, "SELECT ResidentID AS id, 'Resident' AS role FROM resident WHERE Email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $found = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            // Check Organization table
            if (!$found) {
                $stmt = mysqli_prepare($conn, "SELECT OrgID AS id, 'Organization' AS role FROM organization WHERE Email = ?");
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $found = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);
            }

            // Check Admin table
            if (!$found) {
                $stmt = mysqli_prepare($conn, "SELECT AdminID AS id, 'Admin' AS role FROM admin WHERE Email = ?");
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $found = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);
            }

            if ($found) {
                $_SESSION['fp_email'] = $email;
                $_SESSION['fp_role']  = $found['role']; // 'Resident', 'Organization', or 'Admin'
                $_SESSION['fp_step']  = 2;
            } else {
                $errorMsg = "No account found with that email address.";
            }
        }
    }

    /* ---------- Step 2: Reset Password ---------- */
    if ($_POST['action'] === 'reset_password') {

        $newPw     = $_POST['newPw']     ?? '';
        $confirmPw = $_POST['confirmPw'] ?? '';
        $email     = $_SESSION['fp_email'] ?? '';
        $role      = $_SESSION['fp_role']  ?? '';

        if (empty($email) || empty($role)) {
            $_SESSION['fp_step'] = 1;
            $errorMsg = "Your session expired. Please verify your email again.";
        } elseif (empty($newPw) || empty($confirmPw)) {
            $errorMsg = "Please fill in both password fields.";
        } elseif (strlen($newPw) < 8) {
            $errorMsg = "Password must be at least 8 characters long.";
        } elseif ($newPw !== $confirmPw) {
            $errorMsg = "Passwords do not match. Please try again.";
        } else {
            if ($role === 'Resident') {
                $sql = "UPDATE resident SET Password = ? WHERE Email = ?";
            } elseif ($role === 'Organization') {
                $sql = "UPDATE organization SET Password = ? WHERE Email = ?";
            } else {
                $sql = "UPDATE admin SET Password = ? WHERE Email = ?";
            }

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $newPw, $email);

            if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                $_SESSION['fp_step'] = 3;
                unset($_SESSION['fp_email']);
                unset($_SESSION['fp_role']);
            } else {
                $errorMsg = "Something went wrong. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    /* ---------- Restart ---------- */
    if ($_POST['action'] === 'restart') {
        unset($_SESSION['fp_email']);
        unset($_SESSION['fp_role']);
        $_SESSION['fp_step'] = 1;
    }
}

$currentStep = $_SESSION['fp_step'];
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Furever Pet Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/forgotpassword.css">
</head>
<body>

<div class="fp-wrapper">
  <div class="fp-card">

    <div class="fp-header">
      <h2>Forgot Password</h2>
      <p style="font-size:0.85rem;color:var(--text-muted);">
        <?php
          if ($currentStep === 1)     echo "Enter your email to verify your account.";
          elseif ($currentStep === 2) echo "Create a new password for your account.";
          else                        echo "Your password has been updated.";
        ?>
      </p>
    </div>

    <?php if (!empty($errorMsg)): ?>
      <div class="error"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <!-- Step 1: Verify Email -->
    <div class="fp-step <?php echo $currentStep === 1 ? 'active' : ''; ?>">
      <form method="POST">
        <input type="hidden" name="action" value="check_email">
        <div class="fp-field">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>
        <div class="fp-btn-wrap">
          <button type="submit" class="fp-btn">Verify Account</button>
        </div>
      </form>
      <div class="fp-nav">
        <a href="User_Login.nphp">Remembered your password? Log in</a>
      </div>
    </div>

    <!-- Step 2: New Password -->
    <div class="fp-step <?php echo $currentStep === 2 ? 'active' : ''; ?>">
      <form method="POST">
        <input type="hidden" name="action" value="reset_password">
        <div class="fp-field">
          <label for="newPw">New Password</label>
          <input type="password" id="newPw" name="newPw" placeholder="At least 8 characters" required>
        </div>
        <div class="fp-field">
          <label for="confirmPw">Confirm Password</label>
          <input type="password" id="confirmPw" name="confirmPw" placeholder="Repeat your new password" required>
        </div>
        <div class="fp-btn-wrap">
          <button type="submit" class="fp-btn">Update Password</button>
        </div>
      </form>
      <div class="fp-nav">
        <form method="POST" style="display:inline;">
          <input type="hidden" name="action" value="restart">
          <button type="submit" style="background:none;border:none;font-size:0.8125rem;color:var(--deep-brown);text-decoration:underline;cursor:pointer;font-family:inherit;">
            Use a different email
          </button>
        </form>
      </div>
    </div>

    <!-- Step 3: Success -->
    <div class="fp-step <?php echo $currentStep === 3 ? 'active' : ''; ?>">
      <div class="success" style="margin-bottom:1.25rem;">
        Your password has been successfully updated.
      </div>
      <p style="text-align:center;font-size:0.9rem;color:var(--text-muted);margin-bottom:1.5rem;">
        You can now log in using your new password.
      </p>
      <div class="fp-btn-wrap">
        <a href="User_Login.php" class="fp-btn" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">
          Go to Login
        </a>
      </div>
      <div class="fp-nav">
        <span style="font-size:0.78125rem;color:var(--text-muted);">
          If you did not request this change, please contact your administrator immediately.
        </span>
      </div>
    </div>

  </div>
</div>

</body>
</html>