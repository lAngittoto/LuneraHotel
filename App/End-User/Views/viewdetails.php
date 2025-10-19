<?php
// viewdetails.php
$title = $title ?? "View Room Details";
ob_start();
require "header.php";

// Safety guard (shouldn't hit if controller is correct)
if (!isset($room) || !is_array($room)) {
    echo "<p class='p-6 text-center text-red-600'>Room information is not available.</p>";
    $content = ob_get_clean();
    include __DIR__ . "/../../../App/layout.php";
    exit;
}

// Ensure $amenities is an array to avoid foreach warnings
if (!is_array($amenities)) {
    $amenities = [];
}

$statusClass = $statusClass ?? '';
?>
<section class="w-full bg-[#f8f8f8] p-6 sm:p-8 md:p-10 lg:p-12 flex flex-col md:flex-col lg:flex-row justify-around items-start gap-6 md:gap-8 lg:gap-12 select-none">
    <!-- Room Info -->
    <div class="flex flex-col w-full md:w-full lg:w-[40%] border border-[#dcdcdc] bg-[#ffffff] text-[#333333] rounded-lg shadow-sm gap-4">
        <img src="<?= htmlspecialchars($room['img']) ?>" alt="Room Image"
            class="w-full h-64 sm:h-72 md:h-80 lg:h-96 object-cover rounded-t-lg">

        <div class="p-5 sm:p-6 md:p-8 lg:p-10 flex flex-col gap-4">
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-4xl font-semibold text-[#000000]">
                <?= htmlspecialchars($room['room_type']) ?>
            </h1>

            <div class="flex flex-col md:flex-col lg:flex-row w-full justify-between items-start lg:items-center gap-3 text-xs sm:text-sm md:text-base lg:text-lg">
                <span class="<?= htmlspecialchars($statusClass) ?> px-3 py-1 rounded-4xl"><?= htmlspecialchars($room['status']) ?></span>
                <p>Room <?= htmlspecialchars($room['room_number']) ?></p>
                <p><i class="fa-regular fa-user"></i> Up to <?= htmlspecialchars($room['people']) ?> People</p>
            </div>

            <p class="text-xs sm:text-sm md:text-base lg:text-lg mb-3"><?= htmlspecialchars($room['description']) ?></p>

            <h2 class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-[#000000] mb-3">Amenities</h2>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4 lg:gap-5 text-xs sm:text-sm md:text-base lg:text-lg">
                <?php if (count($amenities) === 0): ?>
                    <li>No amenities listed.</li>
                <?php else: ?>
                    <?php foreach ($amenities as $a): ?>
                        <li class="flex items-center gap-2">
                            <i class="<?= htmlspecialchars(getAmenityIcon($a['amenity'])) ?> text-[#800000]"></i>
                            <?= htmlspecialchars($a['amenity']) ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Booking Form -->
    <?php if ($room['status'] === 'Available'): ?>
        <div class="flex flex-col w-full md:w-full lg:w-[40%] bg-[#ffffff] p-5 sm:p-6 md:p-8 lg:p-10 border border-[#dcdcdc] gap-5 sm:gap-6 md:gap-8 lg:gap-10 mt-6 lg:mt-0 rounded-lg shadow-sm">
            <form method="POST" action="/LuneraHotel/App/Public/bookroom" class="flex flex-col gap-4">
                <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']) ?>">

                <div class="flex flex-col text-xs sm:text-sm md:text-base lg:text-lg gap-2">
                    <label for="date" class="mb-1">Date</label>
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-calendar"></i>
                        <?php
                        date_default_timezone_set('Asia/Manila');
                        $currentDate = date("F d, Y");
                        $nextDate = date("F d, Y", strtotime("+2 days"));
                        echo "$currentDate to $nextDate";
                        ?>
                    </div>
                </div>

                <div class="flex flex-col md:flex-col lg:flex-row justify-between gap-4">
                    <div class="flex flex-col w-full lg:w-[48%] text-xs sm:text-sm md:text-base lg:text-lg gap-2">
                        <label for="in" class="mb-1">Check In</label>
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-clock"></i>
                            <div class="outline-none bg-[#f8f8f8] py-2 px-3 border border-[#dcdcdc] w-full rounded-md text-[#333333]">
                                2:00 PM
                            </div>
                        </div>
                        <div class="flex flex-col w-full lg:w-[48%] text-xs sm:text-sm md:text-base lg:text-lg gap-2">
                            <label for="out" class="mb-1">Check Out</label>
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-clock"></i>
                                <div class="outline-none bg-[#f8f8f8] py-2 px-3 border border-[#dcdcdc] w-full rounded-md text-[#333333]">
                                    12:00 AM
                                </div>

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
include __DIR__ . "/../../../App/layout.php";
