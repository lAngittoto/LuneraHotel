<?php ob_start(); 
require_once __DIR__.'/header.php';
?>

<h1>All Rooms</h1>
  <?php
    $content = ob_get_clean();
    include __DIR__ . '/../../../App/layout.php';
    ?>