<?php
// roomsController.php - Updated controller

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/roomsModel.php';
require_once __DIR__ . '/../../config/roomdata.php';

if (!isset($_SESSION['user'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

// Check if user is admin
$isAdmin = false;
if (isset($_SESSION['user']['role'])) {
    $isAdmin = (strtolower($_SESSION['user']['role']) === 'admin');
}

// Get all floors
$floors = getAllFloors($pdo);

// Get rooms per floor
$roomsByFloor = [];
foreach($floors as $floor) {
    $roomsByFloor[$floor] = getRoomsByFloor($pdo, $floor, $isAdmin);
}

// Pass to view
include __DIR__ . '/../Views/rooms.php';
?>