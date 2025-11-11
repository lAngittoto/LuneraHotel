<?php
function getRoomsSummary($pdo)
{
    $stmt = $pdo->prepare("
        SELECT 
            r.room_number,
            r.room_type,
            rt.type_name,
            r.floor,
            r.people,
            r.status
        FROM rooms r
        LEFT JOIN room_type rt ON r.id = rt.id
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
