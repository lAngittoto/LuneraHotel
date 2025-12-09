<?php
ob_start();
require('includes/database.php');
ob_end_clean();

header('Content-Type: application/json');

// Simulate the request
$_POST['roomNumber'] = '101';
$_POST['issueText'] = 'Test issue';

$roomNumber = $_POST['roomNumber'];
$issueText = $_POST['issueText'];

// Get RoomID from RoomNumber
$stmt = $connTasks->prepare("SELECT id as RoomID FROM roomslunera_hotel.rooms WHERE room_number = ?");
$stmt->bind_param('s', $roomNumber);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
$stmt->close();

if (!$room) {
    echo json_encode(['success' => false, 'error' => 'Room not found', 'roomNumber' => $roomNumber]);
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

// Update room status
$stmt = $connTasks->prepare("UPDATE roomslunera_hotel.rooms SET status = 'Under Maintenance' WHERE id = ?");
$stmt->bind_param('i', $roomId);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'roomId' => $roomId, 'message' => 'Test successful']);
?>
