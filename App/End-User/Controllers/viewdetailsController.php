<?php

require_once __DIR__ . "/../Models/db.php";
require_once __DIR__ . "/../Helpers/colorcoding.php";
require_once __DIR__ . "/../Helpers/amenityicon.php";

if (!isset($_SESSION['user'])) {
    header("Location: /LuneraHotel/App/Public");
    exit;
}

$roomId = $_GET['room'] ?? null;
if (!$roomId) {
    die("Invalid room ID");
}

$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$roomId]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$room) {
    die("Room not found");
}

$stmt = $pdo->prepare("SELECT amenity FROM amenities WHERE room_id = ?");
$stmt->execute([$roomId]);
$amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusClass = getStatusClass($room['status']);
$title = "View Room Details";

include __DIR__ . "/../Views/viewdetails.php";
