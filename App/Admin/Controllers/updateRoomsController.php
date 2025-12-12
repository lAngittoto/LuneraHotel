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

// Fetch data
$room = getRoomById($pdo, $roomId);
$roomAmenities = getRoomAmenities($pdo, $roomId);
$allAmenities = getAllAmenities($pdo);
$roomTypes = getAllRoomTypes($pdo);

$errorMessage = null;

// UPDATE ROOM LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_room'])) {

    $status = $_POST['status'] ?? 'Booked';

    $people = intval($_POST['people']);
    $room_number = trim($_POST['room_number']);
    $floor = intval($_POST['floor']);

    // Validation: Room number must be numeric and non-negative
    if (!is_numeric($room_number) || intval($room_number) < 0) {
        $errorMessage = "Invalid room number!";
    }

    // Validation: Maximum 6 people
    elseif ($people > 6) {
        $errorMessage = "Maximum capacity is 6 people only!";
    }

    // Validation: Maximum floor 4
    elseif ($floor > 4) {
        $errorMessage = "Maximum floor allowed is 4!";
    }

    // Validation: Prevent duplicate room numbers
    else {
        $stmt = $pdo->prepare("SELECT id FROM rooms WHERE room_number = ? AND id != ?");
        $stmt->execute([$room_number, $roomId]);
        if ($stmt->fetch()) {
            $errorMessage = "Room number $room_number is already in use!";
        }
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
            'room_type' => $_POST['room_type'],
            'type_name'   => $_POST['type_name'],
            'description' => $_POST['description'],
            'status' => $status,
            'floor' => $floor,
            'people' => $people,
            'img' => $imgPath
        ]);

        // Auto-complete bookings if room is now available
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

include __DIR__ . '/../Views/updaterooms.php';
?>
