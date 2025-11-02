<?php


require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/filterroomsmodel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Debug: check if $pdo exists
if (!isset($pdo)) {
    echo json_encode(['error' => 'PDO not initialized']);
    exit;
}

// Debug: see session
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Get filters
$status = $_GET['status'] ?? '';
$type   = $_GET['type'] ?? '';
$floor  = $_GET['floor'] ?? '';

try {
    $rooms = getFilteredRooms($pdo, $status, $type, $floor);
    echo json_encode($rooms);
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
