<?php
require('database.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['taskId'] ?? '';
    $housekeeperId = $_POST['housekeeperId'] ?? '';
    if ($taskId && $housekeeperId) {
        // Validate HousekeeperID exists
        $stmt = $conn->prepare("SELECT HousekeeperID FROM housekeepers WHERE HousekeeperID = ?");
        $stmt->bind_param('i', $housekeeperId);
        $stmt->execute();
        $result = $stmt->get_result();
        $housekeeper = $result->fetch_assoc();
        $stmt->close();
        
        if ($housekeeper) {
            $housekeeperId = $housekeeper['HousekeeperID'];
            
            // Check if assignment already exists for this taskeady exists for this task
            $stmt = $conn->prepare("SELECT AssignmentID FROM assignments WHERE TaskID = ? AND Status IN ('Pending', 'In Progress')");
            $stmt->bind_param('i', $taskId);
            $stmt->execute();
            $result = $stmt->get_result();
            $existingAssignment = $result->fetch_assoc();
            $stmt->close();
            
            $isReassignment = false;
            if ($existingAssignment) {
                // Update existing assignment (reassignment)
                $stmt = $conn->prepare("UPDATE assignments SET HousekeeperID = ?, AssignedDate = CURDATE() WHERE AssignmentID = ?");
                $stmt->bind_param('ii', $housekeeperId, $existingAssignment['AssignmentID']);
                $success = $stmt->execute();
                $stmt->close();
                $isReassignment = true;
            } else {
                // Create new assignment
                $stmt = $conn->prepare("INSERT INTO assignments (HousekeeperID, TaskID, AssignedDate, Status) VALUES (?, ?, CURDATE(), 'Pending')");
                $stmt->bind_param('ii', $housekeeperId, $taskId);
                $success = $stmt->execute();
                $stmt->close();
            }
            
            if ($success) {
                // Get UserID for notification (from users table, not HousekeeperID)
                $stmt = $conn->prepare("SELECT UserID FROM users WHERE HousekeeperID = ?");
                $stmt->bind_param('i', $housekeeperId);
                $stmt->execute();
                $result = $stmt->get_result();
                $userInfo = $result->fetch_assoc();
                $stmt->close();
                
                $userId = $userInfo['UserID'] ?? null;
                
                // Get room number and task description for notification
                $stmt = $connTasks->query("SELECT r.room_number as RoomNumber, t.Description 
                                      FROM roomslunera_hotel.tasks t 
                                      JOIN roomslunera_hotel.rooms r ON t.RoomID = r.id 
                                      WHERE t.TaskID = " . (int)$taskId);
                $taskInfo = $stmt->fetch_assoc();
                $stmt->close();
                
                $notificationCreated = false;
                $notificationError = '';
                if ($taskInfo && $userId) {
                    // Try to create notification for staff member (if table exists)
                    try {
                        $message = $isReassignment 
                            ? "Reassigned: " . $taskInfo['Description'] . " for Room " . $taskInfo['RoomNumber']
                            : "New assignment: " . $taskInfo['Description'] . " for Room " . $taskInfo['RoomNumber'];
                        $notifStmt = $conn->prepare("INSERT INTO notifications (UserID, Message, Type, IsRead, CreatedAt) VALUES (?, ?, 'assignment', 0, NOW())");
                        if ($notifStmt) {
                            $notifStmt->bind_param('is', $userId, $message);
                            if ($notifStmt->execute()) {
                                $notificationCreated = true;
                            } else {
                                $notificationError = $notifStmt->error;
                                error_log("Notification execute error: " . $notifStmt->error);
                            }
                            $notifStmt->close();
                        } else {
                            $notificationError = $conn->error;
                            error_log("Notification prepare error: " . $conn->error);
                        }
                    } catch (Exception $e) {
                        // Notification failed, but assignment succeeded - continue
                        $notificationError = $e->getMessage();
                        error_log("Notification error: " . $e->getMessage());
                    }
                } elseif (!$userId) {
                    $notificationError = "No UserID found for HousekeeperID " . $housekeeperId;
                }
                
                echo json_encode([
                    'success' => true, 
                    'notificationCreated' => $notificationCreated,
                    'notificationError' => $notificationError
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to create/update assignment']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid housekeeper']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing taskId or housekeeperId']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
