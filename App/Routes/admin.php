<?php
$page = $_GET['page'] ?? 'admin'; // default sa rooms

switch ($page) {

    case 'allrooms':
        include __DIR__ . '/../Admin/Controllers/allroomsController.php';
        break;

    case 'managerooms':
        include __DIR__ . '/../Admin/Controllers/manageroomsController.php';
        break;

    case 'allbookings':
        include __DIR__ . '/../Admin/Controllers/allbookingsController.php';
        break;

    case 'popularity':
        include __DIR__ . '/../Admin/Controllers/popularityController.php';
        break;
    case 'viewdetailsadmin':
        include __DIR__ . '/../Admin/Controllers/viewdetailsadminController.php';
        break;

    case 'updaterooms':
        include __DIR__ . '/../Admin/Controllers/updateRoomsController.php';
        break;
    
    case 'createrooms':
        include __DIR__ . '/../Admin/Controllers/createRoomsController.php';
        break;


    default:
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        break;
}
