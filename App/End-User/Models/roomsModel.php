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

// Get all distinct floors from DB
function getAllFloors($pdo) {
    $stmt = $pdo->prepare("SELECT DISTINCT floor FROM rooms ORDER BY floor ASC");
    $stmt->execute();
    $floors = $stmt->fetchAll(PDO::FETCH_COLUMN);
    // Filter out null or empty floors
    return array_filter($floors, fn($f) => $f !== '' && $f !== null);
}

// Get rooms by floor (exclude deactivated)
// Modified to handle dirty status based on user role
function getRoomsByFloor($pdo, $floor, $isAdmin = false) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE floor = ? AND status != 'Deactivated' ORDER BY room_number ASC");
    $stmt->execute([$floor]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If not admin, convert "dirty" status to "unavailable"
    if (!$isAdmin) {
        foreach ($rooms as &$room) {
            if (strtolower($room['status']) === 'dirty') {
                $room['status'] = 'Unavailable';
            }
        }
        unset($room); // Break reference
    }
    
    return $rooms;
}
?>