<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');

    $conn = new mysqli("localhost", "root", "", "furever_pet_home");
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'DB connection failed']);
        exit;
    }

    $input  = json_decode(file_get_contents('php://input'), true);
    $id     = $input['id']     ?? null;
    $status = $input['status'] ?? null;
    $reason = $input['reason'] ?? null;

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
        $hide_pet = false;

        // UPDATE adopt_application — guna column 'Reason'
        $updateApp = $conn->prepare("UPDATE adopt_application SET Status = ?, Reason = ? WHERE AdoptionID = ?");
        $updateApp->bind_param("sss", $status, $reason, $id);
        if (!$updateApp->execute()) throw new Exception("Error adopt_application: " . $updateApp->error);
        $updateApp->close();

        // UPDATE inbox
        $updateInbox = $conn->prepare("UPDATE inbox SET Status = ? WHERE AdoptionID = ?");
        $updateInbox->bind_param("ss", $status, $id);
        if (!$updateInbox->execute()) throw new Exception("Error inbox: " . $updateInbox->error);
        $updateInbox->close();

        if ($status === 'Approved') {
            // Sembunyikan pet
            $setPet = $conn->prepare("
                UPDATE pet p
                JOIN adopt_application a ON p.PetID = a.PetID
                SET p.IsAvailable = 0
                WHERE a.AdoptionID = ?
            ");
            $setPet->bind_param("s", $id);
            if (!$setPet->execute()) throw new Exception("Error hide pet: " . $setPet->error);
            $setPet->close();
            $hide_pet = true;

            // Tolak permohonan lain — guna column 'Reason'
            $rejectOthers = $conn->prepare("
                UPDATE adopt_application
                SET Status = 'Rejected', Reason = 'Pet has been adopted by someone else.'
                WHERE PetID = (SELECT PetID FROM adopt_application WHERE AdoptionID = ?)
                AND AdoptionID <> ?
                AND Status <> 'Rejected'
            ");
            $rejectOthers->bind_param("ss", $id, $id);
            if (!$rejectOthers->execute()) throw new Exception("Error reject others: " . $rejectOthers->error);
            $rejectOthers->close();

            // Update inbox untuk permohonan lain yang ditolak
            $rejectInboxOthers = $conn->prepare("
                UPDATE inbox 
                SET Status = 'Rejected'
                WHERE AdoptionID IN (
                    SELECT AdoptionID FROM adopt_application 
                    WHERE PetID = (SELECT PetID FROM adopt_application WHERE AdoptionID = ?)
                    AND AdoptionID <> ?
                )
            ");
            $rejectInboxOthers->bind_param("ss", $id, $id);
            if (!$rejectInboxOthers->execute()) throw new Exception("Error reject others inbox: " . $rejectInboxOthers->error);
            $rejectInboxOthers->close();
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
