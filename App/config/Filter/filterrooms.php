<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/filterroomsmodel.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Debug: ensure $pdo exists
if (!isset($pdo)) {
    echo json_encode(['error' => 'PDO not initialized']);
    exit;
}


// Get filter parameters from GET

$status = $_GET['status'] ?? '';
$type   = $_GET['type'] ?? '';
$floor  = $_GET['floor'] ?? ''; // floor is numeric (1,2,3,...)


// Fetch rooms based on filters

try {
    $rooms = getFilteredRooms($pdo, $status, $type, $floor);
    echo json_encode($rooms);
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
