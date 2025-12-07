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

            -- Check if may cleaning task
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
            ON r.room_type = rt.id
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
