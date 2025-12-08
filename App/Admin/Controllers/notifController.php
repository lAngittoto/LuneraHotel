<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php'; // LuneraHotel DB

$stmt = $pdo->query("
    SELECT 
        n.id,
        r.room_number,
        n.description,
        n.status,
        n.completed_at
    FROM notifications n
    JOIN rooms r ON n.room_id = r.id
    ORDER BY n.completed_at DESC
");

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
