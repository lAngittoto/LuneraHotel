<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false]);
    exit;
}

try {
    // Mark all notifications as seen
    $stmt = $pdo->prepare("UPDATE notifications SET seen = 1 WHERE seen = 0");
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch(Exception $e) {
    error_log("Mark done error: ".$e->getMessage());
    echo json_encode(['success' => false]);
}
