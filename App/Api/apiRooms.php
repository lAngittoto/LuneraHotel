<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

$dbFile = __DIR__ . '/../config/db.php';
if (!file_exists($dbFile)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database file missing.'
    ]);
    exit;
}

require_once $dbFile; // $pdo = main DB connection

try {
    // --- 1. Fetch rooms from main DB ---
    $stmt = $pdo->query("SELECT * FROM rooms ORDER BY id ASC");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- 2. Fetch prices from integrated_hotel_db ---
    $pdo2 = new PDO("mysql:host=localhost;dbname=integrated_hotel_db;charset=utf8", "root", "P@ssw0rd");
    $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt2 = $pdo2->query("SELECT Price FROM roomtypes ORDER BY TypeID ASC");
    $pricesData = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    // --- 3. Fetch booked rooms ---
    $stmt3 = $pdo->query("
        SELECT room_id 
        FROM bookings 
        WHERE status = 'Booked'
    ");
    $bookedRooms = $stmt3->fetchAll(PDO::FETCH_COLUMN);

    // --- 4. Merge rooms with prices & booking status ---
$roomsArray = [];
foreach ($rooms as $index => $room) {
    // If booked OR the status is not 'available', set to 'unavailable'
    $status = in_array($room['id'], $bookedRooms) || strtolower($room['status']) !== 'available' 
        ? 'Unavailable' 
        : 'available';

    $roomsArray[] = [
        'id' => $room['id'],
        'RoomNumber' => $room['room_number'] ?? 'N/A',
        'RoomType' => $room['room_type'] ?? 'Room',
        'floor' => $room['floor'] ?? 0,
        'status' => $status, // now dirty / booked / maintenance → 'unavailable'
        'img' => $room['img'] ?? 'Public/images/default.png',
        'description' => $room['description'] ?? '',
        'people' => $room['people'] ?? 2,
        'price' => isset($pricesData[$index]) ? floatval($pricesData[$index]) : 0
    ];
}


    // --- 5. Return JSON ---
    echo json_encode([
        'success' => true,
        'rooms' => $roomsArray
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
