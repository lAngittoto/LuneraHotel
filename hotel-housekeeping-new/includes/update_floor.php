<?php
require('database.php');
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['staffID'], $_POST['floor'])) {
    $housekeeperId = (int) $_POST['staffID'];
    $floor   = (int) $_POST['floor']; // Cast as integer
    
    try {
        $stmt = $conn->prepare("UPDATE housekeepers SET AssignedFloor = ? WHERE HousekeeperID = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ii", $floor, $housekeeperId);
        $success = $stmt->execute();
        
        if (!$success) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $stmt->close();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
}
?>