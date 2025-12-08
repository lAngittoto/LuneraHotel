<?php ob_start(); ?>
<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/../../config/Helpers/colorcoding.php";
require_once __DIR__ . "/../../config/Helpers/correctgrammar.php";
?>

<!-- Header Section -->
<section class="w-full bg-[#f8f8f8] py-16 flex flex-col items-center">
    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 text-[#333333] text-center select-none">
        My Bookings
    </h1>
    <p class="text-gray-600 text-base sm:text-lg md:text-xl text-center max-w-2xl">
        Review and manage all your reserved rooms below.
    </p>
</section>

<?php if (empty($bookedRooms)): ?>
    <p class="text-2xl sm:text-3xl md:text-4xl text-gray-500 text-center my-20 select-none">
        You have no bookings yet.
    </p>
<?php else: ?>

    <!-- Booked Rooms Single Column Stack -->
    <section class="p-10 sm:p-14 md:p-16 w-full max-w-4xl mx-auto flex flex-col gap-12">
        <?php foreach ($bookedRooms as $room): ?>
            <?php $statusClass = getStatusClass($room['booking_status']); ?>

            <div class="bg-white rounded-3xl border border-[#b1b1b1] shadow-2xl hover:shadow-2xl transition overflow-hidden flex flex-col">

                <!-- Room Image -->
                <div class="relative w-full overflow-hidden h-[420px] sm:h-[460px] md:h-[500px]">
                    <img src="<?= htmlspecialchars($room['img']) ?>"
                        alt="Room Image"
                        class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                </div>

                <!-- Room Info -->
                <div class="flex flex-col gap-4 p-8 text-[#333333] flex-grow">
                    <div class="flex flex-row justify-between items-start">
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold leading-tight">
                            <?= htmlspecialchars($room['room_type']) ?>
                        </h2>
                        <h1 class="text-sm sm:text-base md:text-lg px-4 py-2 rounded-3xl <?= $statusClass ?>">
                            <?= htmlspecialchars($room['booking_status']) ?>
                        </h1>
                    </div>

                    <!-- Room Details -->
                    <p class="text-lg sm:text-xl md:text-2xl text-gray-700">
                        <i class="fa-solid fa-bed text-[#800000] mr-2"></i>
                        Room <?= htmlspecialchars($room['room_number']) ?> —
                        <i class="fa-regular fa-user text-[#800000]"></i>
                        <?= htmlspecialchars(correctGuest($room['people'])) ?>

                    </p>
<?php if (!empty($room['floor'])): ?>
    <p class="text-lg sm:text-xl md:text-2xl text-gray-700 flex items-center gap-2">
        <i class="fa-solid fa-building text-[#800000]"></i>
        <?= htmlspecialchars(convertFloor($room['floor'])) ?>
    </p>
<?php endif; ?>


                    <!-- Dates + Check In/Out -->
                    <div class="flex flex-col sm:flex-row justify-between mt-6 text-gray-800 text-lg sm:text-xl gap-4 sm:gap-6">
                        <div class="flex flex-col">
                            <span class="font-semibold block mb-1">
                                <i class="fa-regular fa-calendar text-[#800000] mr-1"></i>Date
                            </span>
                            <?php
                            date_default_timezone_set('Asia/Manila');

                            // Kunin yung raw booking_date galing sa database
                            $rawDate = $room['booking_date'];  

                           
                            $currentDate = date("F d, Y", strtotime($rawDate));

                            // Add two days
                            $nextDate = date("F d, Y", strtotime($rawDate . " +2 days"));

                            echo "<p>$currentDate — $nextDate</p>";
                            ?>

                        </div>

                        <div class="flex flex-col">
                            <span class="font-semibold mb-1">
                                <i class="fa-solid fa-door-open text-[#800000] mr-1"></i>Check In
                            </span>
                            <div class="bg-[#f8f8f8] border border-[#dcdcdc] py-3 px-5 rounded-md text-gray-700 text-center text-lg">
                                2:00 PM
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <span class="font-semibold mb-1">
                                <i class="fa-solid fa-door-closed text-[#800000] mr-1"></i>Check Out
                            </span>
                            <div class="bg-[#f8f8f8] border border-[#dcdcdc] py-3 px-5 rounded-md text-gray-700 text-center text-lg">
                                12:00 AM
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="">Report Issue</a>
                </div>

                <!-- View Details Button -->
                <div class="mt-auto">
                       <a href="viewdetails?id=<?= $room['id'] ?>"
                        class="block w-full text-center px-8 py-5 text-[#333333] font-semibold rounded-b-3xl hover:bg-yellow-600 border-t border-[#b1b1b1] transition text-lg sm:text-xl md:text-2xl">
                        View Details <i class="fa-regular fa-file-lines ml-2"></i>
                    </a>

                </div>
            </div>

        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../App/layout.php";
?>