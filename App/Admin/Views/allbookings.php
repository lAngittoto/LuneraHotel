<?php ob_start(); ?>
<?php require_once __DIR__ . '/header.php'; ?>
<?php require_once __DIR__ . '/../../config/Helpers/colorcoding.php'; ?>
<?php require_once __DIR__ . '/../../config/Helpers/correctgrammar.php'; ?>

<section class="mt-10 text-center text-gray-800">
    <h1 class="text-5xl font-bold">All Bookings</h1>
    <p class="text-xl text-gray-600 mt-3">A comprehensive list of all reservations, including completed history.</p>
</section>

<section class="p-6 md:p-10 w-full">
    <?php if (empty($bookedRooms)): ?>
        <p class="text-gray-600 text-lg text-center">Walang active bookings sa ngayon.</p>
    <?php else: ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

        <?php foreach ($bookedRooms as $room): ?>
            <?php $statusClass = getStatusClass($room['booking_status']); ?>

            <div class="bg-white border border-gray-300 rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">

                <!-- Image -->
                <div class="w-full h-36 md:h-40">
                    <img src="<?= htmlspecialchars($room['img'] ?? 'default.jpg') ?>" 
                         class="w-full h-full object-cover">
                </div>

                <!-- Content -->
                <div class="p-4 flex flex-col justify-between flex-grow">

                    <!-- Name + Status -->
                    <div>
                        <div class="flex justify-between items-start">
                            <h2 class="text-lg font-bold leading-tight">
                                <?= htmlspecialchars($room['room_type']) ?>
                            </h2>
                            <span class="px-3 py-1 text-xs rounded-lg <?= $statusClass ?>">
                                <?= htmlspecialchars($room['booking_status']) ?>
                            </span>
                        </div>

                        <p class="text-gray-600 text-xs mt-1">
                            <i class="fa-solid fa-circle-user text-[#800000] mr-1"></i>
                            Booked by <span class="font-semibold"><?= htmlspecialchars($room['user_email']) ?></span>
                        </p>
                    </div>

                    <!-- Info -->
                    <div class="mt-3 text-gray-700 text-xs space-y-1">

                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-bed text-[#800000] text-sm"></i>
                            <span><?= htmlspecialchars($room['room_type']) ?></span>
                        </div>

                        <?php if (!empty($room['floor'])): ?>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-building text-[#800000] text-sm"></i>
                            <span>Floor <?= htmlspecialchars(convertFloor($room['floor'])) ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-user-group text-[#800000] text-sm"></i>
                            <span><?= htmlspecialchars(correctGuest($room['people'])) ?></span>
                        </div>
                    </div>

                    <!-- Check-in / Check-out -->
                    <?php
                    date_default_timezone_set('Asia/Manila');
                    $rawDate = $room['booking_date'] ?? null;

                    if ($rawDate && strtotime($rawDate) !== false) {
                        $checkInDate  = date("M d, Y", strtotime($rawDate));
                        $checkOutDate = date("M d, Y", strtotime($rawDate . " +2 days"));
                    } else {
                        $checkInDate  = '—';
                        $checkOutDate = '—';
                    }
                    ?>

                    <div class="mt-4 text-xs bg-gray-50 rounded-lg p-3 space-y-2">

                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-calendar-check text-[#800000] text-lg"></i>
                            <span><?= $checkInDate ?> — <?= htmlspecialchars($room['check_in_time'] ?? '2:00 PM') ?></span>
                        </div>

                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-calendar-xmark text-[#800000] text-lg"></i>
                            <span><?= $checkOutDate ?> — <?= htmlspecialchars($room['check_out_time'] ?? '12:00 PM') ?></span>
                        </div>

                    </div>
                    

                    <!-- View Details -->
                    <a href="viewdetailsadmin?id=<?= $room['id'] ?>"
                        class="mt-4 block text-center bg-[#800000] text-white text-sm px-3 py-2 rounded-lg hover:bg-[#a52a2a] transition">
                        View Details
                    </a>

                </div>

            </div>
        <?php endforeach; ?>

    </div>

    <?php endif; ?>
</section>


<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';






?>