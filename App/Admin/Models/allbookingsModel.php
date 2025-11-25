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
        WHERE 1=1
    ";

    if ($userEmail) {
        $sql .= " AND b.user_email = ?";
    }

    // ORDER BY: active bookings first, then completed, then by date DESC
    $sql .= " ORDER BY 
                CASE 
                    WHEN b.status = 'Completed' THEN 1
                    ELSE 0
                END,
                b.booking_date DESC
            ";

    $stmt = $pdo->prepare($sql);

    if ($userEmail) {
        $stmt->execute([$userEmail]);
    } else {
        $stmt->execute();
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
