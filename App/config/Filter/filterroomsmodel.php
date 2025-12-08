<?php

function convertFloor($floor) {
    $names = [
        1 => "First Floor",
        2 => "Second Floor",
        3 => "Third Floor",
        4 => "Fourth Floor",
        5 => "Fifth Floor",
        6 => "Sixth Floor",
        7 => "Seventh Floor",
        8 => "Eighth Floor",
        9 => "Ninth Floor",
        10 => "Tenth Floor"
    ];

    return $names[$floor] ?? "Floor $floor";
}

// Kunin lahat ng floors na may active rooms
function getActiveFloors($pdo)
{
    $stmt = $pdo->prepare("
        SELECT floor
        FROM rooms
        WHERE status != 'Deactivated'
        GROUP BY floor
        HAVING COUNT(*) > 0
        ORDER BY floor ASC
    ");
    $stmt->execute();

    $floors = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $activeFloors = [];
    foreach ($floors as $floor) {
        $rooms = getRoomsByFloor($pdo, $floor);
        if (!empty($rooms)) {
            $activeFloors[] = $floor;
        }
    }

    return $activeFloors;
}


function getRoomsByFloor($pdo, $floor, $isAdmin = false) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM rooms
        WHERE floor = ? AND status != 'Deactivated'
        ORDER BY room_number
    ");
    $stmt->execute([$floor]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$isAdmin) {
        foreach ($rooms as &$room) {
            if (strtolower($room['status']) === 'dirty') {
                $room['status'] = 'Unavailable';
            }
        }
        unset($room);
    }

    return $rooms;
}



// Added: dirty → Unavailable for non-admin
function getFilteredRooms($pdo, $status = '', $type = '', $floor = '', $isAdmin = false)
{
    $query = "
        SELECT r.*, rt.type_name 
        FROM rooms r
        LEFT JOIN room_type rt ON r.id = rt.id
        WHERE r.status != 'Deactivated'
    ";
    $params = [];

    if ($status !== "") {
        $query .= " AND r.status = :status";
        $params[':status'] = $status;
    }

    if ($type !== "") {
        $query .= " AND rt.type_name = :type";
        $params[':type'] = $type;
    }

    if ($floor !== "") {
        $query .= " AND r.floor = :floor";
        $params[':floor'] = $floor;
    }

    $query .= " ORDER BY r.floor ASC, r.room_number ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // CONVERT DIRTY → UNAVAILABLE (USER ONLY)
    if (!$isAdmin) {
        foreach ($rooms as &$room) {
            if (strtolower($room['status']) === 'dirty') {
                $room['status'] = 'Unavailable';
            }
        }
        unset($room);
    }

    return $rooms;
}
?>
