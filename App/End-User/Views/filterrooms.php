<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/db.php';

$status = $_GET['status'] ?? '';
$type   = $_GET['type'] ?? '';
$floor  = $_GET['floor'] ?? '';

$query = "SELECT * FROM rooms WHERE 1=1";
$params = [];

// Status filter
if ($status !== "") {
    $query .= " AND status = :status";
    $params[':status'] = $status;
}

// Room type filter
if ($type !== "") {
    if ($type === "Triple") {
        $query .= " AND (room_type = 'Triple' OR room_type = 'Junior Suite')";
    } elseif ($type === "Family") {
        $query .= " AND (room_type = 'Family' OR room_type = 'Connecting Family Room')";
    } elseif ($type === "Deluxe") {
        $query .= " AND (room_type = 'Deluxe' OR room_type = 'Deluxe Double Room')";
    } elseif ($type === "Double") {
        $query .= " AND (room_type = 'Double' OR room_type = 'Standard Double Room')";
    } elseif ($type === "Single") {
        $query .= " AND (room_type = 'Single' OR room_type = 'Cozy Single Room')";
    } else {
        $query .= " AND room_type = :type";
        $params[':type'] = $type;
    }
}

// Floor filter
if ($floor !== "") {
    $query .= " AND floor = :floor";
    $params[':floor'] = $floor;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rooms);
exit;
?>
