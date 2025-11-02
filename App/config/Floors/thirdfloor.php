<?php
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../roomdata.php';
// Fetch rooms for Third Floor
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE floor = 'Third Floor' ORDER BY room_number");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($rooms as $roomData){
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
}
?>
