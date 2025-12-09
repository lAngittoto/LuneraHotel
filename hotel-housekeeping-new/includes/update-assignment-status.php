<?php
require('database.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomNumber = $_POST['roomNumber'] ?? '';
    $newStatus = $_POST['newStatus'] ?? '';
    
    if ($roomNumber && $newStatus) {
        // Get RoomID from RoomNumber
        $stmt = $connTasks->prepare("SELECT id as RoomID FROM roomslunera_hotel.rooms WHERE room_number = ?");
        $stmt->bind_param('s', $roomNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();
        $stmt->close();
        
        if (!$room) {
            echo json_encode(['success' => false, 'error' => 'Room not found']);
            exit;
        }
        
        $roomId = $room['RoomID'];
        
        // Update assignment status for this room using subquery
        $stmt = $conn->prepare("UPDATE webdb.assignments SET Status = ? WHERE TaskID IN (SELECT TaskID FROM roomslunera_hotel.tasks WHERE RoomID = ?) AND Status = 'Pending'");
        $stmt->bind_param('si', $newStatus, $roomId);
        $success = $stmt->execute();
        $stmt->close();
        
        // Also update room status to In Progress if starting cleaning
        if ($success && $newStatus === 'In Progress') {
            $stmt = $connTasks->prepare("UPDATE roomslunera_hotel.rooms SET status = 'In Progress' WHERE room_number = ?");
            $stmt->bind_param('s', $roomNumber);
            $stmt->execute();
            $stmt->close();
        }
        
        echo json_encode(['success' => $success]);
        exit;
    }
}
echo json_encode(['success' => false, 'error' => 'Missing parameters']);
?>
