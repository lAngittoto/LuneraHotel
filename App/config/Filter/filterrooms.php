<?php


require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/filterroomsmodel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Debug: check if $pdo exists
if (!isset($pdo)) {
    echo json_encode(['error' => 'PDO not initialized']);
    exit;
}
function getActiveFloors($pdo)
{
    $stmt = $pdo->prepare("
        SELECT floor
        FROM rooms
        WHERE status != 'Deactivated'
        GROUP BY floor
        HAVING COUNT(*) > 0
        ORDER BY 
            CASE floor
                WHEN 'First Floor' THEN 1
                WHEN 'Second Floor' THEN 2
                WHEN 'Third Floor' THEN 3
                WHEN 'Fourth Floor' THEN 4
                WHEN 'Fifth Floor' THEN 5
                WHEN 'Sixth Floor' THEN 6
                WHEN 'Seventh Floor' THEN 7
                WHEN 'Eighth Floor' THEN 8
                WHEN 'Ninth Floor' THEN 9
                WHEN 'Tenth Floor' THEN 10
                ELSE 11
            END
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}



// Debug: see session
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Get filters
$status = $_GET['status'] ?? '';
$type   = $_GET['type'] ?? '';
$floor  = $_GET['floor'] ?? '';

try {
    $rooms = getFilteredRooms($pdo, $status, $type, $floor);
    echo json_encode($rooms);
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
