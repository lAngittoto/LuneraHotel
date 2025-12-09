<?php
require('database.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['reg_username'] ?? '');
    $password = trim($_POST['reg_password'] ?? '');
    $uuid = trim($_POST['reg_uuid'] ?? '');
    
    // Validate required fields
    if (empty($username) || empty($password) || empty($uuid)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }
    
    try {
        // Check if UUID exists in housekeepers table
        $checkUuidStmt = $conn->prepare("SELECT HousekeeperID, FullName FROM housekeepers WHERE UUID = ?");
        $checkUuidStmt->bind_param('s', $uuid);
        $checkUuidStmt->execute();
        $uuidResult = $checkUuidStmt->get_result();
        
        if ($uuidResult->num_rows === 0) {
            $checkUuidStmt->close();
            echo json_encode(['success' => false, 'error' => 'Invalid UUID. Please contact administration.']);
            exit;
        }
        
        $housekeeper = $uuidResult->fetch_assoc();
        $housekeeperId = $housekeeper['HousekeeperID'];
        $fullName = $housekeeper['FullName'];
        $checkUuidStmt->close();
        
        // Check if this housekeeper already has an account
        $checkAccountStmt = $conn->prepare("SELECT haveAccount FROM housekeepers WHERE HousekeeperID = ?");
        $checkAccountStmt->bind_param('i', $housekeeperId);
        $checkAccountStmt->execute();
        $accountResult = $checkAccountStmt->get_result();
        $accountData = $accountResult->fetch_assoc();
        $checkAccountStmt->close();
        
        if ($accountData['haveAccount'] == 1) {
            echo json_encode(['success' => false, 'error' => 'This UUID already has an account. Please contact administration if you forgot your password.']);
            exit;
        }
        
        // Check if username already exists
        $checkUserStmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ?");
        $checkUserStmt->bind_param('s', $username);
        $checkUserStmt->execute();
        $userResult = $checkUserStmt->get_result();
        
        if ($userResult->num_rows > 0) {
            $checkUserStmt->close();
            echo json_encode(['success' => false, 'error' => 'Username already exists']);
            exit;
        }
        $checkUserStmt->close();
        
        // Create user account with HousekeeperID link
        $insertStmt = $conn->prepare("INSERT INTO users (Username, Password, HousekeeperID) VALUES (?, ?, ?)");
        $insertStmt->bind_param('ssi', $username, $password, $housekeeperId);
        
        if ($insertStmt->execute()) {
            $insertStmt->close();
            
            // Update haveAccount flag in housekeepers table
            $updateStmt = $conn->prepare("UPDATE housekeepers SET haveAccount = 1 WHERE HousekeeperID = ?");
            $updateStmt->bind_param('i', $housekeeperId);
            $updateStmt->execute();
            $updateStmt->close();
            
            echo json_encode([
                'success' => true,
                'message' => 'Account created successfully for ' . $fullName
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to create account']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>