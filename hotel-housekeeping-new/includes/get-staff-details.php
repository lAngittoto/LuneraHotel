<?php
require('database.php');

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'No ID provided']);
    exit;
}

$housekeeperId = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT HousekeeperID, FullName, Email, Phone, UUID, AssignedFloor, Availability, HireDate 
                        FROM housekeepers WHERE HousekeeperID = ?");
$stmt->bind_param('i', $housekeeperId);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
$stmt->close();

if ($staff) {
    echo json_encode(['success' => true, 'staff' => $staff]);
} else {
    echo json_encode(['success' => false, 'error' => 'Staff not found']);
}
?>
