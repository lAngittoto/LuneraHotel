<?php
require_once __DIR__ . '/../../End-User/Models/db.php'; // adjust path

function getDashboardData() {
    global $pdo;

    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM rooms");
    $totalRooms = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT COUNT(*) AS available FROM rooms WHERE status='Available'");
    $availableRooms = $stmt->fetch(PDO::FETCH_ASSOC)['available'];

    return [$totalRooms, $availableRooms];
}
