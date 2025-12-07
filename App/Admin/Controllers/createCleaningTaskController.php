<?php
// File: App/Admin/Controllers/createTaskController.php
session_start();
require_once __DIR__ . '/../Models/cleaningTaskModel.php';
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

// Only admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$roomId = $_POST['room_id'] ?? null;
$description = $_POST['description'] ?? '';
$housekeeperId = $_POST['housekeeper_id'] ?? null;

if (!$roomId || empty(trim($description))) {
    echo json_encode(['success' => false, 'message' => 'Room ID and description required']);
    exit;
}

try {
    $taskId = createCleaningTask($pdo, $pdoWeb, $roomId, trim($description), $housekeeperId);
    echo json_encode(['success' => true, 'message' => 'Task created', 'task_id' => $taskId]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
