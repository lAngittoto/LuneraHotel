<?php
header('Content-Type: application/json');

require('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestId = $_POST['requestId'] ?? '';
    
    if (empty($requestId)) {
        echo json_encode(['success' => false, 'error' => 'Missing request ID']);
        exit;
    }
    
    // Update maintenance request status to Resolved
    $stmt = $conn->prepare("UPDATE maintenancerequests SET Status = 'Resolved' WHERE RequestID = ?");
    $stmt->bind_param('i', $requestId);
    
    if ($stmt->execute()) {
        // Get the room ID to update room status back to Available
        $getRoomStmt = $conn->prepare("SELECT RoomID FROM maintenancerequests WHERE RequestID = ?");
        $getRoomStmt->bind_param('i', $requestId);
        $getRoomStmt->execute();
        $result = $getRoomStmt->get_result();
        $request = $result->fetch_assoc();
        $getRoomStmt->close();
        
        if ($request) {
            $roomId = $request['RoomID'];
            
            // Update room status back to Available
            $updateRoomStmt = $connTasks->prepare("UPDATE roomslunera_hotel.rooms SET status = 'Available' WHERE id = ?");
            $updateRoomStmt->bind_param('i', $roomId);
            $updateRoomStmt->execute();
            $updateRoomStmt->close();
        }
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    
    $stmt->close();
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request method']);
exit;
