<?php ob_start(); ?>
<?php require_once __DIR__ . "/header.php"; ?>

<section class="w-full bg-[#f8f8f8] p-10 flex flex-col items-center">
    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-10 text-[#333333] text-center">My Bookings</h1>
</section>

<?php if (empty($bookedRooms)): ?>
    <p class="text-xl sm:text-2xl text-gray-600 text-center">You have no bookings yet.</p>
<?php else: ?>
    <div class="flex flex-col gap-8 p-6 sm:p-6 md:p-10 max-w-4xl mx-auto">
        <?php foreach ($bookedRooms as $room): ?>
            <div class="border border-[#dcdcdc] bg-white p-6 sm:p-6 md:p-10 rounded-2xl shadow-lg flex flex-col gap-4 max-w-full mx-auto">

                <!-- Room Image -->
                <div class="w-full relative overflow-hidden rounded-xl mb-5" style="aspect-ratio: 16/9;">
                    <img src="<?= htmlspecialchars($room['img']) ?>" alt="Room Image"
                         class="absolute inset-0 w-full h-full object-cover">
                </div>

                <!-- Room Info -->
                <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-3xl font-bold text-gray-900">
                    <?= htmlspecialchars($room['room_type']) ?>
                </h2>

                <p class="text-xs sm:text-sm md:text-base lg:text-base text-gray-700 mb-2">
                    Room <?= htmlspecialchars($room['room_number']) ?>
                </p>

                <p class="flex items-center gap-2 text-xs sm:text-sm md:text-base lg:text-lg text-gray-700 mb-2">
                    <i class="fa-regular fa-user text-[#800000]"></i> 
                    <?= htmlspecialchars($room['people']) ?> Guests
                </p>

                <?php if (!empty($room['floor'])): ?>
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-building text-[#800000]"></i>
                        <p class="text-xs sm:text-sm md:text-base lg:text-base text-gray-600">
                            <?= htmlspecialchars($room['floor']) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <p class="text-xs sm:text-sm md:text-base lg:text-base text-gray-700 font-semibold mb-4">
                    Status: <?= htmlspecialchars($room['booking_status']) ?>
                </p>

                <!-- Date -->
                <div class="flex flex-col mb-4">
                    <label class="mb-1 font-medium text-xs sm:text-sm md:text-base">Date</label>
                    <input type="text" readonly disabled placeholder="Ex. 05-06-2025"
                           class="outline-none bg-[#f8f8f8] py-2 sm:py-3 px-3 sm:px-4 w-full border border-[#dcdcdc] rounded-lg text-xs sm:text-sm md:text-base">
                </div>

                <!-- Check In / Check Out -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <?php foreach (['Check In', 'Check Out'] as $time): ?>
                        <div class="flex flex-col">
                            <label class="mb-1 font-medium text-xs sm:text-sm md:text-base"><?= $time ?></label>
                            <input type="text" readonly disabled placeholder="9:30"
                                   class="outline-none bg-[#f8f8f8] py-2 sm:py-3 px-3 sm:px-4 w-full border border-[#dcdcdc] rounded-lg text-xs sm:text-sm md:text-base">
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- View Details Button -->
                <a href="index.php?page=viewdetails&room=<?= $room['id'] ?>"
                   class="mt-2 block w-full text-center px-5 py-3 bg-[#800000] text-white rounded-xl shadow hover:bg-red-900 transition text-sm sm:text-base md:text-lg">
                   View Details <i class="fa-regular fa-file-lines ml-2"></i>
                </a>

            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../App/layout.php";
?>
