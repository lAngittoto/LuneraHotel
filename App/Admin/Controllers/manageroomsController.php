<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../Models/manageroomsModel.php";
require_once __DIR__ . "/../../config/Helpers/colorcoding.php";
require_once __DIR__.'/../../config/Helpers/correctgrammar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /LuneraHotel/App/Public');
    exit;
}


    $rooms = getRoomsSummary($pdo);

require_once __DIR__. '/../Views/managerooms.php';