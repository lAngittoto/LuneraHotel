<?php

function getRoomById($pdo, $roomId) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$roomId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllRoomTypes($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT type_name FROM room_type ORDER BY type_name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateRoom($pdo, $roomId, $data) {
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
    return $stmt->execute([
        $data['room_number'],
        $data['room_type'],
        $data['description'],
        $data['status'],
        $data['floor'],
        $data['people'],
        $data['img'] ?? null,
        $roomId
    ]);
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
