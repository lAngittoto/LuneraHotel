<?php
header('Content-Type: application/json');
require('database.php');

// Get rooms that don't have active assignments (Pending or In Progress)
$sql = "SELECT r.id as RoomID, r.room_number as RoomNumber, r.floor as Floor, r.status as Status, r.room_type as RoomType 
        FROM roomslunera_hotel.rooms r
        WHERE NOT EXISTS (
            SELECT 1 
            FROM roomslunera_hotel.tasks t 
            JOIN webdb.assignments a ON t.TaskID = a.TaskID 
            WHERE t.RoomID = r.id 
            AND a.Status IN ('Pending', 'In Progress')
        )
        ORDER BY r.floor, r.room_number";
$result = $connTasks->query($sql);

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

echo json_encode(['success' => true, 'rooms' => $rooms]);
?>
