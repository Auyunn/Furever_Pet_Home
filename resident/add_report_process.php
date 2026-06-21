<?php
/**
 * resident/add_report_process.php
 * Processes the "Report a Stray Animal" form submission.
 * Expects POST from a form with fields: reportName, reportLocation, reportDesc
 */

session_start();
require_once("../db_connect.php"); // adjust path if this file lives elsewhere

// ── AUTH CHECK ──
if (empty($_SESSION['Email'])) {
    header('Location: ../User_Login.php');
    exit;
}

$residentEmail = $_SESSION['Email'];

$stmt = mysqli_prepare($conn, "SELECT ResidentID FROM resident WHERE Email = ? AND Status = 1");
mysqli_stmt_bind_param($stmt, 's', $residentEmail);
mysqli_stmt_execute($stmt);
$authResult = mysqli_stmt_get_result($stmt);
$authRow = mysqli_fetch_assoc($authResult);
mysqli_stmt_close($stmt);

if (!$authRow) {
    session_unset();
    session_destroy();
    header('Location: ../User_Login.php');
    exit;
}

$residentID = $authRow['ResidentID'];

// ── ONLY ACCEPT POST REQUESTS ──
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: AddReport.php');
    exit;
}

// ── COLLECT + VALIDATE INPUT ──
$errors = [];

$reportName     = trim($_POST['reportName'] ?? '');
$reportLocation = trim($_POST['reportLocation'] ?? '');
$reportDesc     = trim($_POST['reportDesc'] ?? '');

if ($reportName === '') {
    $errors[] = 'Pet name / description of animal is required.';
} elseif (mb_strlen($reportName) > 100) {
    $errors[] = 'Pet name must be 100 characters or fewer.';
}

if ($reportLocation === '') {
    $errors[] = 'Location is required.';
} elseif (mb_strlen($reportLocation) > 255) {
    $errors[] = 'Location must be 255 characters or fewer.';
}

if (!empty($errors)) {
    $_SESSION['report_errors'] = $errors;
    $_SESSION['report_old']    = $_POST; // so the form can refill on error
    header('Location: AddReport.php');
    exit;
}

// ── AUTO-GENERATE ReportID ──
// Looks at the highest existing REP### number and increments it.
// e.g. last is REP17 -> next is REP18
$result = mysqli_query($conn, "SELECT ReportID FROM report ORDER BY CAST(SUBSTRING(ReportID, 4) AS UNSIGNED) DESC LIMIT 1");
$lastRow = mysqli_fetch_assoc($result);

if ($lastRow && preg_match('/REP(\d+)/', $lastRow['ReportID'], $matches)) {
    $nextNumber = (int) $matches[1] + 1;
} else {
    $nextNumber = 1; // no reports exist yet
}

$reportID = 'REP' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT); // REP01, REP02, ... REP18

// Safety check: in the rare case of a collision (e.g. concurrent submissions),
// keep incrementing until we find an unused ID.
$checkStmt = mysqli_prepare($conn, "SELECT ReportID FROM report WHERE ReportID = ?");
while (true) {
    mysqli_stmt_bind_param($checkStmt, 's', $reportID);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);
    if (mysqli_stmt_num_rows($checkStmt) === 0) {
        break; // ID is free, safe to use
    }
    $nextNumber++;
    $reportID = 'REP' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
}
mysqli_stmt_close($checkStmt);

// ── INSERT INTO DATABASE ──
$orgID  = null;     // OrgID is nullable per your schema update — assigned later by admin/NGO
$status = 'Submit';  // initial status when a resident first files a report
$photo  = null;      // no file upload handling yet

$query = "INSERT INTO report (ReportID, ResidentID, OrgID, PetName, Location, Description, Status, Photo)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param(
    $stmt,
    'ssssssss', // 8 columns: ReportID, ResidentID, OrgID, PetName, Location, Description, Status, Photo
    $reportID, $residentID, $orgID, $reportName, $reportLocation, $reportDesc, $status, $photo
);

$success = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($success) {
    $_SESSION['report_success'] = "Your report ($reportID) has been submitted successfully. Thank you for helping keep strays safe!";
    mysqli_close($conn);
    header('Location: AddReport.php');
    exit;
} else {
    $_SESSION['report_errors'] = ['Something went wrong while saving your report. Please try again.'];
    $_SESSION['report_old']    = $_POST;
    mysqli_close($conn);
    header('Location: AddReport.php');
    exit;
}