<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache");
header("Expires: 0");
// default page if none provided
$page = $_GET['page'] ?? 'login';

// load routes (web.php will use $page)
require_once __DIR__ . '/../Routes/web.php';
