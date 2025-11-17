<?php

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../Models/viewdetails.php";
require_once __DIR__ . "/../../config/Helpers/colorcoding.php";
require_once __DIR__ . "/../../config/Helpers/amenityicon.php";


if (!isset($_SESSION['user'])) {
    header("Location: /LuneraHotel/App/Public");
    exit;
}

$roomId = $_GET['room'] ?? null;


if (!$roomId) {
    die("Invalid room ID");
}


$room = getRoomById($pdo, $roomId);
if (!$room) {
    die("Room not found");
}


$amenities = getRoomAmenities($pdo, $roomId);


$statusClass = getStatusClass($room['status']);


$title = "View Room Details";


include __DIR__ . "/../Views/viewdetails.php";
