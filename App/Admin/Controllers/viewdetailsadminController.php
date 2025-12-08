<?php


require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../Admin/Models/viewdetailsadminmodel.php";
require_once __DIR__ . "/../../config/Helpers/colorcoding.php";
require_once __DIR__ . "/../../config/Helpers/amenityicon.php";


if (!isset($_SESSION['user'])) {
    header("Location: /LuneraHotel/App/Public");
    exit;
}

$roomId = $_GET['id'] ?? null;

//  Validate room ID
if (!$roomId) {
    die("Invalid room ID");
}

//  Get room data from model
$room = getRoomById($pdo, $roomId);
if (!$room) {
    die("Room not found");
}

//  Get amenities from model
$amenities = getRoomAmenities($pdo, $roomId);

//  Status color
$statusClass = getStatusClass($room['status']);

//  Page title
$title = "View Room Details";

//  Load view
include __DIR__.'/../Views/viewdetialsadmin.php';