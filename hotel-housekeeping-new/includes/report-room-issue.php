<?php
ob_start();
require('database.php');
ob_end_clean();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomNumber = $_POST['roomNumber'] ?? '';
    $issueText = $_POST['issueText'] ?? '';
    
    if (empty($roomNumber) || empty($issueText)) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }
    
    // Get RoomID from RoomNumber
    $stmt = $connTasks->prepare("SELECT id as RoomID FROM roomslunera_hotel.rooms WHERE room_number = ?");
    $stmt->bind_param('s', $roomNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    $stmt->close();
    
    if (!$room) {
        echo json_encode(['success' => false, 'error' => 'Room not found']);
        exit;
    }
    
    $roomId = $room['RoomID'];
    $today = date('Y-m-d');
    
    // Insert maintenance request
    $stmt = $conn->prepare("INSERT INTO maintenancerequests (RoomID, Description, ReportedDate, Status) VALUES (?, ?, ?, 'Open')");
    $stmt->bind_param('iss', $roomId, $issueText, $today);
    
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
        $stmt->close();
        exit;
    }
    $stmt->close();
    
    // Update room status to Under Maintenance
    $stmt = $connTasks->prepare("UPDATE roomslunera_hotel.rooms SET status = 'Under Maintenance' WHERE id = ?");
    $stmt->bind_param('i', $roomId);
    $stmt->execute();
    $stmt->close();
    
    // Create notification for admin (optional, ignore if fails)
    $adminId = 1;
    $message = "New maintenance report for Room " . $roomNumber . ": " . $issueText;
    $severity = 'warning';
    $notifStmt = $conn->prepare("INSERT INTO admin_notifications (AdminID, Message, Severity, IsRead, CreatedAt) VALUES (?, ?, ?, 0, NOW())");
    if ($notifStmt) {
        $notifStmt->bind_param('iss', $adminId, $message, $severity);
        $notifStmt->execute();
        $notifStmt->close();
    }
    
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request method']);
exit;