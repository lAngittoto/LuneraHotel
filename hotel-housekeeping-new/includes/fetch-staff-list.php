<?php
require('database.php');

header('Content-Type: application/json');

$sql = "SELECT HousekeeperID, FullName, Email, Phone, UUID, AssignedFloor, Availability, HireDate, haveAccount
        FROM housekeepers 
        ORDER BY FullName";

$result = $conn->query($sql);

$staff = [];
while ($row = $result->fetch_assoc()) {
    $staff[] = $row;
}

echo json_encode(['success' => true, 'staff' => $staff]);
?>
