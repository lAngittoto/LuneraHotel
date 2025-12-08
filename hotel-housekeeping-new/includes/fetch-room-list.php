<?php
require('database.php');

header('Content-Type: application/json');

$sql = "SELECT id as RoomID, room_number as RoomNumber, room_type as RoomType, floor as Floor, status as Status
        FROM roomslunera_hotel.rooms 
        ORDER BY floor, room_number";

$result = $connTasks->query($sql);

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

echo json_encode(['success' => true, 'rooms' => $rooms]);
?>
