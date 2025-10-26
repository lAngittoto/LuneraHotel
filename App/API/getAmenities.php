<?php
// LuneraHotel/App/API/getAmenities.php
header("Content-Type: application/json");
require_once __DIR__. '/../End-User/Models/db.php';

$roomId = $_GET['room_id'] ?? null;

try {
    if ($roomId) {
        // Fetch amenities for a specific room
        $stmt = $pdo->prepare("SELECT amenity FROM amenities WHERE room_id = ?");
        $stmt->execute([$roomId]);
    } else {
        // Fetch all available amenities
        $stmt = $pdo->query("SELECT DISTINCT amenity FROM amenities");
    }

    $amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "amenities" => $amenities
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
