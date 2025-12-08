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


// DEACTIVATE ROOM
if (isset($_POST['deactivate_room'])) {
    $stmt = $pdo->prepare("UPDATE rooms SET status = 'Deactivated' WHERE id = ?");
    $stmt->execute([$roomId]);
    
    $_SESSION['success_message'] = "Room has been deactivated!";
    header("Location: /LuneraHotel/App/Public/managerooms");
    exit;
}

// REACTIVATE ROOM
if (isset($_POST['reactivate_room'])) {
    $stmt = $pdo->prepare("UPDATE rooms SET status = 'Available' WHERE id = ?");
    $stmt->execute([$roomId]);
    
    $_SESSION['success_message'] = "Room has been reactivated!";
    header("Location: /LuneraHotel/App/Public/managerooms");
    exit;
}





// UPDATE ROOM LOGIC

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_room'])) {

    $status = $_POST['status'] ?? 'Booked';
    
    // Debug: Log what status we received
    error_log("Received status: " . $status);
    error_log("All POST data: " . print_r($_POST, true));

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
        'room_number' => $_POST['room_number'],
        'room_type' => $_POST['room_type'],
        'type_name'   => $_POST['type_name'],
        'description' => $_POST['description'],
        'status' => $status,
        'floor' => $_POST['floor'],
        'people' => $_POST['people'],
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
