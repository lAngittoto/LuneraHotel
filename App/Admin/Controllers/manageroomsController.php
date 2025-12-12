<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../Models/manageroomsModel.php";
require_once __DIR__ . "/../../config/Helpers/colorcoding.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

// DEACTIVATE ROOM
if (isset($_POST['deactivate_room'])) {
    $roomId = $_POST['deactivate_room'];
    $stmt = $pdo->prepare("UPDATE rooms SET status = 'Deactivated' WHERE id = ?");
    $stmt->execute([$roomId]);
    $_SESSION['success_message'] = "Room has been deactivated!";
    header("Location: managerooms");
    exit;
}

// REACTIVATE ROOM
if (isset($_POST['reactivate_room'])) {
    $roomId = $_POST['reactivate_room'];
    $stmt = $pdo->prepare("UPDATE rooms SET status = 'Available' WHERE id = ?");
    $stmt->execute([$roomId]);
    $_SESSION['success_message'] = "Room has been reactivated!";
    header("Location: managerooms");
    exit;
}

// GET ROOMS SUMMARY
$rooms = getRoomsSummary($pdo);

// FORCE AVAILABLE ROOMS THAT HAVE CLEANING TASK TO SHOW "Cleaning in Progress"
foreach ($rooms as &$room) {
    if ($room['status'] === 'Available' && $room['has_cleaning_task']) {
        $room['status'] = 'In Progress';
    }
}
unset($room);

// LOAD VIEW
require_once __DIR__ . '/../Views/managerooms.php';
?>
