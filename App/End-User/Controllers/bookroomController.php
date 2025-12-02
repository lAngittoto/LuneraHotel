<?php


require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../Models/bookroom.php";



//  Check if user is logged in
if (!isset($_SESSION['user']) || empty($_SESSION['user']['email'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

$userEmail = $_SESSION['user']['email'];
$roomId = $_POST['room_id'] ?? null;

//  Validate room ID
if (!$roomId) {
    $_SESSION['error'] = "Invalid request.";
    header('Location: /LuneraHotel/App/Public/rooms');
    exit;
}

try {
    //  Call the model function to handle the booking logic
    bookRoom($pdo, $userEmail, $roomId);
    $_SESSION['success'] = "Room booked successfully!";
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

// Redirect to "My Bookings" page
header('Location: /LuneraHotel/App/Public/mybookings');
exit;
