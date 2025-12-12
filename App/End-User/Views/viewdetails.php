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
$statusClass = getStatusClass($displayStatus);
?>

<div class="p-6 md:p-10">
    <a href="/LuneraHotel/App/Public/rooms" class="inline-block mb-6 text-[#800000] hover:text-[#600000] transition">
        <i class="fa-solid fa-arrow-left text-3xl md:text-4xl"></i>
    </a>

    <section class="flex flex-col lg:flex-row gap-8 lg:gap-12 select-none">

        <!-- Room Info -->
        <div class="w-full lg:w-2/5 bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <img src="<?= htmlspecialchars($room['img']) ?>" 
                alt="Room Image"
                class="w-full h-64 md:h-80 lg:h-96 object-cover">

            <div class="p-6 md:p-8 flex flex-col gap-4">
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-semibold text-gray-800">
                    <?= htmlspecialchars($room['room_type']) ?>
                </h1>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm md:text-base">
                    <span class="<?= htmlspecialchars($statusClass ?? '') ?> px-4 py-1 rounded-full font-medium text-white">
                        <?= htmlspecialchars($displayStatus) ?>
                    </span>
                    <p class="flex items-center gap-2 text-gray-700">
                        <i class='fa-solid fa-door-closed text-[#800000]'></i> Room <?= htmlspecialchars($room['room_number']) ?>
                    </p>
                    <p class="flex items-center gap-2 text-gray-700">
                        <i class="fa-regular fa-user text-[#800000]"></i>
                        <?= htmlspecialchars(correctGrammar($room['people'])) ?>
                    </p>
                </div>

                <p class="text-gray-700 text-sm md:text-base mt-3">
                    <?= htmlspecialchars($room['description']) ?>
                </p>

                <h2 class="text-lg md:text-2xl font-semibold text-gray-800 mt-6">Amenities</h2>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 md:gap-4 mt-2 text-gray-700">
                    <?php if (count($amenities) === 0): ?>
                        <li class="italic text-gray-400">No amenities listed.</li>
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
        <div class="w-full lg:w-3/5 bg-white border border-gray-200 rounded-xl shadow-lg p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 text-center lg:text-left">Report an Issue for this Room</h2>

            <?php if (isset($_SESSION['message'])): ?>
                <p class="text-green-600 mb-6 font-medium">
                    <?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
                </p>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <p class="text-red-600 mb-6 font-medium">
                    <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="/LuneraHotel/App/Public/index.php?page=viewdetails&id=<?= htmlspecialchars($room['id']) ?>" class="space-y-5" enctype="multipart/form-data">
                <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']) ?>">

                <div>
                    <label for="description" class="block mb-2 font-medium text-gray-700">Issue Description:</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="5" 
                        class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                        placeholder="Describe the issue..."
                        required
                    ></textarea>
                </div>

                <div>
                    <label for="images" class="block mb-2 font-medium text-gray-700">Attach Images (optional):</label>
                    <input 
                        type="file" 
                        name="images[]" 
                        id="images" 
                        accept="image/*" 
                        multiple
                        class="w-full"
                    >
                    <p class="text-xs text-gray-500 mt-2">You can upload multiple images (jpg, png, gif). Max 5 files, 5MB each.</p>
                </div>

                <button 
                    type="submit" 
                    class="w-full md:w-auto px-6 py-3 bg-red-600 text-white font-semibold rounded-xl shadow-md hover:bg-red-700 hover:shadow-lg transition-all duration-300"
                >
                    Submit Issue
                </button>
            </form>
        </div>

    </section>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../App/layout.php";
?>
