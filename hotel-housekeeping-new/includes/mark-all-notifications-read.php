<?php
header('Content-Type: application/json');
require('database.php');
session_start();

if (!isset($_SESSION['username']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$accType = $_SESSION['accType'] ?? '';

if ($accType === 'staff') {
    $housekeeperId = $_SESSION['housekeeperID'] ?? 0;
    
    if ($housekeeperId === 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid user']);
        exit;
    }
    
    $stmt = $conn->prepare("UPDATE notifications SET IsRead = 1 WHERE UserID = ?");
    $stmt->bind_param('i', $housekeeperId);
    $success = $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => $success]);
    
} elseif ($accType === 'admin') {
    $adminId = 1;
    
    $stmt = $conn->prepare("UPDATE admin_notifications SET IsRead = 1 WHERE AdminID = ?");
    $stmt->bind_param('i', $adminId);
    $success = $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => $success]);
    
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid account type']);
}
?>
