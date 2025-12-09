<?php
session_start();
require("database.php");

$userName = $_POST["username"];
$password = $_POST["password"];

// First, check the admins table
$sql = "SELECT * FROM admins WHERE Username = ?";
$query = $conn->prepare($sql);
$query->bind_param("s", $userName);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if ($password === $admin['Password']) {
        $_SESSION["username"] = $admin["Username"];
        $_SESSION["accType"] = "admin";
        header("Location: ../admin-dashboard.php");
        exit();
    } else {
        echo "Invalid credentials";
        exit();
    }
}

// If not found in admins, check the users table (staff)
$sql = "SELECT * FROM users WHERE Username = ?";
$query = $conn->prepare($sql);
$query->bind_param("s", $userName);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if ($password === $user['Password']) {
        $_SESSION["username"] = $user["Username"];
        $_SESSION["accType"] = "staff";
        $_SESSION["housekeeperID"] = $user["HousekeeperID"];
        
        // Get staff name from housekeepers table
        $housekeeperStmt = $conn->prepare("SELECT FullName FROM housekeepers WHERE HousekeeperID = ?");
        $housekeeperStmt->bind_param("i", $user["HousekeeperID"]);
        $housekeeperStmt->execute();
        $housekeeperResult = $housekeeperStmt->get_result();
        if ($housekeeperResult->num_rows === 1) {
            $housekeeper = $housekeeperResult->fetch_assoc();
            $_SESSION["staffName"] = $housekeeper["FullName"];
        } else {
            $_SESSION["staffName"] = $user["Username"];
        }
        $housekeeperStmt->close();
        
        header("Location: ../staff-dashboard.php");
        exit();
    } else {
        echo "Invalid credentials";
        exit();
    }
}

// If not found in either table
echo "Invalid credentials";
?>
