<?php
// Models/bookroom.php

function bookRoom($pdo, $email, $roomId)
{
    try {
        // Start transaction (avoid double booking)
        $pdo->beginTransaction();

        // Check if the room is still available (lock the row)
        $stmt = $pdo->prepare("SELECT status FROM rooms WHERE id = ? FOR UPDATE");
        $stmt->execute([$roomId]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room || trim($room['status']) !== 'Available') {
            throw new Exception("This room is not available for booking.");
        }

        // Insert booking record
        $stmt = $pdo->prepare("
            INSERT INTO bookings (user_email, room_id, status, booking_date)
            VALUES (?, ?, 'Booked', NOW())
        ");
        $stmt->execute([$email, $roomId]);

        // Update room status
        $stmt = $pdo->prepare("UPDATE rooms SET status = 'Booked' WHERE id = ?");
        $stmt->execute([$roomId]);

        // Commit transaction
        $pdo->commit();
        return true;

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e; // pass the error back to the controller
    }
}
