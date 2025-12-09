<?php
require('database.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$startDay = $_POST['startDay'] ?? '';
$endDay = $_POST['endDay'] ?? '';
$startTime = $_POST['startTime'] ?? '';
$endTime = $_POST['endTime'] ?? '';

// Validate required fields
if (empty($startDay) || empty($endDay) || empty($startTime) || empty($endTime)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

// Validate days are valid enum values
$validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
if (!in_array($startDay, $validDays) || !in_array($endDay, $validDays)) {
    echo json_encode(['success' => false, 'error' => 'Invalid day selection']);
    exit;
}

// Validate times are valid enum values
$validTimes = [
    '12:00 AM', '01:00 AM', '02:00 AM', '03:00 AM', '04:00 AM', '05:00 AM',
    '06:00 AM', '07:00 AM', '08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM',
    '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM',
    '06:00 PM', '07:00 PM', '08:00 PM', '09:00 PM', '10:00 PM', '11:00 PM'
];

$validEndTimes = array_merge(['12:00 AM'], array_slice($validTimes, 1));

if (!in_array($startTime, $validTimes)) {
    echo json_encode(['success' => false, 'error' => 'Invalid start time']);
    exit;
}

if (!in_array($endTime, $validEndTimes)) {
    echo json_encode(['success' => false, 'error' => 'Invalid end time']);
    exit;
}

// Check if this exact shift already exists
$stmt = $conn->prepare("SELECT ShiftID FROM shifts WHERE StartDay = ? AND EndDay = ? AND StartTime = ? AND EndTime = ?");
$stmt->bind_param('ssss', $startDay, $endDay, $startTime, $endTime);
$stmt->execute();
$result = $stmt->get_result();
$existing = $result->fetch_assoc();
$stmt->close();

if ($existing) {
    echo json_encode(['success' => false, 'error' => 'This shift already exists']);
    exit;
}

// Insert the new shift
$stmt = $conn->prepare("INSERT INTO shifts (StartDay, EndDay, StartTime, EndTime) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $startDay, $endDay, $startTime, $endTime);

if ($stmt->execute()) {
    $shiftId = $conn->insert_id;
    $stmt->close();
    echo json_encode([
        'success' => true, 
        'shiftId' => $shiftId,
        'message' => 'Shift created successfully'
    ]);
} else {
    $stmt->close();
    echo json_encode(['success' => false, 'error' => 'Failed to create shift: ' . $conn->error]);
}
?>
