<?php
// LuneraHotel/App/API/getRoomDetails.php
header("Content-Type: application/json");
require_once __DIR__. '/../End-User/Models/db.php';
require_once __DIR__ . "/../End-User/Models/viewdetails.php";


$roomId = $_GET['room_id'] ?? null;

if (!$roomId) {
    echo json_encode(["success" => false, "message" => "Missing room_id"]);
    exit;
}

try {
    $room = getRoomById($pdo, $roomId);
    if (!$room) {
        echo json_encode(["success" => false, "message" => "Room not found"]);
        exit;
    }

    $amenities = getRoomAmenities($pdo, $roomId);

    echo json_encode([
        "success" => true,
        "room" => $room,
        "amenities" => $amenities
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
