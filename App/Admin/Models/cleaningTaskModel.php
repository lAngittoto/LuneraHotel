<?php
// File: App/Admin/Models/cleaningTaskModel.php
require_once __DIR__ . '/../../config/db.php';

// Create a cleaning task
function createCleaningTask($pdo, $pdoWeb, $roomId, $description, $housekeeperId = null) {
    // Get room info
    $stmtRoom = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmtRoom->execute([$roomId]);
    $room = $stmtRoom->fetch(PDO::FETCH_ASSOC);
    if (!$room) throw new Exception('Room not found');

    // Insert task
    $stmt = $pdo->prepare("INSERT INTO tasks (Description, RoomID) VALUES (?, ?)");
    $stmt->execute([$description, $roomId]);
    $taskId = $pdo->lastInsertId();
    if (!$taskId) throw new Exception('Failed to create task');

    // Assignment
    if ($housekeeperId) {
        $stmt = $pdoWeb->prepare("INSERT INTO assignments (HousekeeperID, TaskID, AssignedDate, Status) VALUES (?, ?, CURDATE(), 'Pending')");
        $stmt->execute([$housekeeperId, $taskId]);
    } else {
        $stmt = $pdoWeb->prepare("INSERT INTO assignments (HousekeeperID, TaskID, AssignedDate, Status) VALUES (NULL, ?, CURDATE(), 'Pending')");
        $stmt->execute([$taskId]);
    }

    return $taskId;
}

// Mark a room as cleaned and available, insert notification
function markRoomAvailable($pdo, $roomId) {
    // Update room status
    $stmt = $pdo->prepare("UPDATE rooms SET status='Available' WHERE id=?");
    $stmt->execute([$roomId]);

    // Get room info
    $stmtRoom = $pdo->prepare("SELECT * FROM rooms WHERE id=?");
    $stmtRoom->execute([$roomId]);
    $room = $stmtRoom->fetch(PDO::FETCH_ASSOC);
    if (!$room) return false;

    // Insert notification
    $stmtNotif = $pdo->prepare("
        INSERT INTO notifications (room_id, room_number, room_name, floor, message, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmtNotif->execute([
        $roomId,
        $room['room_number'],
        $room['room_type'],
        $room['floor'],
        "Room {$room['room_number']} is now available"
    ]);

    return true;
}
