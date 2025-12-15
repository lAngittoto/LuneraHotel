<?php ob_start();
require_once __DIR__ . '/header.php';
?>

<section class="select-none">

  <!-- Dashboard Content -->
  <main class="dashboard p-6">
    <h2 class="text-3xl font-bold mb-4">Admin Dashboard</h2>
    <p class="mb-8">Welcome back, <?= htmlspecialchars($_SESSION['user']['email']) ?>. Here is an overview of your hotel.</p>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
      <!-- Total Rooms -->
      <div class="card p-5 bg-white shadow-lg rounded-xl">
        <h3 class="text-xl font-semibold mb-2">Total Rooms <i class="fa-solid fa-bed text-2xl"></i></h3>
        <p class="description text-gray-500 mb-2">The total number of rooms in the hotel.</p>
        <span class="number text-3xl font-bold"><?= htmlspecialchars($totalRooms) ?></span>
      </div>

      <!-- Available Rooms -->
      <div class="card p-5 bg-white shadow-lg rounded-xl">
        <h3 class="text-xl font-semibold mb-2">Available Rooms <i class="fa-solid fa-bed text-green-700 text-2xl"></i></h3>
        <p class="description text-gray-500 mb-2">Rooms currently available for booking.</p>
        <span class="number text-3xl font-bold"><?= htmlspecialchars($availableRooms) ?></span>
      </div>

      <!-- Total Bookings -->
      <div class="card p-5 bg-white shadow-lg rounded-xl">
        <h3 class="text-xl font-semibold mb-2">Total Bookings <i class="fa-solid fa-book-open text-orange-700 text-2xl"></i></h3>
        <p class="description text-gray-500 mb-2">All-time booking count.</p>
        <span class="number text-3xl font-bold"><?= htmlspecialchars($bookings) ?></span>
      </div>

      <!-- Under Maintenance -->
      <div class="card p-5 bg-white shadow-lg rounded-xl">
        <h3 class="text-xl font-semibold mb-2">Maintenance <i class="fa-solid fa-screwdriver-wrench text-red-700 text-2xl"></i></h3>
        <p class="description text-gray-500 mb-2">Rooms currently under maintenance.</p>
        <span class="number text-3xl font-bold"><?= htmlspecialchars($undermaintenance) ?></span>
      </div>

      <!-- Dirty -->
      <div class="card p-5 bg-white shadow-lg rounded-xl">
        <h3 class="text-xl font-semibold mb-2">Requires Cleaning <i class="fa-solid fa-broom text-orange-700 text-2xl"></i></h3>
        <p class="description text-gray-500 mb-2">Rooms currently need cleaning.</p>
        <span class="number text-3xl font-bold"><?= htmlspecialchars($dirty) ?></span>
      </div>
    </div>

    <!-- Management / Reports Cards -->
    <section class="mt-10">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Manage Rooms -->
        <div class="card p-5 bg-white shadow-lg rounded-xl">
          <h3 class="text-xl font-semibold mb-2"><i class="fa-solid fa-list-check text-[#800000]"></i> Manage Rooms</h3>
          <p class="description text-gray-500 mb-4">View, edit or update the status of all rooms in the hotel.</p>
          <a href="/LuneraHotel/App/Public/managerooms" class="text-white bg-[#800000] py-3 px-5 rounded-xl inline-block text-sm hover:bg-[#a00000] transition">Go to Room Management <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <!-- View All Bookings -->
        <div class="card p-5 bg-white shadow-lg rounded-xl">
          <h3 class="text-xl font-semibold mb-2"><i class="fa-solid fa-eye text-[#800000]"></i> View All Bookings</h3>
          <p class="description text-gray-500 mb-4">See a comprehensive list of all historical and upcoming bookings.</p>
          <a href="/LuneraHotel/App/Public/allbookings" class="text-white bg-[#800000] py-3 px-5 rounded-xl inline-block text-sm hover:bg-[#a00000] transition">Go to Bookings <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <!-- Bookings Popularity Report -->
        <div class="card p-5 bg-white shadow-lg rounded-xl">
          <h3 class="text-xl font-semibold mb-2"><i class="fa-solid fa-chart-simple text-[#800000]"></i> Bookings Popularity Report</h3>
          <p class="description text-gray-500 mb-4">View a detailed report of the most popular rooms based on booking frequency.</p>
          <a href="/LuneraHotel/App/Public/popularity" class="text-white bg-[#800000] py-3 px-5 rounded-xl inline-block text-sm hover:bg-[#a00000] transition">View Full Report <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <!-- Annual Report -->
        <div class="card p-5 bg-white shadow-lg rounded-xl">
          <h3 class="text-xl font-semibold mb-2"><i class="fa-solid fa-chart-simple text-[#800000]"></i> Annual Report</h3>
          <p class="description text-gray-500 mb-4">Generate a comprehensive annual summary of hotel performance, including total bookings, revenue trends, room utilization, and maintenance activity.</p>
          <a href="/LuneraHotel/App/Public/annualreport" class="text-white bg-[#800000] py-3 px-5 rounded-xl inline-block text-sm hover:bg-[#a00000] transition">View Full Report <i class="fa-solid fa-arrow-right"></i></a>
        </div>
            <div class="card p-5 bg-white shadow-lg rounded-xl">
          <h3 class="text-xl font-semibold mb-2"><i class="fa-solid fa-chart-simple text-[#800000]"></i> Invenotry</h3>
          <p class="description text-gray-500 mb-4">Inveniry</p>
          <a href="/LuneraHotel/App/Public/inventory" class="text-white bg-[#800000] py-3 px-5 rounded-xl inline-block text-sm hover:bg-[#a00000] transition">View Full Report <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </div>
    </section>
  </main>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>
