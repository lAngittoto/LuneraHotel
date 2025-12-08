<?php
require("database.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (!isset($_POST['staffID'])) {
    http_response_code(400);
    exit;
}

$housekeeperId = (int) $_POST['staffID'];
$shiftId = $_POST['shiftID'] ?? '';

// Delete existing shift assignment for this housekeeper
$stmt = $conn->prepare("DELETE FROM housekeepershifts WHERE HousekeeperID = ?");
$stmt->bind_param("i", $housekeeperId);
$stmt->execute();
$stmt->close();

// If shiftID is provided, assign the new shift
if ($shiftId !== '') {
    $shiftId = (int) $shiftId;
    
    // Verify shift exists
    $stmt = $conn->prepare("SELECT ShiftID FROM shifts WHERE ShiftID = ?");
    $stmt->bind_param("i", $shiftId);
    $stmt->execute();
    $result = $stmt->get_result();
    $shift = $result->fetch_assoc();
    $stmt->close();
    
    if ($shift) {
        // Create assignment
        $stmt = $conn->prepare("INSERT INTO housekeepershifts (HousekeeperID, ShiftID) VALUES (?, ?)");
        $stmt->bind_param("ii", $housekeeperId, $shiftId);
        $stmt->execute();
        $stmt->close();
    } else {
        http_response_code(404);
        echo 'Shift not found';
        exit;
    }
}

http_response_code(200);
echo 'OK';
?>