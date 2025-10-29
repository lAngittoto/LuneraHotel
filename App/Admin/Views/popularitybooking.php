<?php ob_start(); 
require_once __DIR__.'/header.php';
?>

<h1>Popularity</h1>

  <?php
    $content = ob_get_clean();
    include __DIR__ . '/../../../App/layout.php';
    ?>