<?php
require_once __DIR__ . '/../../config/db.php'; // adjust path

function getDashboardData() {
    global $pdo;

    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM rooms");
    $totalRooms = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT COUNT(*) AS available FROM rooms WHERE status='Available'");
    $availableRooms = $stmt->fetch(PDO::FETCH_ASSOC)['available'];

    $stmt = $pdo->query("SELECT COUNT(*) AS undermaintenance from rooms where status='under maintenance'");
    $undermaintenance = $stmt->fetch(PDO::FETCH_ASSOC)['undermaintenance'];

    $stmt = $pdo->query("SELECT COUNT(*) AS booked from bookings ");
    $bookings = $stmt->fetch(PDO::FETCH_ASSOC)['booked'];

    return [$totalRooms, $availableRooms,$bookings, $undermaintenance];
}
