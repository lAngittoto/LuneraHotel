<?php
// Start output buffering to catch any accidental output
ob_start();

header('Content-Type: application/json');
require('database.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['housekeeperId'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'No ID provided']);
    exit;
}

$housekeeperId = (int) $_POST['housekeeperId'];

// First, get the UserID associated with this housekeeper
$stmt = $conn->prepare("SELECT UserID FROM users WHERE HousekeeperID = ?");
$stmt->bind_param('i', $housekeeperId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$userId = $user['UserID'] ?? null;
$stmt->close();

// Delete notifications if UserID exists
if ($userId) {
    $stmt = $conn->prepare("DELETE FROM notifications WHERE UserID = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();
}

// Delete from housekeepershifts first (foreign key constraint)
$stmt = $conn->prepare("DELETE FROM housekeepershifts WHERE HousekeeperID = ?");
$stmt->bind_param('i', $housekeeperId);
$stmt->execute();
$stmt->close();

// Delete or update assignments - set HousekeeperID to NULL instead of deleting
$stmt = $conn->prepare("UPDATE assignments SET HousekeeperID = NULL WHERE HousekeeperID = ?");
$stmt->bind_param('i', $housekeeperId);
$stmt->execute();
$stmt->close();

// Delete from users table (using HousekeeperID link)
$stmt = $conn->prepare("DELETE FROM users WHERE HousekeeperID = ?");
$stmt->bind_param('i', $housekeeperId);
$stmt->execute();
$stmt->close();

// Finally delete from housekeepers
$stmt = $conn->prepare("DELETE FROM housekeepers WHERE HousekeeperID = ?");
$stmt->bind_param('i', $housekeeperId);

if ($stmt->execute()) {
    $stmt->close();
    ob_end_clean();
    echo json_encode(['success' => true]);
} else {
    $stmt->close();
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Failed to delete staff member']);
}
?>
