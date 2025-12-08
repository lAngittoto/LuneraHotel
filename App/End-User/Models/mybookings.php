<?php
// Models/mybookings.php

function getUserBookings($pdo, $email)
{
    $stmt = $pdo->prepare("
        SELECT 
            r.*, 
            b.booking_date, 
            b.status AS booking_status
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE LOWER(TRIM(b.user_email)) = LOWER(TRIM(?))
        ORDER BY b.booking_date DESC
    ");
    $stmt->execute([$email]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
