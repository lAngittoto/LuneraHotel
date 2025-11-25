<?php
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
        ORDER BY 
            CASE 
                WHEN b.status = 'Booked' THEN 0
                WHEN b.status = 'Completed' THEN 1
                WHEN b.status = 'Cancelled' THEN 2
                ELSE 3
            END,
            b.booking_date DESC
    ");
    $stmt->execute([$email]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
