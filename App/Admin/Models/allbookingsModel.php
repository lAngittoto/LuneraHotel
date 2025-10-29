<?php
function getAllBookings($pdo)
{
    $stmt = $pdo->prepare("
        SELECT 
            r.*, 
            b.booking_date, 
            b.status AS booking_status, 
            b.user_email
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        ORDER BY b.booking_date DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
