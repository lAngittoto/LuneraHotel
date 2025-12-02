<?php
$host = "localhost";   
$user = "root";        
$pass = "P@ssw0rd";           
$dbname = "roomsintegrate";    
$dbname_web = "webdb";

try {
    // PDO connection for roomslunera_hotel (tasks)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // PDO connection for webdb (assignments)
    $pdoWeb = new PDO("mysql:host=$host;dbname=$dbname_web;charset=utf8", $user, $pass);
    $pdoWeb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connected successfully!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());

}
