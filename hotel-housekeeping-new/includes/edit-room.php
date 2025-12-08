<?php
header('Content-Type: application/json');
require('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['roomId'] ?? '';
    $roomNumber = $_POST['roomNumber'] ?? '';
    $floor = $_POST['floor'] ?? '';
    $roomType = $_POST['roomType'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if ($roomId && $roomNumber && $floor && $roomType && $status) {
        $stmt = $conn->prepare("UPDATE rooms SET RoomNumber = ?, Floor = ?, RoomType = ?, Status = ? WHERE RoomID = ?");
        $stmt->bind_param('sissi', $roomNumber, $floor, $roomType, $status, $roomId);
        $success = $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => $success]);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Missing parameters']);
?>
