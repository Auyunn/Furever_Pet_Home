<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');

    $conn = new mysqli("localhost", "root", "", "furever_pet_home");
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'DB connection failed']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $id     = $input['id']     ?? null;
    $status = $input['status'] ?? null;

    if (!$id || !$status) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Missing id or status']);
        exit;
    }

    $allowed = ['Approved', 'Rejected', 'Pending'];
    if (!in_array($status, $allowed, true)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid status']);
        exit;
    }

    try {
        $conn->begin_transaction();

        $update = $conn->prepare("UPDATE adopt_application SET Status = ? WHERE AdoptionID = ?");
        $update->bind_param("ss", $status, $id);
        if (!$update->execute()) throw new Exception($update->error);
        $update->close();

    // tukar return value -- tak perlu hide_pet flag
        $conn->commit();
        echo json_encode(['ok' => true]);
        exit;

        if ($status === 'Approved') {
            $set = $conn->prepare("
                UPDATE pet p
                JOIN adopt_application a ON p.PetID = a.PetID
                SET p.IsAvailable = 0
                WHERE a.AdoptionID = ?
            ");
            $set->bind_param("s", $id);
            if (!$set->execute()) throw new Exception($set->error);
            $set->close();
            $hide_pet = true;

            $rejectOthers = $conn->prepare("
                UPDATE adopt_application
                SET Status = 'Rejected'
                WHERE PetID = (SELECT PetID FROM adopt_application WHERE AdoptionID = ?)
                AND AdoptionID <> ?
                AND Status <> 'Rejected'
            ");
            $rejectOthers->bind_param("ss", $id, $id);
            if (!$rejectOthers->execute()) throw new Exception($rejectOthers->error);
            $rejectOthers->close();
        }

        $conn->commit();
        echo json_encode(['ok' => true, 'hide_pet' => $hide_pet]);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        exit;
    }
?>
