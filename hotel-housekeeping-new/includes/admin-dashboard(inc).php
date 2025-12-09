<?php
require("database.php");

$sql = "SELECT COUNT(*) AS total_rooms FROM roomslunera_hotel.rooms";
$result = $connTasks->query($sql);
$row = $result->fetch_assoc();
$totalRooms = $row['total_rooms'];

$sql = "SELECT status as Status, COUNT(*) AS total FROM roomslunera_hotel.rooms GROUP BY status";
$result = $connTasks->query($sql);

$counts = [
    "Clean" => 0,
    "Dirty" => 0,
    "In Progress" => 0,
    "Maintenance" => 0
];

while ($row = $result->fetch_assoc()) {
    // Capture counts directly
    $status = $row['Status'];
    $counts[$status] = $row['total'];
}

// Treat 'Available' rooms as 'Clean' for dashboard overview
if (isset($counts['Available'])) {
    $counts['Clean'] = $counts['Available'];
}

// Map 'Under Maintenance' to 'Maintenance' key
if (isset($counts['Under Maintenance'])) {
    $counts['Maintenance'] = $counts['Under Maintenance'];
}
?>