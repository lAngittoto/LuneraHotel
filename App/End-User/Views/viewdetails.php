<?php
$title = $title ?? "View Room Details";
ob_start();
require "header.php";

// Safety guard
if (!isset($room) || !is_array($room)) {
    echo "<p class='p-6 text-center text-red-600'>Room information is not available.</p>";
    $content = ob_get_clean();
    include __DIR__ . "/../../../App/layout.php";
    exit;
}

// Ensure $amenities is an array
if (!is_array($amenities)) {
    $amenities = [];
}

// Role & status
$role = $_SESSION['user']['role'] ?? 'user';
$displayStatus = $room['status'];
if ($role !== 'admin' && strtolower($room['status']) === 'dirty') {
    $displayStatus = 'Unavailable';
}

// Status class for badge

?>
<div>
    <a href="/LuneraHotel/App/Public/rooms">
        <i class="fa-solid fa-arrow-left text-4xl text-[#800000] px-10 mt-10"></i>
    </a>
</div>
<section class="w-full bg-[#f8f8f8] p-6 sm:p-8 md:p-10 lg:p-12 flex flex-col md:flex-col lg:flex-row justify-around items-start gap-6 md:gap-8 lg:gap-12 select-none">

    <!-- Room Info -->
    <div class="flex flex-col w-full md:w-full lg:w-[40%] border border-[#dcdcdc] bg-[#ffffff] text-[#333333] rounded-lg shadow-sm gap-4">
        <img src="<?= htmlspecialchars($room['img']) ?>" alt="Room Image"
            class="w-full h-64 sm:h-72 md:h-80 lg:h-96 object-cover rounded-t-lg">

        <div class="p-5 sm:p-6 md:p-8 lg:p-10 flex flex-col gap-4">
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-4xl font-semibold text-[#333333]">
                <?= htmlspecialchars($room['room_type']) ?>
            </h1>

            <div class="flex flex-col md:flex-col lg:flex-row w-full justify-between items-start lg:items-center gap-3 text-xs sm:text-sm md:text-base lg:text-lg">
                <span class="<?= htmlspecialchars($statusClass) ?> px-3 py-1 rounded-4xl"><?= htmlspecialchars($displayStatus) ?></span>
                <p><i class='fa-solid fa-door-closed text-[#800000]'></i> Room <?= htmlspecialchars($room['room_number']) ?></p>
                <p>
                    <i class="fa-regular fa-user text-[#800000]"></i>
                    <?= htmlspecialchars(correctGrammar($room['people'])) ?>
                </p>
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

    <!-- Report Issue Form -->
    <section class="w-full max-w-xl mx-auto mt-10 p-8 bg-white border border-gray-200 rounded-2xl shadow-lg sm:p-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center sm:text-left">Report an Issue for this Room</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <p class="text-green-600 mb-6 text-center sm:text-left font-medium">
            <?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="/LuneraHotel/App/Public/index.php?page=viewdetails&id=<?= $room['id'] ?>" class="space-y-5">
        <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']) ?>">

        <div>
            <label for="description" class="block mb-2 text-gray-700 font-medium">Issue Description:</label>
            <textarea 
                name="description" 
                id="description" 
                rows="5" 
                class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                placeholder="Describe the issue..."
                required
            ></textarea>
        </div>

        <button 
            type="submit" 
            class="w-full sm:w-auto px-6 py-3 bg-red-600 text-white font-semibold rounded-xl shadow-md hover:bg-red-700 hover:shadow-lg transition-all duration-300"
        >
            Submit Issue
        </button>
    </form>
</section>


</section>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../App/layout.php";
?>
