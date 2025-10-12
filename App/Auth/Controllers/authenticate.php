<?php
require __DIR__.'/../Models/config.php';  // Connect to DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Fetch user from DB
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify hashed password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user; // buong user data i-save sa session

        if ($user['role'] === 'admin') {
            header('Location: /LuneraHotel/App/Public/admin');
        } else {
            header('Location: /LuneraHotel/App/Public/rooms');
        }

        exit;
    } else {
        $_SESSION['error'] = "Invalid email or password";
        header('Location: /LuneraHotel/App/Public');
        exit;
    }
}