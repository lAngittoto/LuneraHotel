<?php
// $page ay galing sa index.php
$page = $_GET['page'] ?? 'login';

// Define which pages belong to Auth/Admin
$authPages = ['login', 'authenticate', 'admin'];

// Define which pages belong to End-User
$endUserPages = ['home', 'rooms', 'mybookings', 'viewdetails', 'bookroom']; // ✅ added bookroom

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
} else {
    http_response_code(404);
    echo '<h1>404 - Page Not Found</h1>';
}
