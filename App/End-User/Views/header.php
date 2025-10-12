<?php
ob_start();

?>
<header class=" bg-[#8b2d2d] w-screen h-[30%] sm:h-[10%] sm:p-10 p-7 flex flex-col sm:flex-row justify-between items-center">
  <h1 class="text-4xl sm:text-6xl md:text-7xl text-[#ffffff] cursor-default select-none mb-5 sm:mb-0 flex flex-row items-center gap-5">
    <img src="images/logo.jpg" alt="logo" class="w-10">
    Lunare Hotel
  </h1>
  <div class="flex flex-col sm:flex-row gap-5 sm:gap-10 justify-center items-center text-[#ffffff] text-[1.2rem] sm:text-xl font-light select-none">
    <a href="/LuneraHotel/App/Public/rooms" class=" text-[1rem]">Rooms</a>
    <a href="/LuneraHotel/App/Public/mybookings" class=" text-[1rem]">My Booking</a>
    <a href="/LuneraHotel/App/Auth/Controllers/logout.php"  class=" text-[1rem]">Log out</a>
  </div>
</header>


<?php
// Save everything above into $content
$content = ob_get_clean();

// Then include the layout
include __DIR__ . '/../../../App/layout.php';

?>