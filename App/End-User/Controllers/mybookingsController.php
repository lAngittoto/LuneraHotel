<?php
session_start();
require_once __DIR__ . "/../Models/db.php";

// ✅ Check if user is logged in
if (!isset($_SESSION['user']) || empty($_SESSION['user']['email'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

$userEmail = $_SESSION['user']['email'];

// ✅ Fetch all bookings for this user, newest first
$stmt = $pdo->prepare("
    SELECT r.*, b.booking_date, b.status AS booking_status
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE LOWER(TRIM(b.user_email)) = LOWER(TRIM(?))
    ORDER BY b.booking_date DESC
");
$stmt->execute([$userEmail]);
$bookedRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Load the view
$title = "My Bookings";
include __DIR__ . "/../Views/mybookings.php";
