<?php
// File: App/Admin/Controllers/completeTaskController.php
session_start();
require_once __DIR__ . '/../Models/cleaningTaskModel.php';
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

// Only admin or housekeeper
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$taskId = $_POST['task_id'] ?? null;
if (!$taskId) {
    echo json_encode(['success' => false, 'message' => 'Task ID required']);
    exit;
}

try {
    // Get task to find room
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id=?");
    $stmt->execute([$taskId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$task) throw new Exception('Task not found');

    $roomId = $task['RoomID'];

    // Update assignment status to Done
    $stmtAssign = $pdo->prepare("UPDATE assignments SET Status='Done' WHERE TaskID=?");
    $stmtAssign->execute([$taskId]);

    // Mark room as available and insert notification
    $result = markRoomAvailable($pdo, $roomId);
    if (!$result) throw new Exception('Failed to update room or create notification');

    echo json_encode(['success' => true, 'message' => 'Task completed. Room available. Notification sent.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
