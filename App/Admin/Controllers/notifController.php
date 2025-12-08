<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

// Only admin can fetch notifications
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode([]);
    exit;
}

try {
    // Use completed_at instead of created_at
    $stmt = $pdo->prepare("SELECT * FROM notifications ORDER BY completed_at DESC LIMIT 20");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($notifications);
} catch (Exception $e) {
    error_log("Notification fetch error: " . $e->getMessage());
    echo json_encode([]);
}
