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
<!-- Rooms List - Compact 4 Column -->
<section class="p-4 md:p-8 w-full">
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">

        <?php foreach ($bookedRooms as $index => $room): ?>
            <?php $statusClass = getStatusClass($room['status'] ?? 'Available'); ?>

            <div class="bg-white border border-gray-300 rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">

                <!-- Image (smaller) -->
                <div class="w-full h-36 md:h-40">
                    <img src="<?= htmlspecialchars($room['img']) ?>"
                         class="w-full h-full object-cover">
                </div>

                <!-- Content -->
                <div class="p-4 flex flex-col flex-grow justify-between">

                    <!-- Title + Badge -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 leading-tight">
                            <?= htmlspecialchars($room['room_type']) ?>
                            <?php if ($index < 3): ?>
                                <span class="ml-1 text-xs"><?= $topBadges[$index + 1] ?></span>
                            <?php endif; ?>
                        </h2>

                        <span class="mt-1 inline-block px-2 py-1 text-xs rounded-lg <?= $statusClass ?>">
                            <?= htmlspecialchars($room['total_bookings'] ?? 0) ?>
                            <?= popularity($room['total_bookings'] ?? 0) ?>
                        </span>

                        <!-- Short Description -->
                        <p class="text-gray-600 text-xs mt-2 line-clamp-3">
                            <?= htmlspecialchars($room['description']) ?>
                        </p>
                    </div>

                    <!-- Info (smaller icons/text) -->
                    <div class="mt-3 space-y-1 text-gray-700 text-xs">

                        <?php if (!empty($room['floor'])): ?>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-building text-[#800000] text-sm"></i>
                                <span>Floor <?= htmlspecialchars(convertFloor($room['floor'])) ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="flex items-center gap-1">
                            <i class="fa-solid fa-user-group text-[#800000] text-sm"></i>
                            <?= htmlspecialchars(correctGuest($room['people'])) ?>
                        </div>

                    </div>

                    <!-- Button (smaller) -->
                    <a href="viewdetailsadmin?id=<?= $room['id'] ?>"
                       class="mt-3 text-center bg-[#800000] text-white text-sm px-3 py-2 rounded-lg hover:bg-[#a52a2a] transition">
                        View Details
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>
</section>


<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>