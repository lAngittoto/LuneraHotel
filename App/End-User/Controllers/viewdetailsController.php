<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/viewdetails.php';
require_once __DIR__ . '/../../config/Helpers/amenityicon.php';
require_once __DIR__ . '/../../config/Helpers/colorcoding.php';
require_once __DIR__ . '/../../config/Helpers/correctgrammar.php';



// Redirect if user not logged in
if (!isset($_SESSION['user'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

$model = new RoomModel($pdo);

// Check if a room ID is passed
$roomId = $_GET['id'] ?? null;
if (!$roomId) {
    die("No room selected.");
}

// Handle Report Issue form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id'], $_POST['description'])) {
    $result = $model->reportIssue($_POST['room_id'], $_POST['description']);
    $_SESSION['message'] = $result['message'];
    header("Location: /LuneraHotel/App/Public/index.php?page=viewdetails&id=" . $_POST['room_id']);
    exit;
}

// Fetch room and amenities
$room = $model->getRoomById($roomId);
if (!$room) {
    die("Room not found.");
}
$amenities = $model->getRoomAmenities($roomId);

// Optionally load rooms by floor
function getAllFloors($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT floor FROM rooms ORDER BY floor ASC");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getRoomsByFloor($pdo, $floor) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE floor = ?");
    $stmt->execute([$floor]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$floors = getAllFloors($pdo);
$roomsByFloor = [];
foreach ($floors as $floor) {
    $rooms = getRoomsByFloor($pdo, $floor);
    if (!empty($rooms)) {
        $roomsByFloor[$floor] = $rooms;
    }
}

// Load view
include __DIR__ . '/../Views/viewdetails.php';
