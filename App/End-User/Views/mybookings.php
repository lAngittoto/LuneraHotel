<?php ob_start(); ?>
<?php require_once __DIR__ . "/header.php"; ?>

<section class="w-full bg-[#f8f8f8] p-10 flex flex-col items-center">
    <h1 class="text-5xl mb-10 text-[#333333]">My Bookings</h1>
</section>

<?php if (empty($bookedRooms)): ?>
    <p class="text-2xl text-gray-600 text-center">You have no bookings yet.</p>
<?php else: ?>
    <div class="flex flex-col gap-8 p-10 max-w-4xl mx-auto">
        <?php foreach ($bookedRooms as $room): ?>
            <div class="border border-[#dcdcdc] bg-white p-6 sm:p-8 md:p-10 rounded-2xl shadow-lg flex flex-col gap-5 max-w-full mx-auto lg:max-w-3xl">
                <img src="<?= htmlspecialchars($room['img']) ?>" alt="Room Image"
                     class="w-full h-56 sm:h-72 md:h-80 lg:h-96 object-cover rounded-xl mb-5">

                <h2 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($room['room_type']) ?></h2>
                <p class="text-base text-gray-700 mb-2">Room <?= htmlspecialchars($room['room_number']) ?></p>

                <p class="flex items-center gap-2 text-lg text-gray-700 mb-2">
                    <i class="fa-regular fa-user text-[#800000]"></i> 
                    <?= htmlspecialchars($room['people']) ?> Guests
                </p>

                <?php if (!empty($room['floor'])): ?>
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-building text-[#800000]"></i>
                        <p class="text-base text-gray-600"><?= htmlspecialchars($room['floor']) ?></p>
                    </div>
                <?php endif; ?>

                <p class="text-base text-gray-700 font-semibold">
                    Status: <?= htmlspecialchars($room['booking_status']) ?>
                </p>

                <a href="index.php?page=viewdetails&room=<?= $room['id'] ?>"
                   class="mt-6 block w-full text-center px-5 py-3 bg-[#800000] text-white rounded-xl shadow hover:bg-red-900 transition">
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
