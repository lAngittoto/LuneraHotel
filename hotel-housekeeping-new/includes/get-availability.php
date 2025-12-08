<?php
session_start();
require_once 'database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$staffName = $_SESSION['staffName'] ?? $_SESSION['username'];

try {
    $stmt = $conn->prepare("SELECT Availability FROM housekeepers WHERE FullName = ?");
    $stmt->bind_param("s", $staffName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'availability' => $row['Availability'] ?? 'Available']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Staff member not found']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
