<?php
header('Content-Type: application/json');
require('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['roomId'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if ($roomId && $description) {
        // Create new task in roomslunera_Hotel database
        $stmt = $connTasks->prepare("INSERT INTO tasks (Description, RoomID) VALUES (?, ?)");
        $stmt->bind_param('si', $description, $roomId);
        $success = $stmt->execute();
        $taskId = $connTasks->insert_id;
        $stmt->close();
        
        if ($success) {
            // Get room number for notification
            $stmt = $conn->prepare("SELECT RoomNumber FROM rooms WHERE RoomID = ?");
            $stmt->bind_param('i', $roomId);
            $stmt->execute();
            $result = $stmt->get_result();
            $room = $result->fetch_assoc();
            $stmt->close();
            
            if ($room) {
                // Create notification for admin (using AdminID = 1)
                $adminId = 1;
                $message = "New task created: " . $description . " for Room " . $room['RoomNumber'];
                $notifStmt = $conn->prepare("INSERT INTO admin_notifications (AdminID, Message, Severity, IsRead, CreatedAt) VALUES (?, ?, 'info', 0, NOW())");
                $notifStmt->bind_param('is', $adminId, $message);
                $notifStmt->execute();
                $notifStmt->close();
            }
            
            echo json_encode(['success' => true, 'taskId' => $taskId]);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'error' => 'Missing parameters or creation failed']);
?>
