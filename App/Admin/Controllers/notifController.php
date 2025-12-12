<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode([]);
    exit;
}

try {

    // Fetch all notifications including IMAGES
    $stmt = $pdo->prepare("
        SELECT 
            id,
            description,
            images,
            seen,
            completed_at,
            'Room Update' AS status
        FROM notifications
        ORDER BY id DESC
        LIMIT 50
    ");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count unseen notifications
    $stmt2 = $pdo->prepare("SELECT COUNT(*) AS unseen_count FROM notifications WHERE seen = 0");
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
