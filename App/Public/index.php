<?php
session_start();

// default page if none provided
$page = $_GET['page'] ?? 'login';

// load routes (web.php will use $page)
require_once __DIR__ . '/../Routes/web.php';
