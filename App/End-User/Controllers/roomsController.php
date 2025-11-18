<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/roomsModel.php';
require_once __DIR__ . '/../../config/roomdata.php';

if (!isset($_SESSION['user'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

// Kunin lahat ng floors
$floors = getAllFloors($pdo);

// Kunin rooms per floor
$roomsByFloor = [];
foreach($floors as $floor){
    $roomsByFloor[$floor] = getRoomsByFloor($pdo, $floor);
}

// I-pass sa view
include __DIR__ . '/../Views/rooms.php';
