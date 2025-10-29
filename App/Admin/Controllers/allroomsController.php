<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

require_once __DIR__. '/../Views/allrooms.php';