<?php
function getRoomsSummary($pdo)
{
    $sql = "
        SELECT 
            r.id,
            r.room_number,
            r.room_type,
            r.floor,
            r.people,
            r.status,
            rt.type_name,
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM tasks t 
                    WHERE t.RoomID = r.id
                ) 
                THEN 1 
                ELSE 0 
            END AS has_cleaning_task
        FROM rooms r
        LEFT JOIN room_type rt ON rt.id = r.id
        ORDER BY r.id ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
