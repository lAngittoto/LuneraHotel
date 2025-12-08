<?php
require('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $uuid = trim($_POST['uuid'] ?? '');
    $hireDate = $_POST['hireDate'] ?? null;
    $assignedFloor = $_POST['assignedFloor'] ?? null;
    $availability = $_POST['availability'] ?? 'Available';
    $editId = $_POST['editId'] ?? null;
    
    // Validate required fields
    if (empty($fullName)) {
        echo json_encode(['success' => false, 'error' => 'Full name is required']);
        exit;
    }
    
    // Set default hire date to today if not provided
    if (empty($hireDate)) {
        $hireDate = date('Y-m-d');
    }
    
    // Handle empty floor assignment
    if ($assignedFloor === '') {
        $assignedFloor = null;
    }
    
    try {
        if ($editId) {
            // UPDATE existing staff
            $editId = (int) $editId;
            $stmt = $conn->prepare("UPDATE housekeepers SET FullName = ?, Phone = ?, Email = ?, UUID = ?, HireDate = ?, AssignedFloor = ?, Availability = ? WHERE HousekeeperID = ?");
            $stmt->bind_param('sssssssi', $fullName, $phone, $email, $uuid, $hireDate, $assignedFloor, $availability, $editId);
            
            if ($stmt->execute()) {
                $stmt->close();
                echo json_encode([
                    'success' => true,
                    'message' => 'Staff member updated successfully'
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update staff member']);
            }
        } else {
            // INSERT new staff
            $stmt = $conn->prepare("INSERT INTO housekeepers (FullName, Phone, Email, UUID, HireDate, AssignedFloor, Availability) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssss', $fullName, $phone, $email, $uuid, $hireDate, $assignedFloor, $availability);
            
            if ($stmt->execute()) {
                $housekeeperId = $conn->insert_id;
                $stmt->close();
                echo json_encode([
                    'success' => true, 
                    'housekeeperId' => $housekeeperId,
                    'message' => 'Staff member added successfully'
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to insert into database']);
            }
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
