<?php ob_start();
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../../config/Helpers/colorcoding.php';
require_once __DIR__ . '/../../config/Helpers/correctgrammar.php';
?>

<?php
$topBadges = [
    1 => '<i class="fa-solid fa-crown text-yellow-500"></i> Top 1',
    2 => '<i class="fa-solid fa-crown text-gray-400"></i> Top 2',
    3 => '<i class="fa-solid fa-crown text-yellow-800"></i> Top 3'
];
?>

<!-- Header -->
<section class="mt-10 text-center text-gray-800">
    <h1 class="text-4xl md:text-5xl font-bold mb-2">Booking Popularity Report</h1>
    <p class="text-base md:text-lg text-gray-600">rooms ranked by the total number of times they have been booked.</p>
</section>

<!-- Rooms List -->
<section class="p-6 md:p-10 flex flex-col items-center gap-10 w-full">
    <?php foreach ($bookedRooms as $index => $room): ?>
        <?php $statusClass = getStatusClass($room['status'] ?? 'Available'); ?>

        <div class="flex flex-col md:flex-row bg-white border border-gray-300 rounded-2xl shadow-md hover:shadow-xl transition w-full max-w-6xl overflow-hidden">

            <!-- Room Image -->
            <div class="w-full md:w-2/5 h-72 md:h-80">
                <img
                    src="<?= htmlspecialchars($room['img']) ?>"
                    alt="Room Image"
                    class="w-full h-full object-cover hover:scale-105 transition duration-500">
            </div>

            <!-- Room Details -->
            <div class="w-full md:w-3/5 p-6 md:p-8 flex flex-col justify-between text-gray-800">


                <div>
                    <div class="flex flex-col sm:flex-row justify-between items-center sm:items-start gap-3">
                        <h2 class="text-2xl md:text-3xl font-bold text-center sm:text-left">
                            <?= htmlspecialchars($room['room_type']) ?>
                            <?php if ($index < 3): ?>
                                <span class="ml-2"><?= $topBadges[$index + 1] ?></span>
                            <?php endif; ?>
                        </h2>

                        <span class="px-4 py-2 rounded-2xl text-sm md:text-base <?= $statusClass ?> w-fit whitespace-nowrap">
                            <?= htmlspecialchars($room['total_bookings'] ?? 0) . " " . popularity($room['total_bookings'] ?? 0) ?>

                        </span>
                    </div>

                    <!-- Description -->
                    <p class="text-gray-600 mt-4 text-sm md:text-base leading-relaxed text-justify">
                        <?= htmlspecialchars($room['description']) ?>
                    </p>
                </div>

                <!-- Room Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 text-gray-700 text-sm md:text-base">
<?php if (!empty($room['floor'])): ?>
    <div class="flex items-center gap-2 md:gap-3">
        <i class="fa-solid fa-building text-[#800000] text-lg"></i>
        <span>Floor <?= htmlspecialchars(convertFloor($room['floor'])) ?></span>
    </div>
<?php endif; ?>

                    <div class="flex items-center gap-2 md:gap-3">
                        <i class="fa-solid fa-user-group text-[#800000] text-lg"></i>
                        <?= htmlspecialchars(correctGuest($room['people'])) ?>
                    </div>
                </div>

                <!-- View Details Button -->
                <a href="viewdetailsadmin?id=<?= $room['id'] ?>"
                    class="mt-6 block text-center bg-[#800000] text-white px-6 py-3 md:py-4 rounded-xl font-semibold hover:bg-[#a52a2a] transition">
                    View Details <i class="fa-regular fa-file-lines ml-2"></i>
                </a>

            </div>
        </div>
    <?php endforeach; ?>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>