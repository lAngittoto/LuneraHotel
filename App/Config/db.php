<?php
$host = "";   // MySQL server (usually localhost)
$user = "";        // default user sa XAMPP
$pass = "";            // default password sa XAMPP (empty)
$dbname = "";    // palitan mo ng database name mo

try {
    // PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

    // Set error mode para makita errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: Debug confirmation (comment out later)
    // echo "Connected successfully!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
