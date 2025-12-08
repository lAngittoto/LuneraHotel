<?php
require_once __DIR__ . '/../../config/db.php'; 

function getDashboardData() {
    global $pdo;

    // Total rooms (exclude deactivated)
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM rooms WHERE status != 'Deactivated'");
    $totalRooms = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Available rooms
    $stmt = $pdo->query("SELECT COUNT(*) AS available FROM rooms WHERE status='Available'");
    $availableRooms = $stmt->fetch(PDO::FETCH_ASSOC)['available'];

    // Rooms under maintenance
    $stmt = $pdo->query("SELECT COUNT(*) AS undermaintenance FROM rooms WHERE status='under maintenance'");
    $undermaintenance = $stmt->fetch(PDO::FETCH_ASSOC)['undermaintenance'];
    
    // Total active bookings (you can include all bookings, or only for non-deactivated rooms if needed)
    $stmt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status != 'Completed'");
    $bookings = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) AS dirty FROM rooms WHERE status='dirty'");
    $dirty = $stmt->fetch(PDO::FETCH_ASSOC)['dirty'];

    return [$totalRooms, $availableRooms, $bookings, $undermaintenance, $dirty];
}
