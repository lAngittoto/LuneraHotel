<?php
ob_start();
?>

<header class="bg-[#8b2d2d] w-screen p-7 sm:p-10 flex flex-col sm:flex-row justify-between items-center">

    <!-- Logo & Title -->
    <h1 class="flex items-center gap-4 text-white text-4xl sm:text-5xl md:text-6xl cursor-default select-none mb-5 sm:mb-0">
        <img src="images/logo.jpg" alt="Lunera Hotel Logo" class="w-14 sm:w-16 md:w-20 rounded-full border-2 border-white">
        Lunera Hotel
    </h1>

    <!-- Navigation Links -->
    <nav class="flex flex-col sm:flex-row gap-5 sm:gap-10 justify-center items-center text-white text-2xl font-light select-none">
        <a href="/LuneraHotel/App/Public/rooms" class="hover:text-gray-300 transition-colors duration-200">Rooms</a>
        <a href="/LuneraHotel/App/Public/mybookings" class="hover:text-gray-300 transition-colors duration-200">My Bookings</a>
        <a href="/LuneraHotel/App/Auth/Controllers/logout.php" class="hover:text-gray-300 transition-colors duration-200">Log Out</a>
    </nav>

</header>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>
