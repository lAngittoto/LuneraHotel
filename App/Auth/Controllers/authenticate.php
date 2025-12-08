<?php
require __DIR__.'/../Models/config.php';
require __DIR__.'/../Models/authenticatemodel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = getUser($pdo, $email);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;

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
