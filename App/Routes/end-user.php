<?php
$page = $_GET['page'] ?? 'rooms'; // default sa rooms

switch ($page) {
    case 'rooms':
        include __DIR__ . '/../End-User/Views/rooms.php';
        break;

    case 'mybookings':
        include __DIR__ . '/../End-User/Views/mybookings.php';
        break;

    case 'viewdetails':
        include __DIR__ . '/../End-User/Views/viewdetails.php';
        break;

    default:
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        break;
}
