<?php ob_start(); ?>
<section class="select-none">
  <div class="bg-[#8b2d2d] not-visited:w-screen px-5 py-5 flex flex-row justify-between text-[#ffffff] font-mono items-center select">
    <h1 class="text-3xl">Lunera Hotel</h1>
    <a href="/LuneraHotel/App/Auth/Controllers/logout.php" class=" text-2xl">Log out</a>
  </div>

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
          <span class="number"><?= htmlspecialchars($bookings)?></span>
       </div>
       <!-- Under Maintenance -->
        <div class="card">
          <h3>Maintenance<i class="fa-solid fa-screwdriver-wrench text-4xl text-red-700"></i></h3>
          <p class="description">Rooms currently under maintenance.</p>
          <span class="number"><?= htmlspecialchars($undermaintenance)?></span>
        </div>
    </div>
  </main>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>
