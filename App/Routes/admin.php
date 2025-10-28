<?php
$page = $_GET['page'] ?? 'admin'; // default sa rooms

switch ($page) {
    case 'managerooms':
        include __DIR__ . '/../Admin/Views/managerooms.php';
        break;

    case 'allbookings':
        include __DIR__ . '/../Admin/Views/allbookings.php';
        break;

    case 'popularity':
        include __DIR__ . '/../Admin/Views/popularitybooking.php';
        break;


    default:
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        break;
}
