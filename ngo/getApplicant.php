<?php
session_start();
include __DIR__ . '/../db_connect.php';

$conn = new mysqli("localhost", "root", "", "furever_pet_home");
if ($conn->connect_error) {
    die("connection failed." . $conn->connect_error);
}

$row = null;

if (isset($_GET['id'])) {
    $adoptionID = trim($_GET['id']);

    $stmt = $conn->prepare("SELECT a.AdoptionID, a.Status, a.Reason, a.RequestDate,
                                   r.ResidentID, r.FirstName, r.LastName, r.Email, r.NumberPhone, r.Address,
                                   p.PetID, p.PetName, p.PetType, p.Age, p.Gender, p.Photo, p.IsAvailable, p.OrgID
                            FROM adopt_application a
                            JOIN resident r ON a.ResidentID = r.ResidentID
                            JOIN pet p ON a.PetID = p.PetID
                            WHERE a.AdoptionID = ?
                            LIMIT 1");
    if (!$stmt) {
        die("Prepared failed: " . $conn->error);
    }

    $stmt->bind_param("s", $adoptionID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
}

if (!$row) {
    echo '<div style="color:#c00">No applicant found</div>';
    exit;
}

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$requestDate = isset($row['RequestDate']) ? date("d/m/Y H:i", strtotime($row['RequestDate'])) : '-';
?>


<div class="applicant-details" data-current-id="<?= e($row['AdoptionID']) ?>">
    <button onclick="closePanel()" style="float:right;margin:4px;">× Close</button>
    <h3>Applicant Details</h3>
    <p><strong>Request ID:</strong> <?= e($row['AdoptionID']) ?></p>
    <p><strong>Request Date:</strong> <?= e($requestDate) ?></p>

    <h4>Pet</h4>
    <p><strong>Pet ID:</strong> <?= e($row['PetID']) ?></p>
    <p><strong>Pet Name:</strong> <?= e($row['PetName']) ?> (<?= e($row['PetType']) ?>)</p>
    <p><strong>Available:</strong> <?= $row['IsAvailable'] ? 'Yes' : 'No' ?></p>

    <h4>Adopter</h4>
    <p><strong>Name:</strong> <?= e($row['FirstName'] . ' ' . $row['LastName']) ?></p>
    <p><strong>Email:</strong> <?= e($row['Email']) ?></p>
    <p><strong>Phone:</strong> <?= e($row['NumberPhone']) ?></p>
    <p><strong>Address:</strong> <?= e($row['Address']) ?></p>

    <h4>Applicant</h4>
    <p><strong>Status:</strong> <?= e($row['Status']) ?></p>
    <p><strong>Reason:</strong><br><?= nl2br(e($row['Reason'])) ?></p>

    <div style="margin-top:12px">
        <button class="btn-approve" onclick="updateStatus('<?= e($row['AdoptionID']) ?>','Approved')">Approve</button>
        <button class="btn-reject" onclick="updateStatus('<?= e($row['AdoptionID']) ?>','Rejected')">Reject</button>
    </div>
</div>

