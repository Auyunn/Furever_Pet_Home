<?php
    session_start();
    include __DIR__ . '/../db_connect.php';

    $data = json_decode(file_get_contents("php://input"), true);
    $petID = $data['petID'] ?? null;

    if (!$petID) {
        echo json_encode(["ok" => false, "error" => "Missing petID"]);
        exit;
    }

    if ($conn->connect_error) {
        echo json_encode(["ok" => false, "error" => "DB connection failed"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE pet SET IsAvailable=1 WHERE PetID=?");
    $stmt->bind_param("s", $petID);

    if ($stmt->execute()) {
        echo json_encode(["ok" => true]);
    } else {
        echo json_encode(["ok" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
?>
