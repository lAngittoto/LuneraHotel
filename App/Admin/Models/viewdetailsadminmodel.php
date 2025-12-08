<?php
// room functions

// Get single room by ID
function getRoomById($pdo, $roomId, $isAdmin = false)
{
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$roomId]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room && !$isAdmin) {
        // If not admin, convert "dirty" to "Unavailable"
        if (strtolower($room['status']) === 'dirty') {
            $room['status'] = 'Unavailable';
        }
    }

    return $room;
}

// Get room amenities
function getRoomAmenities($pdo, $roomId)
{
    $stmt = $pdo->prepare("SELECT amenity FROM amenities WHERE room_id = ?");
    $stmt->execute([$roomId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
