<?php
function getRoomsSummary($pdo)
{
    $query = "
        SELECT 
            r.id,
            r.room_number,
            r.room_type,
            rt.type_name,
            r.floor,
            r.people,
            r.status,
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM tasks 
                    WHERE RoomID = r.id
                ) 
                THEN 1 
                ELSE 0 
            END AS has_cleaning_task
        FROM rooms r
        LEFT JOIN room_type rt 
            ON rt.id = r.room_type
        ORDER BY r.floor ASC, r.room_number ASC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
