<?php
session_start();
header('Content-Type: application/json');
require('database.php');

if (!isset($_SESSION['username']) || $_SESSION['accType'] !== 'staff') {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$housekeeperId = $_SESSION['housekeeperID'] ?? 0;

if ($housekeeperId === 0) {
    echo json_encode(['success' => false, 'error' => 'No housekeeper ID found']);
    exit;
}

// Get user info from users table
$stmt = $conn->prepare("SELECT u.Username, h.Email, h.Phone FROM users u JOIN housekeepers h ON u.HousekeeperID = h.HousekeeperID WHERE u.HousekeeperID = ?");
$stmt->bind_param('i', $housekeeperId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if ($userData) {
    echo json_encode([
        'success' => true,
        'username' => $userData['Username'],
        'email' => $userData['Email'],
        'phone' => $userData['Phone']
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'User not found']);
}
?>
