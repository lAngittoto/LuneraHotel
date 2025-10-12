<?php
$host = "localhost";   // MySQL server (usually localhost)
$user = "root";        // default user sa XAMPP
$pass = "P@ssw0rd";            // default password sa XAMPP (empty)
$dbname = "roomslunera_hotel";    // palitan mo ng database name mo

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
