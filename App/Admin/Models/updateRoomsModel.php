<?php


function getAllRoomTypes($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT type_name FROM room_type ORDER BY type_name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    $room = getRoomById($pdo, $roomId);

}

function updateRoom($pdo, $roomId, $data) {
    // 1️⃣ Update descriptive room name in rooms table
    $stmt = $pdo->prepare("
        UPDATE rooms SET 
            room_number = ?, 
            room_type = ?, 
            description = ?, 
            status = ?, 
            floor = ?, 
            people = ?,
            img = COALESCE(?, img)
        WHERE id = ?
    ");
    $stmt->execute([
        $data['room_number'],
        $data['room_type'],   // descriptive name
        $data['description'],
        $data['status'],
        $data['floor'],
        $data['people'],
        $data['img'] ?? null,
        $roomId
    ]);

    // 2️⃣ Update category in room_type table
    $stmt = $pdo->prepare("DELETE FROM room_type WHERE id = ?");
    $stmt->execute([$roomId]);

    $stmt = $pdo->prepare("INSERT INTO room_type (id, type_name) VALUES (?, ?)");
    $stmt->execute([$roomId, $data['type_name']]); // dropdown category
}



function getRoomAmenities($pdo, $roomId) {
    $stmt = $pdo->prepare("SELECT amenity FROM amenities WHERE room_id = ?");
    $stmt->execute([$roomId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getAllAmenities($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT amenity FROM amenities");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function updateAmenities($pdo, $roomId, $amenities) {
    $stmt = $pdo->prepare("DELETE FROM amenities WHERE room_id = ?");
    $stmt->execute([$roomId]);

    $stmt = $pdo->prepare("INSERT INTO amenities (room_id, amenity) VALUES (?, ?)");
    foreach ($amenities as $amenity) {
        if (!empty($amenity)) {
            $stmt->execute([$roomId, $amenity]);
        }
    }
}
?>
