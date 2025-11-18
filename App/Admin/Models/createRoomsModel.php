<?php

function getAllRoomTypes($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT type_name FROM room_type ORDER BY type_name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllAmenities($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT amenity FROM amenities");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function createRoom($pdo, $data) {
    // Insert room
    $stmt = $pdo->prepare("
        INSERT INTO rooms (room_number, room_type, description, status, floor, people, img)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $data['room_number'],
        $data['room_type'],
        $data['description'],
        $data['status'],
        $data['floor'],
        $data['people'],
        $data['img'] ?? null
    ]);

    // Get last inserted room id
    return $pdo->lastInsertId();
}

function addRoomAmenities($pdo, $roomId, $amenities) {
    $stmt = $pdo->prepare("INSERT INTO amenities (room_id, amenity) VALUES (?, ?)");
    foreach ($amenities as $amenity) {
        if (!empty($amenity)) {
            $stmt->execute([$roomId, $amenity]);
        }
    }
}
?>
