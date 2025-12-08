<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/cleaningTaskModel.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['room_id'] ?? null;
    $description = $_POST['description'] ?? '';
    $housekeeperId = $_POST['housekeeper_id'] ?? null;

    if (!$roomId || empty(trim($description))) {
        echo json_encode(['success' => false, 'message' => 'Room ID and description are required']);
        exit;
    }

    try {
        $taskId = createCleaningTask($pdo, $pdoWeb, $roomId, trim($description), $housekeeperId);
        echo json_encode([
            'success' => true, 
            'message' => 'Cleaning task created successfully',
            'task_id' => $taskId
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to create task: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}