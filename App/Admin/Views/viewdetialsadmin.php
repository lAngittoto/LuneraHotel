<?php


ob_start();
require "header.php";


if (!isset($room) || !is_array($room)) {
    echo "<p class='p-6 text-center text-red-600'>Room information is not available.</p>";
    $content = ob_get_clean();
    include __DIR__ . "/../../../App/layout.php";
    exit;
}


if (!is_array($amenities)) {
    $amenities = [];
}

$statusClass = $statusClass ?? '';
?>
<?php


// Determine last page
$lastPage = $_SERVER['HTTP_REFERER'] ?? '/LuneraHotel/App/Public/allbookings';
?>
<div>
    <a href="<?= htmlspecialchars($lastPage) ?>">
        <i class="fa-solid fa-arrow-left text-4xl text-[#800000] px-10 mt-10"></i>
    </a>
</div>

<section class="w-full bg-[#f8f8f8] p-6 sm:p-8 md:p-10 lg:p-12 flex flex-col md:flex-col lg:flex-row justify-around items-start gap-6 md:gap-8 lg:gap-12 select-none">
    <!-- Room Info -->

    <div class="flex flex-col w-full md:w-full lg:w-[40%] border border-[#b8b8b8] bg-[#ffffff] text-[#333333] rounded-2xl shadow-2xl gap-4">
        <img src="<?= htmlspecialchars($room['img']) ?>" alt="Room Image"
            class="w-full h-64 sm:h-72 md:h-80 lg:h-96 object-cover rounded-t-2xl">

        <div class="p-5 sm:p-6 md:p-8 lg:p-10 flex flex-col gap-4">
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-4xl font-semibold text-[#333333]">
                <?= htmlspecialchars($room['room_type']) ?>
            </h1>

            <div class="flex flex-col md:flex-col lg:flex-row w-full justify-between items-start lg:items-center gap-3 text-xs sm:text-sm md:text-base lg:text-lg">
                <span class="<?= htmlspecialchars($statusClass) ?> px-3 py-1 rounded-4xl"><?= htmlspecialchars($room['status']) ?></span>
                <p><i class='fa-solid fa-door-closed text-[#800000]'></i> Room <?= htmlspecialchars($room['room_number']) ?></p>
                <p><i class="fa-regular fa-user text-[#800000]"></i> Up to <?= htmlspecialchars($room['people']) ?> People</p>
            </div>

            <p class="text-xs sm:text-sm md:text-base lg:text-lg mb-3"><?= htmlspecialchars($room['description']) ?></p>

            <h2 class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-[#333333] mb-3">Amenities</h2>
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



</section>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../App/layout.php";
