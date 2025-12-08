<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../Models/allbookingsModel.php";
require_once __DIR__ . "/../Models/allroomsModel.php";

// Admin wants to see all bookings (only active)
$bookedRooms = getAllBookings($pdo);

$title = "All Bookings";

// Load view
require_once __DIR__ . '/../Views/allbookings.php';
?>
