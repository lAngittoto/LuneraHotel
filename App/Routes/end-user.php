<?php
$page = $_GET['page'] ?? 'rooms'; // default sa rooms

switch ($page) {
    case 'rooms':
        include __DIR__ . '/../End-User/Controllers/roomsController.php';
        break;

    case 'viewdetails':
        include __DIR__ . '/../End-User/Controllers/viewdetailsController.php';
        break;

    case 'bookroom':
        include __DIR__ . '/../End-User/Controllers/bookroomController.php';
        break;


    default:
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        break;
}
