<?php

function getRoomsSummary($pdo)
{
    $query = "
        SELECT 
            r.id,
            r.room_number,
            r.room_type,          -- may value pa rin kung gusto mo reference
            rt.type_name,         -- actual display name
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
            ON rt.id = r.id      -- ✅ correct join
        ORDER BY r.floor ASC, r.room_number ASC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
