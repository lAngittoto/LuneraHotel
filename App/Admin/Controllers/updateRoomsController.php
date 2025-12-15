<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/updateRoomsModel.php';
require_once __DIR__ . '/../Models/InventoryModel.php';
require_once __DIR__ . '/../../config/Helpers/amenityicon.php';

if (!isset($_SESSION['user'])) {
    header("Location: /LuneraHotel/App/Public");
    exit;
}

$roomId = $_GET['id'] ?? null;
if (!$roomId) die("Invalid room ID");

// Fetch data
$room = getRoomById($pdo, $roomId);
$roomAmenities = getRoomAmenities($pdo, $roomId);
$allAmenities = getAllAmenities($pdo);
$roomTypes = getAllRoomTypes($pdo);

$errorMessage = null;
$successMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_room'])) {

    $status = $_POST['status'] ?? 'Booked';
    $people = intval($_POST['people']); // Direct user input
    $room_number = trim($_POST['room_number']);
    $floor = intval($_POST['floor']);

    // Validations
    if (!is_numeric($room_number) || intval($room_number) < 0) {
        $errorMessage = "Invalid room number!";
    } elseif ($people > 6) {
        $errorMessage = "Maximum capacity is 6 people only!";
    } elseif ($floor > 4) {
        $errorMessage = "Maximum floor allowed is 4!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM rooms WHERE room_number = ? AND id != ?");
        $stmt->execute([$room_number, $roomId]);
        if ($stmt->fetch()) $errorMessage = "Room number $room_number is already in use!";
    }

    if (!$errorMessage) {
        // Image upload
        $imgPath = null;
        if (!empty($_FILES['img']['name'])) {
            $targetDir = __DIR__ . '/../../Public/images/';
            $imgPath = $targetDir . basename($_FILES['img']['name']);
            move_uploaded_file($_FILES['img']['tmp_name'], $imgPath);
            $imgPath = 'images/' . basename($_FILES['img']['name']);
        }

        // Update room
        updateRoom($pdo, $roomId, [
            'room_number' => $room_number,
            'room_type'   => $_POST['room_type'],
            'description' => $_POST['description'],
            'status'      => $status,
            'floor'       => $floor,
            'people'      => $people,
            'img'         => $imgPath,
            'type_name'   => $_POST['type_name']
        ]);

        // Only increment inventory if booked
        if ($status === 'Booked') {
            $inventoryModel = new InventoryModel($pdo);
            $inventoryModel->bookRoom($roomId, $people);
        }

        // Mark completed bookings if available
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

        $room = getRoomById($pdo, $roomId);
        $roomAmenities = getRoomAmenities($pdo, $roomId);
        $successMessage = "Room updated successfully!";
    }
}

include __DIR__ . '/../Views/updaterooms.php';
?>
