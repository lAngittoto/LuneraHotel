<?php ob_start();
require_once __DIR__ . '/header.php';
?>

<section class="select-none">


  <!-- Dashboard Content -->
  <main class="dashboard">
    <h2>Admin Dashboard</h2>
    <p>Welcome back, <?= htmlspecialchars($_SESSION['user']['email']) ?>. Here is an overview of your hotel.</p>

    <div class="card-container">
      <!-- Total Rooms -->
      <div class="card">
        <h3>Total Rooms <i class="fa-solid fa-bed text-4xl"></i></h3>
        <p class="description">The total number of rooms in the hotel.</p>
        <span class="number"><?= htmlspecialchars($totalRooms) ?></span>
      </div>

      <!-- Available Rooms -->
      <div class="card">
        <h3>Available Rooms <i class="fa-solid fa-bed text-green-700 text-4xl"></i></h3>
        <p class="description">Rooms currently available for booking.</p>
        <span class="number"><?= htmlspecialchars($availableRooms) ?></span>
      </div>

      <!-- Booked -->
      <div class="card">
        <h3>Total Bookings <i class="fa-solid fa-book-open text-4xl text-orange-700"></i></h3>
        <p class="description">All-time booking count.</p>
        <span class="number"><?= htmlspecialchars($bookings) ?></span>
      </div>
      <!-- Under Maintenance -->
      <div class="card">
        <h3>Maintenance<i class="fa-solid fa-screwdriver-wrench text-4xl text-red-700"></i></h3>
        <p class="description">Rooms currently under maintenance.</p>
        <span class="number"><?= htmlspecialchars($undermaintenance) ?></span>
      </div>

      <!-- Dirty -->
      <div class="card">
        <h3>Dirty<i class=" text-4xl fa-solid fa-broom text-orange-700"></i></h3>
        <p class="description">Rooms currently need to clean.</p>
        <span class="number"><?= htmlspecialchars($dirty) ?></span>
      </div>
    </div>
    <section class="mt-10 ">
      <div class=" flex flex-col gap-10 md:w-[50vw] w-full">
        <div class="card">
          <h3><i class="fa-solid fa-list-check text-[#800000]"></i>Manage Rooms</h3>
          <p class="description">View, edit or update the status of all rooms in the hotel.</p> <br>
          <a href="/LuneraHotel/App/Public/managerooms" class=" text-[#ffffff] bg-[#800000] py-3 px-5 rounded-xl sm:text-[0.8rem] text-[0.7rem] ">Go to Room Management <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="card">
          <h3><i class="fa-solid fa-eye text-[#800000]"></i>View All Bookings</h3>
          <p class="description">See a comprehensive list of all historical and upcoming bookings.</p> <br>
          <a href="/LuneraHotel/App/Public/allbookings" class=" text-[#ffffff] bg-[#800000] py-3 px-5 rounded-xl sm:text-[0.8rem] text-[0.7rem]">Go to Bookings <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="card">
          <h3><i class="fa-solid fa-chart-simple text-[#800000]"></i>Bookings Popularity Report</h3>
          <p class="description">View a detailed report of the most popular rooms based on booking frequency.</p> <br>
          <a href="/LuneraHotel/App/Public/popularity" class=" text-[#ffffff] bg-[#800000] py-3 px-5 rounded-xl sm:text-[0.8rem] text-[0.7rem]">View Full Report <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </div>
    </section>
    </div>
  </main>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>