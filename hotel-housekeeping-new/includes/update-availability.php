<?php
session_start();
require_once 'database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$availability = $_POST['availability'] ?? '';
$reason = $_POST['reason'] ?? '';
$staffName = $_SESSION['staffName'] ?? $_SESSION['username'];

// Validate availability value
$validStatuses = ['Available', 'On Break', 'Absent', 'On Leave', 'Unavailable'];
if (!in_array($availability, $validStatuses)) {
    echo json_encode(['success' => false, 'error' => 'Invalid availability status']);
    exit();
}

// Statuses that require a reason
$requiresReason = ['Absent', 'On Leave', 'Unavailable'];
if (in_array($availability, $requiresReason) && empty(trim($reason))) {
    echo json_encode(['success' => false, 'error' => 'Reason is required for this status']);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE housekeepers SET Availability = ?, reason = ? WHERE FullName = ?");
    $stmt->bind_param("sss", $availability, $reason, $staffName);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Availability updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update availability']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
