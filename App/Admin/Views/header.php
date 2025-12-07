<?php
ob_start();
?>

<header class="bg-[#8b2d2d] w-screen p-7 sm:p-10 flex flex-col sm:flex-row justify-between items-center sticky top-0 z-50">

  <h1 class="flex items-center gap-4 text-white text-4xl sm:text-5xl md:text-6xl cursor-default select-none mb-5 sm:mb-0">
    <img src="images/logo.jpg" alt="Lunera Hotel Logo" class="w-14 sm:w-16 md:w-20 rounded-full border-2 border-white">
    Lunera Hotel
  </h1>

  <nav class="flex flex-col sm:flex-row gap-5 sm:gap-10 justify-center items-center text-white font-light select-none">

    <a href="/LuneraHotel/App/Public/allrooms"><span class="text-[1.3rem]">Rooms</span></a>
    <a href="/LuneraHotel/App/Public/admin"><span class="text-[1.3rem]">Dashboard</span></a>

    <!-- Notification Icon -->
    <div class="relative cursor-pointer">
      <i class="fa-solid fa-bell text-2xl"></i>

      <!-- Optional: Notification Badge -->
      <!-- <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs px-2 py-1 rounded-full">3</span> -->
    </div>

    <a href="/LuneraHotel/App/Auth/Controllers/logout.php">
      <p class="text-[1rem] px-5 py-3 bg-[#ffffff] text-[#800000] rounded-2xl shadow-2xl">Log out</p>
    </a>

  </nav>

</header>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>
