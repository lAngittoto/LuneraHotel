<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode([]);
    exit;
}

try {
    // Fetch all notifications (no filtering)
    $stmt = $pdo->prepare("SELECT *, 'Room Update' AS status FROM notifications ORDER BY completed_at DESC LIMIT 50");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count unseen (still use original seen column)
    $stmt2 = $pdo->prepare("SELECT COUNT(*) as unseen_count FROM notifications WHERE seen = 0");
    $stmt2->execute();
    $unseen_count = $stmt2->fetch(PDO::FETCH_ASSOC)['unseen_count'];

    echo json_encode([
        'notifications' => $notifications,
        'unseen_count' => (int)$unseen_count
    ]);
} catch (Exception $e) {
    error_log("Notification fetch error: " . $e->getMessage());
    echo json_encode(['notifications'=>[], 'unseen_count'=>0]);
}
