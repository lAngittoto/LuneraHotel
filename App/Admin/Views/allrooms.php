<?php 
$title = "Rooms";
ob_start();
require_once 'header.php';


if (!isset($_SESSION['user'])) {
       header('Location: /LuneraHotel/App/Public');
exit;

}
?>
<h1 class=" text-5xl font-bold p-10 text-[#333333]">List of all rooms</h1>

<h1 class="text-4xl p-10">First Floor</h1>

<section class="p-10 w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-10 items-stretch">
    <?php require_once __DIR__.'/../../config/Floors/firstfloor.php'; ?>
</section>

<h1 class="text-4xl p-10">Second Floor</h1>
<section class="p-10 w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-10 items-stretch">
    <?php require_once __DIR__.'/../../config/Floors/secondfloor.php'; ?>
</section>

<h1 class="text-4xl p-10">Third Floor</h1>
<section class="p-10 w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-10 items-stretch">
    <?php require_once __DIR__.'/../../config/Floors/thirdfloor.php'; ?>
</section>


<?php
$content = ob_get_clean();
include __DIR__. '/../../../App/layout.php';
?>
