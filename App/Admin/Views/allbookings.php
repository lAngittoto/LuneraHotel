<?php ob_start(); ?>
<?php require_once __DIR__ . '/header.php'; ?>
<?php require_once __DIR__ . '/../../config/Helpers/colorcoding.php'; ?>
<?php require_once __DIR__ . '/../../config/Helpers/correctgrammar.php'; ?>

<section class="mt-10 text-center text-gray-800">
    <h1 class="text-5xl font-bold">All Bookings</h1>
    <p class="text-xl text-gray-600 mt-3">A comprehensive list of all reservations, including completed history.</p>
</section>

<section class="p-8 flex flex-col items-center gap-10">
    <?php if (empty($bookedRooms)): ?>
        <p class="text-gray-600 text-lg">Walang active bookings sa ngayon.</p>
    <?php else: ?>
        <?php foreach ($bookedRooms as $room): ?>
            <?php $statusClass = getStatusClass($room['booking_status']); ?>

            <div class="flex flex-col md:flex-row bg-white border border-gray-300 rounded-3xl shadow-lg overflow-hidden hover:shadow-2xl transition w-full max-w-5xl">
                <!-- Room Image -->
                <div class="w-full md:w-2/5 h-72 md:h-auto">
                    <img src="<?= htmlspecialchars($room['img'] ?? 'default.jpg') ?>"
                        alt="Room Image"
                        class="w-full h-full object-cover hover:scale-105 transition duration-500">
                </div>

                <!-- Room Details -->
                <div class="w-full md:w-3/5 p-8 flex flex-col justify-between text-gray-800">
                    <!-- Room Name + Status -->
                    <div>
                        <div class="flex flex-col sm:flex-row justify-between items-center sm:items-start gap-3">
                            <h2 class="text-2xl sm:text-3xl font-bold"><?= htmlspecialchars($room['room_type']) ?></h2>
                            <span class="px-4 py-2 rounded-2xl text-sm sm:text-base <?= $statusClass ?> w-fit whitespace-nowrap">
                                <?= htmlspecialchars($room['booking_status']) ?>
                            </span>
                        </div>

                        <p class="text-gray-600 mt-2 text-base sm:text-lg text-center sm:text-left">
                            <i class="fa-solid fa-circle-user text-[#800000] mr-2"></i>
                            Booked by <span class="font-semibold"><?= htmlspecialchars($room['user_email']) ?></span>
                        </p>
                    </div>

                    <!-- Room Info -->
                    <div class="grid grid-cols-2 gap-3 mt-6 text-gray-700 text-base sm:text-lg">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-bed text-[#800000] text-lg"></i>
                            <span><?= htmlspecialchars($room['room_type']) ?></span>
                        </div>

<?php if (!empty($room['floor'])): ?>
    <div class="flex items-center gap-3">
        <i class="fa-solid fa-building text-[#800000] text-lg"></i>
        <span>Floor  <?= htmlspecialchars(convertFloor($room['floor'])) ?></span>
    </div>
<?php endif; ?>


                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-user-group text-[#800000] text-lg"></i>
                            <span>
                                <i class="fa-regular fa-user text-[#800000]"></i>
                                <?= htmlspecialchars(correctGuest($room['people'])) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Check In / Out -->
                    <div class="mt-6 border-t border-gray-300 pt-5 bg-gray-50 rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <!-- Check In -->
                        <?php
                        date_default_timezone_set('Asia/Manila');

                        $rawDate = $room['booking_date'] ?? null;

                        if ($rawDate && strtotime($rawDate) !== false) {
                            $checkInDate  = date("F d, Y", strtotime($rawDate));
                            $checkOutDate = date("F d, Y", strtotime($rawDate . " +2 days"));
                        } else {
                            $checkInDate  = '—';
                            $checkOutDate = '—';
                        }
                        ?>

                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3">
                            <i class="fa-regular fa-calendar-check text-[#800000] text-2xl"></i>
                            <div>
                                <p class="font-semibold text-gray-800 text-base">Check In</p>
                                <p class="text-gray-700 text-[1.1rem] mt-1">
                                    <i class="fa-solid fa-calendar-day text-[#800000] mr-1"></i>
                                    <?= htmlspecialchars($checkInDate) ?>
                                </p>
                                <p class="text-gray-700 text-[1.1rem]">
                                    <i class="fa-solid fa-clock text-[#800000] mr-1"></i>
                                    <?= htmlspecialchars($room['check_in_time'] ?? '2:00 PM') ?>
                                </p>
                            </div>
                        </div>

                        <!-- Check Out -->
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3">
                            <i class="fa-regular fa-calendar-xmark text-[#800000] text-2xl"></i>
                            <div>
                                <p class="font-semibold text-gray-800 text-base">Check Out</p>
                                <p class="text-gray-700 text-[1.1rem] mt-1">
                                    <i class="fa-solid fa-calendar-day text-[#800000] mr-1"></i>
                                    <?= htmlspecialchars($checkOutDate) ?>
                                </p>
                                <p class="text-gray-700 text-[1.1rem]">
                                    <i class="fa-solid fa-clock text-[#800000] mr-1"></i>
                                    <?= htmlspecialchars($room['check_out_time'] ?? '12:00 PM') ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- View Details Button -->
                    <a href="viewdetailsadmin?id=<?= $room['id'] ?>"
                        class="mt-6 block w-full text-center px-8 py-4 border border-gray-300 rounded-2xl text-[#333333] font-semibold hover:bg-yellow-600 hover:text-white transition text-lg">
                        View Details <i class="fa-regular fa-file-lines ml-2"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';






?>