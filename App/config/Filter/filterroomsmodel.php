<?php


function getFilteredRooms($pdo, $status = '', $type = '', $floor = '')
{
    $query = "
        SELECT r.*, rt.type_name 
        FROM rooms r
        LEFT JOIN room_type rt ON r.id = rt.id
        WHERE r.status != 'Deactivated'
    ";
    $params = [];

    // Status filter
    if ($status !== "") {
        $query .= " AND r.status = :status";
        $params[':status'] = $status;
    }

    // Room category filter (type_name)
    if ($type !== "") {
        $query .= " AND rt.type_name = :type";
        $params[':type'] = $type;
    }

    // Floor filter
    if ($floor !== "") {
        $query .= " AND r.floor = :floor";
        $params[':floor'] = $floor;
    }

    $query .= " ORDER BY 
        CASE r.floor
            WHEN 'First Floor' THEN 1
            WHEN 'Second Floor' THEN 2
            WHEN 'Third Floor' THEN 3
            WHEN 'Fourth Floor' THEN 4
            WHEN 'Fifth Floor' THEN 5
            WHEN 'Sixth Floor' THEN 6
            WHEN 'Seventh Floor' THEN 7
            WHEN 'Eighth Floor' THEN 8
            WHEN 'Ninth Floor' THEN 9
            WHEN 'Tenth Floor' THEN 10
            ELSE 11
        END
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
