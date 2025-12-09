<?php
header('Content-Type: application/json');
require('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['roomId'] ?? '';
    $description = $_POST['description'] ?? '';
    $housekeeperId = $_POST['housekeeperId'] ?? null;
    
    if ($roomId && $description) {
        // Step 1: Create the task in roomslunera_hotel database
        $stmt = $connTasks->prepare("INSERT INTO tasks (Description, RoomID) VALUES (?, ?)");
        $stmt->bind_param('si', $description, $roomId);
        $success = $stmt->execute();
        $taskId = $connTasks->insert_id;
        $stmt->close();
        
        if (!$success) {
            echo json_encode(['success' => false, 'error' => 'Failed to create task']);
            exit;
        }
        
        // Step 2: Get room info to store with assignment
        $stmt = $connTasks->prepare("SELECT room_number FROM roomslunera_hotel.rooms WHERE id = ?");
        $stmt->bind_param('i', $roomId);
        $stmt->execute();
        $roomResult = $stmt->get_result();
        $roomInfo = $roomResult->fetch_assoc();
        $stmt->close();
        
        $roomNumber = $roomInfo['room_number'];
        
        // Step 3: Create the assignment in webdb with room/task info
        if ($housekeeperId) {
            // Assigned to specific staff
            $stmt = $conn->prepare("INSERT INTO assignments (HousekeeperID, TaskID, RoomNumber, TaskDescription, AssignedDate, Status) VALUES (?, ?, ?, ?, CURDATE(), 'Pending')");
            $stmt->bind_param('iiss', $housekeeperId, $taskId, $roomNumber, $description);
        } else {
            // Unassigned (NULL housekeeper)
            $stmt = $conn->prepare("INSERT INTO assignments (HousekeeperID, TaskID, RoomNumber, TaskDescription, AssignedDate, Status) VALUES (NULL, ?, ?, ?, CURDATE(), 'Pending')");
            $stmt->bind_param('iss', $taskId, $roomNumber, $description);
        }
        
        $success = $stmt->execute();
        $assignmentId = $conn->insert_id;
        $stmt->close();
        
        if (!$success) {
            echo json_encode(['success' => false, 'error' => 'Failed to create assignment']);
            exit;
        }
        
        // Step 4: Get room info for notifications
        $stmt = $connTasks->prepare("SELECT room_number as RoomNumber FROM roomslunera_hotel.rooms WHERE id = ?");
        $stmt->bind_param('i', $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();
        $stmt->close();
        
        if ($room) {
            // Create admin notification
            $adminId = 1;
            $adminMessage = "New assignment created: " . $description . " for Room " . $room['RoomNumber'];
            $notifStmt = $conn->prepare("INSERT INTO admin_notifications (AdminID, Message, Severity, IsRead, CreatedAt) VALUES (?, ?, 'info', 0, NOW())");
            $notifStmt->bind_param('is', $adminId, $adminMessage);
            $notifStmt->execute();
            $notifStmt->close();
            
            // Create staff notification if assigned
            if ($housekeeperId) {
                // Get UserID from users table
                $stmt = $conn->prepare("SELECT UserID FROM users WHERE HousekeeperID = ?");
                $stmt->bind_param('i', $housekeeperId);
                $stmt->execute();
                $result = $stmt->get_result();
                $userInfo = $result->fetch_assoc();
                $stmt->close();
                
                $userId = $userInfo['UserID'] ?? null;
                if ($userId) {
                    $staffMessage = "New assignment: " . $description . " for Room " . $room['RoomNumber'];
                    $notifStmt = $conn->prepare("INSERT INTO notifications (UserID, Message, Type, IsRead, CreatedAt) VALUES (?, ?, 'assignment', 0, NOW())");
                    $notifStmt->bind_param('is', $userId, $staffMessage);
                    $notifStmt->execute();
                    $notifStmt->close();
                }
            }
        }
        
        echo json_encode(['success' => true, 'taskId' => $taskId, 'assignmentId' => $assignmentId]);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Missing required fields']);
?>
