<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Admin/Models/allroomsModel.php';
require_once __DIR__ . '/../../config/roomdata.php';
require_once __DIR__ .'/../../config/Helpers/amenityicon.php';
require_once __DIR__ .'/../../config/Helpers/colorcoding.php';
require_once __DIR__ .'/../../config/Helpers/correctgrammar.php';

if (!isset($_SESSION['user'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

// Check if a room ID is passed
$roomId = $_GET['id'] ?? null;
if (!$roomId) {
    die("No room selected.");
}

// Get specific room
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$roomId]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    die("Room not found.");
}
$statusClass = getStatusClass($room['status']);
// Optional: get amenities for this room
$stmt = $pdo->prepare("SELECT * FROM amenities WHERE room_id = ?");
$stmt->execute([$roomId]);
$amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load all rooms per floor (for listing if needed)
$floors = getAllFloors($pdo);
$roomsByFloor = [];
foreach ($floors as $floor) {
    $rooms = getRoomsByFloor($pdo, $floor);
    if (!empty($rooms)) {
        $roomsByFloor[$floor] = $rooms;
    }
}


// Pass everything to the view
include __DIR__ . '/../Views/viewdetails.php';
