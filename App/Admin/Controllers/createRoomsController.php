<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/createRoomsModel.php';
require_once __DIR__ . '/../../config/Helpers/amenityicon.php';

if (!isset($_SESSION['user'])) {
    header("Location: /LuneraHotel/App/Public");
    exit;
}

// Fetch data for dropdowns
$roomTypes = getAllRoomTypes($pdo);
$allAmenities = getAllAmenities($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_room'])) {

    // Use submitted status or default to Under Maintenance
    $status = isset($_POST['status']) && $_POST['status'] !== '' ? $_POST['status'] : 'Under Maintenance'; 


    // Image upload
    $imgPath = null;
    if (!empty($_FILES['img']['name'])) {
        $targetDir = __DIR__ . '/../../Public/images/';
        $imgPath = $targetDir . basename($_FILES['img']['name']);
        move_uploaded_file($_FILES['img']['tmp_name'], $imgPath);
        $imgPath = 'images/' . basename($_FILES['img']['name']); // relative path
    }

    // Create room
    $roomId = createRoom($pdo, [
        'room_number' => $_POST['room_number'],
        'room_type' => $_POST['room_type'],
        'description' => $_POST['description'],
        'status' => $status,
        'floor' => $_POST['floor'],
        'people' => $_POST['people'],
        'img' => $imgPath,
         'type_name'   => $_POST['type_name'] 
    ]);

    // Add amenities
    $selectedAmenities = $_POST['amenities'] ?? [];
    addRoomAmenities($pdo, $roomId, $selectedAmenities);

    $successMessage = "Room created successfully!";
}

// Load view
include __DIR__ . '/../Views/createrooms.php';
?>
