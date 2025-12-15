<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/createRoomsModel.php';
require_once __DIR__ . '/../../config/Helpers/amenityicon.php';

if (!isset($_SESSION['user'])) {
    header("Location: /LuneraHotel/App/Public");
    exit;
}

// Fetch dropdown data
$roomTypes = getAllRoomTypes($pdo);
$allAmenities = getAllAmenities($pdo);

$errorMessage = null;
$successMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_room'])) {

    $room_number = trim($_POST['room_number']);
    $people = intval($_POST['people']);
    $floor = intval($_POST['floor']);

    // DEFAULT STATUS
    $status = $_POST['status'] ?? 'Under Maintenance';

    /* ================= VALIDATION ================= */

    // Room number validation
    if (!is_numeric($room_number) || intval($room_number) < 0) {
        $errorMessage = "Invalid room number!";
    }

    // Capacity limit
    elseif ($people > 6) {
        $errorMessage = "Maximum capacity is 6 people only!";
    }

    // Floor limit
    elseif ($floor > 4) {
        $errorMessage = "Maximum floor allowed is 4!";
    }

    // Duplicate room number check
    else {
        $stmt = $pdo->prepare("SELECT id FROM rooms WHERE room_number = ?");
        $stmt->execute([$room_number]);
        if ($stmt->fetch()) {
            $errorMessage = "Room number $room_number already exists!";
        }
    }

    /* ================= CREATE ================= */

    if (!$errorMessage) {

        // Image upload
        $imgPath = null;
        if (!empty($_FILES['img']['name'])) {
            $targetDir = __DIR__ . '/../../Public/images/';
            $filename = time() . '_' . basename($_FILES['img']['name']);
            move_uploaded_file($_FILES['img']['tmp_name'], $targetDir . $filename);
            $imgPath = 'images/' . $filename;
        }

        // Create room
        $roomId = createRoom($pdo, [
            'room_number' => $room_number,
            'room_type'   => $_POST['room_type'],
            'description' => $_POST['description'],
            'status'      => $status,
            'floor'       => $floor,
            'people'      => $people,
            'img'         => $imgPath,
            'type_name'   => $_POST['type_name']
        ]);

        // Amenities
        $selectedAmenities = $_POST['amenities'] ?? [];
        addRoomAmenities($pdo, $roomId, $selectedAmenities);

        $successMessage = "Room created successfully!";
    }
}

// Load view
include __DIR__ . '/../Views/createrooms.php';
