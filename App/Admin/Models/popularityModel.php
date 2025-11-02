<?php
function getRoomPopularity($pdo)
{
    $stmt = $pdo->prepare("
        SELECT 
            r.*, 
            COUNT(b.id) AS total_bookings
        FROM rooms r
        LEFT JOIN bookings b ON r.id = b.room_id
        GROUP BY r.id
        ORDER BY total_bookings DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

