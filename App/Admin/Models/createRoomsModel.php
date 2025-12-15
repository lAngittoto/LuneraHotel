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

    $stmt = $pdo->prepare("
        INSERT INTO rooms 
        (room_number, room_type, description, status, floor, people, img)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $data['room_number'],
        $data['room_type'],
        $data['description'],
        $data['status'],
        $data['floor'],
        $data['people'],
        $data['img']
    ]);

    $roomId = $pdo->lastInsertId();

    // Save room type
    if (!empty($data['type_name'])) {
        $stmt = $pdo->prepare("INSERT INTO room_type (id, type_name) VALUES (?, ?)");
        $stmt->execute([$roomId, $data['type_name']]);
    }

    return $roomId;
}

function addRoomAmenities($pdo, $roomId, $amenities) {
    $stmt = $pdo->prepare("INSERT INTO amenities (room_id, amenity) VALUES (?, ?)");
    foreach ($amenities as $amenity) {
        $stmt->execute([$roomId, $amenity]);
    }
}
