<?php
function getRoomsSummary($pdo)
{
    $stmt = $pdo->prepare("
        SELECT 
            r.id,
            r.room_number,
            r.room_type,
            rt.type_name,
            r.floor,
            r.people,
            r.status,
            CASE WHEN EXISTS (SELECT 1 FROM tasks WHERE RoomID = r.id) THEN 1 ELSE 0 END as has_cleaning_task
        FROM rooms r
        LEFT JOIN room_type rt ON r.id = rt.id
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
