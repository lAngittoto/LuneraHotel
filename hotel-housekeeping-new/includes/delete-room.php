<?php
header('Content-Type: application/json');
require('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['roomId'] ?? '';
    
    if ($roomId) {
        // Delete associated tasks first from roomslunera_Hotel database
        $stmt = $connTasks->prepare("DELETE FROM tasks WHERE RoomID = ?");
        $stmt->bind_param('i', $roomId);
        $stmt->execute();
        $stmt->close();
        
        // Delete the room
        $stmt = $conn->prepare("DELETE FROM rooms WHERE RoomID = ?");
        $stmt->bind_param('i', $roomId);
        $success = $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => $success]);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Missing roomId']);
?>
