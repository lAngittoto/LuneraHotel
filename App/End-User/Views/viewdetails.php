<?php
$title = "View Room Details";
ob_start();

require "header.php";
require_once __DIR__ . "/../models/db.php";
require_once __DIR__ . "/../helpers/colorcoding.php";
require_once __DIR__ . "/../helpers/amenityicon.php";

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /rmsminicapstone/login');
    exit;
}

$roomId = $_GET['room'] ?? null;

if ($roomId) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$roomId]);
    $room = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT amenity FROM amenities WHERE room_id = ?");
    $stmt->execute([$roomId]);
    $amenities = $stmt->fetchAll();
}

if (!$room) {
    echo "Room not found.";
    exit;
}

$statusClass = getStatusClass($room['status']);
?>
<section class="w-full bg-[#f8f8f8] p-6 sm:p-8 md:p-10 lg:p-12 flex flex-col md:flex-col lg:flex-row justify-around items-start gap-6 md:gap-8 lg:gap-12">
    <!-- Room Info -->
    <div class="flex flex-col w-full md:w-full lg:w-[40%] border border-[#dcdcdc] bg-[#ffffff] text-[#333333] rounded-lg shadow-sm gap-4">
        <img src="<?= $room['img'] ?>" alt="Room Image" 
             class="w-full h-64 sm:h-72 md:h-80 lg:h-96 object-cover rounded-t-lg">
        <div class="p-5 sm:p-6 md:p-8 lg:p-10 flex flex-col gap-4">
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-4xl font-semibold text-[#000000]"><?= $room['room_type'] ?></h1>

            <div class="flex flex-col md:flex-col lg:flex-row w-full justify-between items-start lg:items-center gap-3 text-xs sm:text-sm md:text-base lg:text-lg">
                <span class="<?= $statusClass ?> px-3 py-1 rounded-4xl"><?= $room['status'] ?></span>
                <p>Room <?= $room['room_number'] ?></p>
                <p><i class="fa-regular fa-user"></i> Up to <?= $room['people'] ?> People</p>
            </div>

            <p class="text-xs sm:text-sm md:text-base lg:text-lg mb-3"><?= $room['description'] ?></p>

            <h2 class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-[#000000] mb-3">Amenities</h2>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4 lg:gap-5 text-xs sm:text-sm md:text-base lg:text-lg">
                <?php foreach ($amenities as $a): ?>
                    <li class="flex items-center gap-2">
                        <i class="<?= getAmenityIcon($a['amenity']) ?> text-[#800000]"></i>
                        <?= htmlspecialchars($a['amenity']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Booking Form -->
    <?php if ($room['status'] === 'Available'): ?>
    <div class="flex flex-col w-full md:w-full lg:w-[40%] bg-[#ffffff] p-5 sm:p-6 md:p-8 lg:p-10 border border-[#dcdcdc] gap-5 sm:gap-6 md:gap-8 lg:gap-10 mt-6 lg:mt-0 rounded-lg shadow-sm">
        <form method="POST" action="/rmsminicapstone/bookroom" class="flex flex-col gap-4 sm:gap-5 md:gap-6 lg:gap-8">
            <input type="hidden" name="room_id" value="<?= $room['id'] ?>">

            <div class="flex flex-col text-xs sm:text-sm md:text-base lg:text-lg gap-2">
                <label for="date" class="mb-1">Date</label>
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar"></i>
                    <input type="text" name="date" readonly disabled placeholder="Ex.05-06-2025"
                           class="outline-none bg-[#f8f8f8] py-2 px-3 border border-[#dcdcdc] w-full rounded-md">
                </div>
            </div>

            <div class="flex flex-col md:flex-col lg:flex-row justify-between gap-4">
                <div class="flex flex-col w-full lg:w-[48%] text-xs sm:text-sm md:text-base lg:text-lg gap-2">
                    <label for="in" class="mb-1">Check in</label>
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-clock"></i>
                        <input type="text" name="in" readonly disabled placeholder="9:30"
                               class="outline-none bg-[#f8f8f8] py-2 px-3 border border-[#dcdcdc] w-full rounded-md">
                    </div>
                </div>
                <div class="flex flex-col w-full lg:w-[48%] text-xs sm:text-sm md:text-base lg:text-lg gap-2">
                    <label for="out" class="mb-1">Check Out</label>
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-clock"></i>
                        <input type="text" name="out" readonly disabled placeholder="9:30"
                               class="outline-none bg-[#f8f8f8] py-2 px-3 border border-[#dcdcdc] w-full rounded-md">
                    </div>
                </div>
            </div>

            <button type="submit"
                class="bg-[#800000] text-xs sm:text-sm md:text-base lg:text-lg py-3 mt-5 text-[#ffffff] text-center rounded-2xl cursor-pointer">
                Confirm Booking
            </button>
        </form>
    </div>
    <?php endif; ?>
</section>


<?php
$content = ob_get_clean();
include "layout.php";
?>
