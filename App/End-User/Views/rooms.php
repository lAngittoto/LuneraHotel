<?php 
$title = "Rooms";
ob_start();
require_once 'header.php';
require_once __DIR__.'/../../config/Filter/filter.php';

if (!isset($_SESSION['user'])) {
       header('Location: /LuneraHotel/App/Public');
exit;

}
?>
<?php foreach($roomsByFloor as $floor => $rooms): ?>
    <?php if (!empty($rooms)): ?>
        <h1 class='text-4xl font-bold p-10'><?= convertFloor($floor) ?></h1>
        <section class='p-10 w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-8'>
            <?php foreach($rooms as $roomData): ?>
                <?php 
                    $r = new Rooms(
                        $roomData['id'],
                        $roomData['img'],
                        $roomData['room_type'],
                        $roomData['status'],
                        $roomData['description'],
                        $roomData['room_number'],
                        $roomData['people'],
                        $roomData['floor']
                    );
                    $r->displayRoom();
                ?>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
<?php endforeach; ?>


<?php
$content = ob_get_clean();
include __DIR__. '/../../../App/layout.php';
?>
