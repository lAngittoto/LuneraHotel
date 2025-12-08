<?php
require('database.php');

header('Content-Type: application/json');

// Fetch all maintenance requests with room information from cross-database JOIN
$sql = "SELECT m.RequestID, m.Description, m.ReportedDate, m.Status, 
               r.room_number as RoomNumber, r.floor as Floor
        FROM webdb.maintenancerequests m
        JOIN roomslunera_hotel.rooms r ON m.RoomID = r.id
        ORDER BY m.ReportedDate DESC, m.RequestID DESC";

$result = $conn->query($sql);

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

// Get statistics
$statsQuery = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN Status = 'Open' THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN Status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN Status = 'Resolved' THEN 1 ELSE 0 END) as resolved
               FROM maintenancerequests";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();

echo json_encode([
    'success' => true,
    'requests' => $requests,
    'stats' => $stats
]);
?>
