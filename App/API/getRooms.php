<?php
error_reporting(0);
ob_start();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Include necessary files

require_once __DIR__ . "/../End-User/Models/db.php";
require_once __DIR__ . "/../End-User/Controllers/roomdata.php"; 

try {
    // Fetch all rooms
    $stmt = $pdo->query("SELECT * FROM rooms");
    $roomsArray = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $room = new Rooms(
            $row['id'],
            $row['img'],           // keep as-is, no base URL
            $row['room_type'],
            $row['status'],
            $row['description'],
            $row['room_number'],
            $row['people'],
            $row['floor']
        );

        // Convert to array with lowercase keys (img is raw from DB)
        $roomsArray[] = [
            "id" => $room->id,
            "img" => $room->img,    // no base URL
            "room_type" => $room->RoomType,
            "status" => $room->status,
            "description" => $room->description,
            "room_number" => $room->RoomNumber,
            "people" => $room->people,
            "floor" => $room->floor
        ];
    }

    echo json_encode([
        "success" => true,
        "rooms" => $roomsArray
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}


?>
