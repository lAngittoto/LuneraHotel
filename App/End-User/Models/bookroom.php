<?php

require_once __DIR__ . "/../Models/db.php"; // DB connection

// ✅ Check user session
if (!isset($_SESSION['user']) || empty($_SESSION['user']['email'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

$userEmail = $_SESSION['user']['email'];
$roomId = $_POST['room_id'] ?? null;

// ✅ Validate room_id
if (!$roomId) {
    $_SESSION['error'] = "Invalid request.";
    header('Location: /LuneraHotel/App/Public/rooms');
    exit;
}

try {
    // ✅ Start transaction to avoid race conditions
    $pdo->beginTransaction();

    // ✅ Check if room is still Available (lock row)
    $stmt = $pdo->prepare("SELECT status FROM rooms WHERE id = ? FOR UPDATE");
    $stmt->execute([$roomId]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room || trim($room['status']) !== 'Available') {
        throw new Exception("This room is not available for booking.");
    }

    // ✅ Insert booking
    $stmt = $pdo->prepare("INSERT INTO bookings (user_email, room_id, status, booking_date) VALUES (?, ?, 'Booked', NOW())");
    $stmt->execute([$userEmail, $roomId]);

    // ✅ Update room status to "Booked"
    $stmt = $pdo->prepare("UPDATE rooms SET status = 'Booked' WHERE id = ?");
    $stmt->execute([$roomId]);

    $pdo->commit();

    $_SESSION['success'] = "Room booked successfully!";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = $e->getMessage();
}

// ✅ Redirect to LuneraHotel My Bookings
header('Location: /LuneraHotel/App/Public/mybookings');
exit;
?>
