<?php
// Controllers/MyBookingsController.php

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../Models/mybookings.php";

require_once __DIR__ . '/../../Admin/Models/allroomsModel.php'; // path depende sa project mo



//  Check if user is logged in
if (!isset($_SESSION['user']) || empty($_SESSION['user']['email'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

$userEmail = $_SESSION['user']['email'];

//  Fetch booked rooms using the model
$bookedRooms = getUserBookings($pdo, $userEmail);

//  Page title (passed to view)
$title = "My Bookings";

//  Load the view
include __DIR__ . "/../Views/mybookings.php";
