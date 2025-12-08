<?php
require("database.php");

$floor = (int)$_GET['floor'];

// Housekeepers assigned to the floor
$onFloorRes = $conn->query("SELECT HousekeeperID, FullName, Availability FROM housekeepers WHERE AssignedFloor=$floor");
$onFloor = $onFloorRes->fetch_all(MYSQLI_ASSOC);

// Other housekeepers
$otherRes = $conn->query("SELECT HousekeeperID, FullName, Availability FROM housekeepers WHERE AssignedFloor<>$floor OR AssignedFloor IS NULL");
$other = $otherRes->fetch_all(MYSQLI_ASSOC);

// Return with backwards compatible key names for JS
foreach ($onFloor as &$h) {
    $h['StaffMember'] = $h['FullName'];
    $h['StaffID'] = $h['HousekeeperID'];
    $h['Availability'] = $h['Availability'] ?? 'Available';
}
foreach ($other as &$h) {
    $h['StaffMember'] = $h['FullName'];
    $h['StaffID'] = $h['HousekeeperID'];
    $h['Availability'] = $h['Availability'] ?? 'Available';
}

echo json_encode(['onFloor'=>$onFloor,'other'=>$other]);
?>