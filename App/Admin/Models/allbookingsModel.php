<?php
function getAllBookings($pdo, $userEmail = null)
{
    $sql = "
        SELECT 
            r.*, 
            b.booking_date, 
            b.status AS booking_status, 
            b.user_email
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE b.status = 'Booked'   -- ipapakita lang ang aktibong bookings
        ORDER BY b.booking_date DESC
    ";

    if ($userEmail) {
        $sql .= " AND b.user_email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userEmail]);
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
