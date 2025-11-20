<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/roomsModel.php';
require_once __DIR__ . '/../../config/roomdata.php';

if (!isset($_SESSION['user'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

// Get all floors
$floors = getAllFloors($pdo);

// Get rooms per floor (exclude deactivated)
$roomsByFloor = [];
foreach($floors as $floor){
    $roomsByFloor[$floor] = getRoomsByFloor($pdo, $floor); // getRoomsByFloor filters out Deactivated rooms
}

// Pass to view
include __DIR__ . '/../Views/rooms.php';
