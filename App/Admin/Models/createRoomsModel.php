<?php

// Fetch all room categories/types
function getAllRoomTypes($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT type_name FROM room_type ORDER BY type_name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch all amenities
function getAllAmenities($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT amenity FROM amenities");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Create a new room
function createRoom($pdo, $data) {

    // Convert empty to NULL
    $data['floor'] = $data['floor'] !== '' ? $data['floor'] : null;
    $data['people'] = $data['people'] !== '' ? $data['people'] : null;

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
    $roomId = $pdo->lastInsertId();

    //  Insert type_name into room_type table
    if (!empty($data['type_name'])) {
        $stmt = $pdo->prepare("INSERT INTO room_type (id, type_name) VALUES (?, ?)");
        $stmt->execute([$roomId, $data['type_name']]);
    }

    return $roomId;
}

// Add amenities for a room
function addRoomAmenities($pdo, $roomId, $amenities) {
    $stmt = $pdo->prepare("INSERT INTO amenities (room_id, amenity) VALUES (?, ?)");
    foreach ($amenities as $amenity) {
        if (!empty($amenity)) {
            $stmt->execute([$roomId, $amenity]);
        }
    }
}


function getRoomById($pdo, $roomId) {
    $stmt = $pdo->prepare("
        SELECT r.*, rt.type_name
        FROM rooms r
        LEFT JOIN room_type rt ON r.id = rt.id
        WHERE r.id = ?
    ");
    $stmt->execute([$roomId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
