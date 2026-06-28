<?php
session_start();
 
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "furever_pet_home"; 
 
$conn = mysqli_connect($host, $user, $pass, $dbname);
 
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
 
/* enter email , reset new password */
if (!isset($_SESSION['fp_step'])) {
    $_SESSION['fp_step'] = 1;
}
 
$errorMsg   = "";
$successMsg = "";
 
/* form submissions */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    /* ---------- step1: verify email existion ---------------------------------------------------------- */

    if (isset($_POST['action']) && $_POST['action'] === 'check_email') {
 
        $email = trim($_POST['email'] ?? '');
 
        if (empty($email)) {
            $errorMsg = "Please enter your email address.";
        } else {
            $stmt = mysqli_prepare($conn, "SELECT ResidentID FROM resident WHERE Email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
 
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $_SESSION['fp_email'] = $email;
                $_SESSION['fp_step']  = 2;
            } else {
                $errorMsg = "No account found with that email address.";
            }
            mysqli_stmt_close($stmt);
        }
    }
 
    /* ---------- step2: set new password -------------------------------------------------------------------- */

    if (isset($_POST['action']) && $_POST['action'] === 'reset_password') {
 
        $newPw     = $_POST['newPw'] ?? '';
        $confirmPw = $_POST['confirmPw'] ?? '';
        $email     = $_SESSION['fp_email'] ?? '';
 
        if (empty($email)) {
            // Session expired or step skipped, send back to step 1
            $_SESSION['fp_step'] = 1;
            $errorMsg = "Your session expired. Please verify your email again.";
        } elseif (empty($newPw) || empty($confirmPw)) {
            $errorMsg = "Please fill in both password fields.";
        } elseif (strlen($newPw) < 8) {
            $errorMsg = "Password must be at least 8 characters long.";
        } elseif ($newPw !== $confirmPw) {
            $errorMsg = "Passwords do not match. Please try again.";
        } else {
            $hashedPw = password_hash($newPw, PASSWORD_DEFAULT);
 
            // changed
              $stmt = mysqli_prepare($conn, "UPDATE resident SET Password = ? WHERE Email = ?");
              mysqli_stmt_bind_param($stmt, "ss", $newPw, $email);

              if (mysqli_stmt_execute($stmt)) {
                  if (mysqli_stmt_affected_rows($stmt) > 0) {
                      $_SESSION['fp_step'] = 3;
                      unset($_SESSION['fp_email']);
                  } else {
                      $errorMsg = "No matching account was found for email \"$email\" — the password was not changed. Please verify the email is correct.";
                  }
              } else {
                  $errorMsg = "Something went wrong: " . mysqli_error($conn);
              }
              mysqli_stmt_close($stmt);
        }
    }
 
    /* ---------- restart the flow ---------- */
    if (isset($_POST['action']) && $_POST['action'] === 'restart') {
        unset($_SESSION['fp_email']);
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
<title>Forgot Password — Furever Home Pet</title>

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
          if ($currentStep === 1) echo "Enter your email to verify your account.";
          elseif ($currentStep === 2) echo "Create a new password for your account.";
          else echo "Your password has been updated.";
        ?>
      </p>
    </div>
 
    <?php if (!empty($errorMsg)): ?>
      <div class="error"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>
 
    <?php if (!empty($successMsg)): ?>
      <div class="success"><?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>
 
    <!--------------------------- step1: verify email  -------------------------------------------------------------------------->
    <div class="fp-step <?php echo $currentStep === 1 ? 'active' : ''; ?>">
      <form method="POST" action="ForgotPassword.php">
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
        <a href="User_Login.php">Remembered your password? Log in</a>
      </div>
    </div>
 
    <!---------------------------- step2: set new password  --------------------------------------------------------------->

    <div class="fp-step <?php echo $currentStep === 2 ? 'active' : ''; ?>">
      <form method="POST" action="ForgotPassword.php">
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
        <form method="POST" action="ForgotPassword.php" style="display:inline;">
          <input type="hidden" name="action" value="restart">
          <button type="submit" style="background:none;border:none;font-size:0.8125rem;color:var(--deep-brown);text-decoration:underline;cursor:pointer;font-family:inherit;">
            Use a different email
          </button>
        </form>
      </div>
    </div>
 
    <!---------------------------- step3: done  ---------------------------------------------------------------->

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