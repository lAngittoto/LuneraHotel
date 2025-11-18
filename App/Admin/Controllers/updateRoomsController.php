<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/updateRoomsModel.php';
require_once __DIR__ . '/../../config/Helpers/amenityicon.php';

if (!isset($_SESSION['user'])) {
    header("Location: /LuneraHotel/App/Public");
    exit;
}

$roomId = $_GET['id'] ?? null;
if (!$roomId) die("Invalid room ID");

// Fetch data for view
$room = getRoomById($pdo, $roomId);
$roomAmenities = getRoomAmenities($pdo, $roomId);
$allAmenities = getAllAmenities($pdo);
$roomTypes = getAllRoomTypes($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_room'])) {
$status = $_POST['status'] ?? 'Booked';

    // Image upload
    $imgPath = null;
    if (!empty($_FILES['img']['name'])) {
        $targetDir = __DIR__ . '/../../Public/images/';
        $imgPath = $targetDir . basename($_FILES['img']['name']);
        move_uploaded_file($_FILES['img']['tmp_name'], $imgPath);
        $imgPath = 'images/' . basename($_FILES['img']['name']); // relative path
    }

    // Update room
    updateRoom($pdo, $roomId, [
        'room_number' => $_POST['room_number'],
        'room_type' => $_POST['room_type'],
        'description' => $_POST['description'],
        'status' => $status,
        'floor' => $_POST['floor'],
        'people' => $_POST['people'],
        'img' => $imgPath
    ]);

    // If room is now Available, mark all Booked bookings as Completed
    if ($status === 'Available') {
        $stmt = $pdo->prepare("
            UPDATE bookings
            SET status = 'Completed'
            WHERE room_id = ? AND status = 'Booked'
        ");
        $stmt->execute([$roomId]);
    }

    // Update amenities
    $selectedAmenities = $_POST['amenities'] ?? [];
    updateAmenities($pdo, $roomId, $selectedAmenities);

    // Refresh data
    $room = getRoomById($pdo, $roomId);
    $roomAmenities = getRoomAmenities($pdo, $roomId);
    $successMessage = "Room updated successfully!";
}

// Load view
include __DIR__ . '/../Views/updaterooms.php';
?>
