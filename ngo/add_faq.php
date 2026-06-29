<?php
    session_start();
    include("../db_connect.php");

    if (empty($_SESSION['loggedin']) || $_SESSION['role'] !== 'ngo') {
        header("Location: ../User_Login.php");
        exit;
    }

    $orgID = $_SESSION['orgID'];
    $error = "";
    $success = isset($_GET['success']) ? "FAQ posted successfully!" : "";

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['post_faq'])) {
        $question    = trim($_POST['question'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($question === '' || $description === '') {
            $error = "Please fill in all fields.";
        } else {
            $sql  = "INSERT INTO faq (OrgID, Question, Description) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $orgID, $question, $description);

            if ($stmt->execute()) {
                header("Location: add_faq.php?success=1");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add FAQ - Furever Pet Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-page {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem 4rem;
        }
        .form-page h2 {
            font-family: 'Playfair Display', serif;
            color: var(--deep-brown);
            margin-bottom: 1.5rem;
        }
        .faq-form {
            display: grid;
            grid-template-columns: 1fr 1.3fr;
            gap: 2.5rem;
            background: rgba(250,246,240,0.95);
            border: 1px solid rgba(130,85,64,0.2);
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .form-field {
            margin-bottom: 1.1rem;
        }
        .form-field label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.35rem;
            color: var(--deep-brown);
        }
        .form-field input,
        .form-field textarea {
            width: 100%;
            padding: 0.55rem 0.7rem;
            font-size: 0.9rem;
            font-family: 'DM Sans', sans-serif;
            border: 1px solid rgba(130,85,64,0.4);
            border-radius: 0.3rem;
            background-color: rgba(255,255,255,0.7);
            color: var(--deep-brown);
            outline: none;
            transition: border-color 0.2s, background-color 0.2s;
        }
        .form-field textarea {
            min-height: 160px;
            resize: vertical;
        }
        .form-field input:focus,
        .form-field textarea:focus {
            border-color: var(--deep-brown);
            background-color: #ffffff;
        }
        .form-actions {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1rem;
            border-top: 1px solid rgba(130,85,64,0.15);
            padding-top: 1.25rem;
        }
        .form-actions button,
        .form-actions a {
            padding: 0.5rem 1.5rem;
            font-size: 0.9rem;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            border-radius: 2rem;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.2s, color 0.2s;
        }
        .btn-reset {
            background: transparent;
            border: 1px solid rgba(130,85,64,0.5);
            color: var(--deep-brown);
        }
        .btn-reset:hover { background: rgba(130,85,64,0.08); }
        .btn-post {
            background: var(--deep-brown);
            border: 1px solid var(--deep-brown);
            color: #fff;
        }
        .btn-post:hover { background: var(--rose); border-color: var(--rose); }
        .btn-cancel {
            background: transparent;
            border: 1px solid rgba(130,85,64,0.3);
            color: var(--deep-brown);
        }
        .btn-cancel:hover { background: rgba(130,85,64,0.06); }
        .form-msg {
            grid-column: 1 / -1;
            text-align: center;
            font-size: 0.85rem;
            padding: 0.6rem;
            border-radius: 0.3rem;
            margin-bottom: 0.5rem;
        }
        .form-msg.error { color: #d43f3a; background: rgba(212,63,58,0.1); }
        .form-msg.success { color: #2b753a; background: rgba(43,117,58,0.1); }

        @media (max-width: 700px) {
            .faq-form { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
<div class="container">

    <nav class="navbar" id="navbar">
        <div class="navbar-top">
            <a href="#" class="nav-logo">
                <img src="../image/icons/logo.png" alt="Furever Pet Home">
                <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
                <button class="notif-btn" title="Notifications" onclick="window.location.href='inbox.php';">🔔<span class="notif-dot"></span></button>
                <div class="avatar" title="My Profile"><?php echo htmlspecialchars(substr($_SESSION['name'] ?? 'NGO', 0, 2)); ?></div>
            </div>
        </div>

        <div class="nav-links">
            <a href="Pet_listing.php" class="nav-tab"> Home</a>
            <a href="inbox.php" class="nav-tab">Inbox</a>
            <a href="findapet.php" class="nav-tab">Find A Pet</a>
            <a href="petcommunity.php" class="nav-tab">Pet Community</a>
            <a href="helpcenter_ngo.php" class="nav-tab active">Help Center</a>
            <a href="Analytics.php" class="nav-tab">Analytics</a>
            <a href="report.php" class="nav-tab">Report</a>
        </div>
    </nav>

    <section class="sub-navbar">
        <button class="guidelines-btn" onclick="window.location.href='guidelines_ngo.php';">Guidelines</button>
        <button class="faq-btn" onclick="window.location.href='helpcenter_ngo.php';">FAQ</button>
    </section>

    <div class="form-page">
        <h2>FAQ Form</h2>

        <form class="faq-form" method="POST" action="">
            <?php if ($error): ?>
                <div class="form-msg error"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div class="form-msg success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <div>
                <div class="form-field">
                    <label for="question">Question</label>
                    <input type="text" id="question" name="question" value="" required>
                </div>
            </div>

            <div>
                <div class="form-field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn-reset">Reset Form</button>
                <button type="submit" name="post_faq" class="btn-post">Post FAQ</button>
                <a href="helpcenter_ngo.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

</div>

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
                    <li><a href="Pet_listing.php">Home</a></li>
                    <li><a href="inbox.php">Inbox</a></li>
                    <li><a href="findapet.php">Find A Pet</a></li>
                    <li><a href="petcommunity.php">Pet Community</a></li>
                    <li><a href="Analytics.php">Analytics</a></li>
                    <li><a href="Report.php">Report Animal</a></li>
                </ul>
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

<script src="../js/script.js"></script>
</body>
</html>
