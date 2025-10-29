<?php
// Controllers/ViewDetailsController.php

require_once __DIR__ . "/../../End-User/Models/db.php";
require_once __DIR__ . "/../../Admin/Models/viewdetailsadminmodel.php";
require_once __DIR__ . "/../../End-User/Helpers/colorcoding.php";
require_once __DIR__ . "/../../End-User/Helpers/amenityicon.php";

// ✅ Check session (assuming session_start() is handled globally)
if (!isset($_SESSION['user'])) {
    header("Location: /LuneraHotel/App/Public");
    exit;
}

$roomId = $_GET['id'] ?? null;

// ✅ Validate room ID
if (!$roomId) {
    die("Invalid room ID");
}

// ✅ Get room data from model
$room = getRoomById($pdo, $roomId);
if (!$room) {
    die("Room not found");
}

// ✅ Get amenities from model
$amenities = getRoomAmenities($pdo, $roomId);

// ✅ Status color
$statusClass = getStatusClass($room['status']);

// ✅ Page title
$title = "View Room Details";

// ✅ Load view
include __DIR__.'/../Views/viewdetialsadmin.php';