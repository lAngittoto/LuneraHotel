<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /LuneraHotel/App/Public');
    exit;
}


require_once __DIR__ . "/../../End-User/Models/db.php";
require_once __DIR__ . "/../Models/allbookingsModel.php";




$userEmail = $_SESSION['user']['email'];

// ✅ Fetch booked rooms using the model
$bookedRooms = getAllBookings($pdo, $userEmail);

// ✅ Page title (passed to view)
$title = "All Bookings";

// ✅ Load the view



require_once __DIR__. '/../Views/allbookings.php';