<?php
// manageroomsController.php

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../Models/manageroomsModel.php";
require_once __DIR__ . "/../../config/Helpers/colorcoding.php";
require_once __DIR__ . "/../../config/Helpers/correctgrammar.php";



if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

// GET ROOMS SUMMARY
$rooms = getRoomsSummary($pdo);

// FORCE AVAILABLE ROOMS THAT HAVE CLEANING TASK TO SHOW "Cleaning in Progress"
foreach ($rooms as &$room) {
    if ($room['status'] === 'Available' && $room['has_cleaning_task']) {
        $room['status'] = 'In Progress';
    }
}
unset($room);

// LOAD VIEW
require_once __DIR__ . '/../Views/managerooms.php';
