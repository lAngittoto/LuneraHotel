<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /LuneraHotel/App/Public');
    exit;
}


require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../Models/popularityModel.php";
require_once __DIR__ . "/../Models/allroomsModel.php";




$userEmail = $_SESSION['user']['email'];

//  Fetch booked rooms using the model
$bookedRooms = getRoomPopularity($pdo, $userEmail);

//  Page title (passed to view)
$title = "Popularity";

//  Load the view



require_once __DIR__. '/../Views/popularitybooking.php';