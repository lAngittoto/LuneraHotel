<?php
header('Content-Type: application/json');
require('database.php');

$staffFilter = $_GET['staff'] ?? '';
$roomFilter = $_GET['room'] ?? '';
$dateFrom = $_GET['dateFrom'] ?? '';
$dateTo = $_GET['dateTo'] ?? '';

// Build query for completed assignments using stored room/task info
$sql = "SELECT 
            a.AssignmentID,
            a.AssignedDate,
            a.Status,
            a.TimeCompleted,
            h.FullName as StaffName,
            a.RoomNumber,
            a.TaskDescription
        FROM webdb.assignments a
        LEFT JOIN webdb.housekeepers h ON a.HousekeeperID = h.HousekeeperID
        WHERE a.Status = 'Completed'";

$params = [];
$types = '';

if (!empty($staffFilter)) {
    $sql .= " AND h.FullName LIKE ?";
    $params[] = "%$staffFilter%";
    $types .= 's';
}

if (!empty($roomFilter)) {
    $sql .= " AND r.RoomNumber LIKE ?";
    $params[] = "%$roomFilter%";
    $types .= 's';
}

if (!empty($dateFrom)) {
    $sql .= " AND a.AssignedDate >= ?";
    $params[] = $dateFrom;
    $types .= 's';
}

if (!empty($dateTo)) {
    $sql .= " AND a.AssignedDate <= ?";
    $params[] = $dateTo;
    $types .= 's';
}

$sql .= " ORDER BY a.AssignedDate DESC LIMIT 100";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

$stmt->close();

echo json_encode(['success' => true, 'history' => $history]);
?>
