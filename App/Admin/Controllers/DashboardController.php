<?php

// Only admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

// Load model
require_once __DIR__ . '/../Models/DashboardModel.php';

// Get data from model
list($totalRooms, $availableRooms, $bookings, $undermaintenance) = getDashboardData();

// Page title
$title = 'Admin Dashboard';

// Load view (variables available inside view)
require_once __DIR__ . '/../Views/dashboard.php';
