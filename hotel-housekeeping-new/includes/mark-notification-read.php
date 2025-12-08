<?php
header('Content-Type: application/json');
require('database.php');
session_start();

if (!isset($_SESSION['username']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$notificationId = $_POST['notificationId'] ?? '';
$accType = $_SESSION['accType'] ?? '';

if (!$notificationId) {
    echo json_encode(['success' => false, 'error' => 'Missing notificationId']);
    exit;
}

if ($accType === 'staff') {
    $stmt = $conn->prepare("UPDATE notifications SET IsRead = 1 WHERE NotificationID = ?");
    $stmt->bind_param('i', $notificationId);
    $success = $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => $success]);
    
} elseif ($accType === 'admin') {
    $stmt = $conn->prepare("UPDATE admin_notifications SET IsRead = 1 WHERE NotificationID = ?");
    $stmt->bind_param('i', $notificationId);
    $success = $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => $success]);
    
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid account type']);
}
?>
