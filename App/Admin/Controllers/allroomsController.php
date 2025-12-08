<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/allroomsModel.php';
require_once __DIR__ . '/../../config/roomdata.php';

if (!isset($_SESSION['user'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}


$floors = getAllFloors($pdo);


$roomsByFloor = [];
foreach ($floors as $floor) {
    $rooms = getRoomsByFloor($pdo, $floor);
    if (!empty($rooms)) {
        $roomsByFloor[$floor] = $rooms;
    }
}

include __DIR__ . '/../Views/allrooms.php';
