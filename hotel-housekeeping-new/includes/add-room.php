<?php
require('database.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$roomNumber = trim($_POST['roomNumber'] ?? '');
$roomType = trim($_POST['roomType'] ?? '');
$floor = intval($_POST['floor'] ?? 0);
$status = trim($_POST['status'] ?? 'Clean');

// Validate required fields
if (empty($roomNumber)) {
    echo json_encode(['success' => false, 'error' => 'Room number is required']);
    exit;
}

if (empty($roomType)) {
    echo json_encode(['success' => false, 'error' => 'Room type is required']);
    exit;
}

if ($floor <= 0) {
    echo json_encode(['success' => false, 'error' => 'Valid floor number is required']);
    exit;
}

// Check if room number already exists
$checkStmt = $conn->prepare("SELECT RoomID FROM rooms WHERE RoomNumber = ?");
$checkStmt->bind_param('s', $roomNumber);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Room number already exists']);
    $checkStmt->close();
    exit;
}
$checkStmt->close();

// Insert new room
$stmt = $conn->prepare("INSERT INTO rooms (RoomNumber, RoomType, Floor, Status) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssis', $roomNumber, $roomType, $floor, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Room added successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to add room: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
