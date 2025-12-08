<?php
header('Content-Type: application/json');
require('database.php');
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$accType = $_SESSION['accType'] ?? '';

if ($accType === 'staff') {
    // Get staff notifications - need to get UserID from users table
    $housekeeperId = $_SESSION['housekeeperID'] ?? 0;
    
    if ($housekeeperId === 0) {
        echo json_encode(['success' => true, 'notifications' => [], 'unreadCount' => 0]);
        exit;
    }
    
    // Get UserID from users table using HousekeeperID
    $stmt = $conn->prepare("SELECT UserID FROM users WHERE HousekeeperID = ?");
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userInfo = $result->fetch_assoc();
    $stmt->close();
    
    $userId = $userInfo['UserID'] ?? 0;
    
    if ($userId === 0) {
        echo json_encode(['success' => true, 'notifications' => [], 'unreadCount' => 0]);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT NotificationID, Message, Type, IsRead, CreatedAt FROM notifications WHERE UserID = ? ORDER BY CreatedAt DESC LIMIT 20");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    $unreadCount = 0;
    
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
        if ($row['IsRead'] == 0) {
            $unreadCount++;
        }
    }
    $stmt->close();
    
    echo json_encode(['success' => true, 'notifications' => $notifications, 'unreadCount' => $unreadCount]);
    
} elseif ($accType === 'admin') {
    // Get admin notifications
    // For now, we'll use a placeholder AdminID of 1
    $adminId = 1;
    
    $stmt = $conn->prepare("SELECT NotificationID, Message, Severity, IsRead, CreatedAt FROM admin_notifications WHERE AdminID = ? ORDER BY CreatedAt DESC LIMIT 20");
    $stmt->bind_param('i', $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    $unreadCount = 0;
    
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
        if ($row['IsRead'] == 0) {
            $unreadCount++;
        }
    }
    $stmt->close();
    
    echo json_encode(['success' => true, 'notifications' => $notifications, 'unreadCount' => $unreadCount]);
    
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid account type']);
}
?>
