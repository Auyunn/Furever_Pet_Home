<?php
/**
 * resident/add_report_process.php
 */
session_start();
require_once("../db_connect.php");

if (empty($_SESSION['residentID'])) {
    header('Location: ../User_Login.php');
    exit;
}
$residentID = $_SESSION['residentID'];

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
    $_SESSION['report_old']    = $_POST;
    header('Location: AddReport.php');
    exit;
}

// ── AUTO-GENERATE ReportID ──
$result  = mysqli_query($conn, "SELECT ReportID FROM report ORDER BY CAST(SUBSTRING(ReportID, 4) AS UNSIGNED) DESC LIMIT 1");
$lastRow = mysqli_fetch_assoc($result);
if ($lastRow && preg_match('/REP(\d+)/', $lastRow['ReportID'], $matches)) {
    $nextNumber = (int) $matches[1] + 1;
} else {
    $nextNumber = 1;
}
$reportID = 'REP' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

$checkStmt = mysqli_prepare($conn, "SELECT ReportID FROM report WHERE ReportID = ?");
while (true) {
    mysqli_stmt_bind_param($checkStmt, 's', $reportID);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);
    if (mysqli_stmt_num_rows($checkStmt) === 0) break;
    $nextNumber++;
    $reportID = 'REP' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
}
mysqli_stmt_close($checkStmt);

// ── PROSES UPLOAD GAMBAR ──
$photo = null;
if (isset($_FILES['reportPhoto']) && $_FILES['reportPhoto']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath   = $_FILES['reportPhoto']['tmp_name'];
    $fileName      = $_FILES['reportPhoto']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $uploadFileDir = __DIR__ . '/../image/report/';
    if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0755, true);
    }
    $newFileName = $reportID . '.' . $fileExtension;
    $dest_path   = $uploadFileDir . $newFileName;
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($fileExtension, $allowedExtensions)) {
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $photo = $newFileName;
        }
    }
}

// ── GET OrgID based on location (match against OrgAddress, active orgs only) ──
$orgID = null;

// Cuba match: kawasan dalam OrgAddress wujud dalam Location yang user taip,
// ATAU location yang user taip wujud dalam OrgAddress.
// Hanya organisasi aktif (Status = 1) yang dipertimbangkan.
$orgStmt = mysqli_prepare(
    $conn,
    "SELECT OrgID FROM organization
     WHERE Status = 1
       AND (? LIKE CONCAT('%', OrgAddress, '%') OR OrgAddress LIKE CONCAT('%', ?, '%'))
     LIMIT 1"
);
mysqli_stmt_bind_param($orgStmt, 'ss', $reportLocation, $reportLocation);
mysqli_stmt_execute($orgStmt);
$orgResult = mysqli_fetch_assoc(mysqli_stmt_get_result($orgStmt));
mysqli_stmt_close($orgStmt);

if ($orgResult) {
    $orgID = $orgResult['OrgID'];
} else {
    // Takde match terus — cuba match per-perkataan kawasan dalam Location
    // (cth: user taip "Bandar Botanic" je, tanpa alamat penuh)
    $locationWords = preg_split('/[\s,]+/', $reportLocation, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($locationWords as $word) {
        if (mb_strlen($word) < 4) continue; // skip perkataan terlalu pendek (cth: "no", "41200")
        $wordStmt = mysqli_prepare(
            $conn,
            "SELECT OrgID FROM organization WHERE Status = 1 AND OrgAddress LIKE CONCAT('%', ?, '%') LIMIT 1"
        );
        mysqli_stmt_bind_param($wordStmt, 's', $word);
        mysqli_stmt_execute($wordStmt);
        $wordResult = mysqli_fetch_assoc(mysqli_stmt_get_result($wordStmt));
        mysqli_stmt_close($wordStmt);
        if ($wordResult) {
            $orgID = $wordResult['OrgID'];
            break;
        }
    }
}

// Kalau masih takde match, fallback: organisasi aktif pertama
if ($orgID === null) {
    $orgQuery = mysqli_query($conn, "SELECT OrgID FROM organization WHERE Status = 1 LIMIT 1");
    $orgRow   = $orgQuery ? mysqli_fetch_assoc($orgQuery) : null;
    $orgID    = $orgRow['OrgID'] ?? null;
}

// Kalau betul-betul takde organisasi aktif langsung, jangan crash —
// bagi error jelas kat user supaya admin boleh tambah/aktifkan organization.
if ($orgID === null) {
    $_SESSION['report_errors'] = [
        'Unable to submit report: no active organization is registered in the system yet. Please contact the administrator.'
    ];
    $_SESSION['report_old'] = $_POST;
    mysqli_close($conn);
    header('Location: AddReport.php');
    exit;
}

$status = 'Pending';

// ── INSERT INTO DATABASE ──
$query = "INSERT INTO report (ReportID, ResidentID, OrgID, PetName, Location, Description, Status, Photo)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param(
    $stmt,
    'ssssssss',
    $reportID, $residentID, $orgID, $reportName, $reportLocation, $reportDesc, $status, $photo
);
$success = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($success) {
    $_SESSION['report_success'] = "Your report ($reportID) has been submitted successfully!";
    mysqli_close($conn);
    header('Location: Report.php');
    exit;
} else {
    $_SESSION['report_errors'] = ['Something went wrong while saving your report. Please try again.'];
    $_SESSION['report_old']    = $_POST;
    mysqli_close($conn);
    header('Location: AddReport.php');
    exit;
}