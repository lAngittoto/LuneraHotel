<?php
header('Content-Type: application/json');
session_start();
require('database.php');

if (!isset($_SESSION['username']) || $_SESSION['accType'] !== 'staff') {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$housekeeperId = $_SESSION['housekeeperID'] ?? 0;

if ($housekeeperId === 0) {
    echo json_encode(['success' => false, 'error' => 'No housekeeper ID found']);
    exit;
}

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';

// Validate required fields
if (empty($username)) {
    echo json_encode(['success' => false, 'error' => 'Username is required']);
    exit;
}

// Check if username is already taken by another user
$stmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ? AND HousekeeperID != ?");
$stmt->bind_param('si', $username, $housekeeperId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Username is already taken']);
    $stmt->close();
    exit;
}
$stmt->close();

$usernameChanged = false;

// If changing password, verify current password first
if (!empty($newPassword)) {
    if (empty($currentPassword)) {
        echo json_encode(['success' => false, 'error' => 'Current password is required to change password']);
        exit;
    }
    
    // Get current password hash
    $stmt = $conn->prepare("SELECT Password FROM users WHERE HousekeeperID = ?");
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user || !password_verify($currentPassword, $user['Password'])) {
        echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
        exit;
    }
    
    // Update username and password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET Username = ?, Password = ? WHERE HousekeeperID = ?");
    $stmt->bind_param('ssi', $username, $hashedPassword, $housekeeperId);
    $success = $stmt->execute();
    $stmt->close();
    
    if (!$success) {
        echo json_encode(['success' => false, 'error' => 'Failed to update user credentials']);
        exit;
    }
    
    // Check if username changed
    if ($username !== $_SESSION['username']) {
        $usernameChanged = true;
        $_SESSION['username'] = $username;
    }
} else {
    // Update just username
    $stmt = $conn->prepare("UPDATE users SET Username = ? WHERE HousekeeperID = ?");
    $stmt->bind_param('si', $username, $housekeeperId);
    $success = $stmt->execute();
    $stmt->close();
    
    if (!$success) {
        echo json_encode(['success' => false, 'error' => 'Failed to update username']);
        exit;
    }
    
    // Check if username changed
    if ($username !== $_SESSION['username']) {
        $usernameChanged = true;
        $_SESSION['username'] = $username;
    }
}

// Update housekeeper email and phone
$stmt = $conn->prepare("UPDATE housekeepers SET Email = ?, Phone = ? WHERE HousekeeperID = ?");
$stmt->bind_param('ssi', $email, $phone, $housekeeperId);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    echo json_encode(['success' => true, 'usernameChanged' => $usernameChanged]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update contact information']);
}
?>
