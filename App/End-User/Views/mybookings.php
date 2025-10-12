<?php
$title = "My Bookings";
ob_start();

if (!isset($_SESSION['user'])) {
      header('Location: /LuneraHotel/App/Public');
    exit;
}

require_once __DIR__ . "/../models/db.php";
require_once __DIR__ . "/header.php"; // ✅ navbar/header
?>

<section class="w-full bg-[#f8f8f8] p-10 flex flex-col items-center">
    <h1 class="text-5xl mb-10 text-[#333333]">My Bookings</h1>
</section>

<?php
$userEmail = $_SESSION['user']['email'];

// Kunin lahat ng bookings ng user + details ng room
// Kunin lahat ng bookings ng user na HINDI pa completed
$stmt = $pdo->prepare("
    SELECT r.*, b.booking_date, b.status AS booking_status
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_email = ? AND b.status != 'Completed'
");
$stmt->execute([$userEmail]);
$bookedRooms = $stmt->fetchAll();

$stmt->execute([$userEmail]);
$bookedRooms = $stmt->fetchAll();

if (!$bookedRooms) {
    echo "<p class='text-2xl text-gray-600 text-center'>You have no bookings yet.</p>";
} else {
    echo "<div class='flex flex-col gap-8 p-10 max-w-4xl mx-auto'>";
    foreach ($bookedRooms as $room): ?>

    <div class="border border-[#dcdcdc] bg-white p-6 sm:p-8 md:p-10 rounded-2xl shadow-lg flex flex-col gap-5 max-w-full mx-auto lg:max-w-3xl">

    <!-- Room Image -->
    <img src="<?= $room['img'] ?>" alt="Room Image"
         class="w-full h-56 sm:h-72 md:h-80 lg:h-96 object-cover rounded-xl mb-5">

    <!-- Room Info -->
    <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-3xl font-bold text-gray-900"><?= $room['room_type'] ?></h2>
    <p class="text-sm sm:text-base md:text-lg text-gray-700 mb-2">Room <?= $room['room_number'] ?></p>

    <!-- Guests -->
    <p class="flex items-center gap-2 text-sm sm:text-base md:text-lg text-gray-700 mb-2">
        <i class="fa-regular fa-user text-[#800000]"></i> 
        <?= $room['people'] ?> Guests
    </p>

    <!-- Floor -->
    <div class="flex items-center gap-2 mb-4">
        <i class="fa-solid fa-building text-[#800000]"></i>
        <p class="text-sm sm:text-base md:text-lg text-gray-600"><?= $room['floor'] ?></p>
    </div>
    
    <!-- Check In / Out -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Check In -->
        <div class="flex flex-col">
            <label for="in" class="mb-1 font-medium text-sm sm:text-base md:text-lg">Check In</label>
            <div class="flex items-center gap-2">
                <i class="fa-regular fa-clock text-[#800000]"></i>
                <input type="text" name="in" readonly disabled
                       placeholder="9:30"
                       class="outline-none bg-[#f8f8f8] py-2 px-3 w-full border border-[#dcdcdc] rounded-lg text-sm sm:text-base md:text-lg">
            </div>
        </div>

        <!-- Check Out -->
        <div class="flex flex-col">
            <label for="out" class="mb-1 font-medium text-sm sm:text-base md:text-lg">Check Out</label>
            <div class="flex items-center gap-2">
                <i class="fa-regular fa-clock text-[#800000]"></i>
                <input type="text" name="out" readonly disabled
                       placeholder="9:30"
                       class="outline-none bg-[#f8f8f8] py-2 px-3 w-full border border-[#dcdcdc] rounded-lg text-sm sm:text-base md:text-lg">
            </div>
        </div>
    </div>

    <!-- Button -->
    <a href="index.php?page=viewdetails&room=<?= $room['id'] ?>"
       class="mt-6 block w-full text-center px-5 py-3 bg-[#800000] text-white rounded-xl shadow hover:bg-red-900 transition text-sm sm:text-base md:text-lg">
       View Details <i class="fa-regular fa-file-lines ml-2"></i>
    </a>
</div>

<?php endforeach;
    echo "</div>";
}
?>

<?php
$content = ob_get_clean();
include __DIR__ .'/../../../App/layout.php';
?>