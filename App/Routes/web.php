<?php
// $page ay galing sa index.php
$page = $_GET['page'] ?? 'login';

// Define which pages belong to Auth/Admin
$authPages = ['login', 'authenticate', 'admin'];

// Define which pages belong to End-User
$endUserPages = ['rooms', 'viewdetails', 'bookroom'];
$adminPages = ['managerooms','allbookings','popularity','allrooms' ,'viewdetailsadmin','updaterooms','createrooms','notification','annualreport','inventory']; //  added bookroom

// Decide which route to include
if (in_array($page, $authPages)) {
    // Admin/Auth routing
    switch ($page) {
        case 'login':
            include __DIR__ . '/../Auth/Views/login.php';
            break;

        case 'authenticate':
            include __DIR__ . '/../Auth/Controllers/authenticate.php';
            break;
            
        case 'admin':
            require_once __DIR__ . '/../Admin/Controllers/DashboardController.php';
            break;

    }
} elseif (in_array($page, $endUserPages)) {
    // Include End-User router
    require_once __DIR__ . '/end-user.php';
} elseif(in_array($page, $adminPages)){
    require_once __DIR__ .'/admin.php';
}
else {
    http_response_code(404);
    echo '<h1>404 - Page Not Found</h1>';
}
