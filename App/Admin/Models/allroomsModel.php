<?php

// Convert floor number to readable name
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

// Get all distinct floors from DB
function getAllFloors($pdo) {
    $stmt = $pdo->prepare("SELECT DISTINCT floor FROM rooms ORDER BY floor ASC");
    $stmt->execute();
    $floors = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return array_filter($floors, fn($f) => $f !== '' && $f !== null);
}

// Get rooms by floor (INCLUDE deactivated)
function getRoomsByFloor($pdo, $floor, $isAdmin = false) {

    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE floor = ? ORDER BY room_number ASC");
    $stmt->execute([$floor]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rooms as &$room) {

        // replace Deactivated → Out of Order
        if (strtolower($room['status']) === 'deactivated') {
            $room['status'] = 'Out of Order';
        }

        // DO NOT TOUCH — cleaning flow intact
        if (!$isAdmin && strtolower($room['status']) === 'dirty') {
            $room['status'] = 'Unavailable';
        }
    }
    unset($room);

    return $rooms;
}
?>
