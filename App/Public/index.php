<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache");
header("Expires: 0");

$page = $_GET['page'] ?? 'login';


require_once __DIR__ . '/../Routes/web.php';
