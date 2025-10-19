<?php ob_start(); ?>
<section class="">
  <div class="bg-[#8b2d2d] not-visited:w-screen px-5 py-5 flex flex-row justify-between text-[#ffffff] font-mono items-center select">
    <h1 class="text-3xl">Lunera Hotel</h1>
    <a href="/LuneraHotel/App/Auth/Controllers/logout.php">Log out</a>
  </div>

  <!-- Dashboard Content -->
  <main class="dashboard">
    <h2>Admin Dashboard</h2>
    <p>Welcome back, <?= htmlspecialchars($_SESSION['user']['email']) ?>. Here is an overview of your hotel.</p>

    <div class="card-container">
      <!-- Total Rooms -->
      <div class="card">
        <h3>Total Rooms <i class="fa-solid fa-bed"></i></h3>
        <p class="description">The total number of rooms in the hotel.</p>
       <span class="number"><?= htmlspecialchars($totalRooms) ?></span>
      </div>

      <!-- Available Rooms -->
      <div class="card">
        <h3>Available Rooms <i class="fa-solid fa-bed" style="color:green;"></i></h3>
        <p class="description">Rooms currently available for booking.</p>
      <span class="number"><?= htmlspecialchars($availableRooms) ?></span>
      </div>
    </div>
  </main>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>
