<?php

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /LuneraHotel/App/Public');
    exit;
}


//require_once __DIR__ . '/../models/db.php';
$title = 'Admin Dashboard';

ob_start();

// total rooms
//$stmt = $pdo->query("SELECT COUNT(*) AS total FROM rooms");
//$totalRooms = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// available rooms
//$stmt = $pdo->query("SELECT COUNT(*) AS available FROM rooms WHERE status = 'Available'");
//$availableRooms = $stmt->fetch(PDO::FETCH_ASSOC)['available'];
?>
<section class=" select-none">
<div class="  bg-[#8b2d2d] not-visited:w-screen px-5 py-5 flex flex-row justify-between text-[#ffffff] font-mono items-center select">
<h1 class=" text-3xl">Lunera Hotel</h1>
<a href="/LuneraHotel/App/Auth/Controllers/logout.php">Log out</a>
</div>
  

<!-- Dashboard Content -->
<main class="dashboard">
  <h2>Admin Dashboard</h2>
  <p >Welcome back, <?= htmlspecialchars($_SESSION['user']['email']) ?>. Here is an overview of your hotel.</p>

  <div class="card-container">
    <!-- Total Rooms -->
    <div class="card">
      <h3 >Total Rooms <i class="fa-solid fa-bed"></i></h3>
      <p class="description">The total number of rooms in the hotel.</p>
      <span class="number"><?= $totalRooms ?></span>
    </div>

    <!-- Available Rooms -->
    <div class="card">
      <h3>Available Rooms <i class="fa-solid fa-bed" style="color:green;"></i></h3>
      <p class="description">Rooms currently available for booking.</p>
      <span class="number"><?= $availableRooms ?></span>
    </div>
  </div>
</main>
</section>
<?php
$content = ob_get_clean();
include __DIR__ .'/../../../App/layout.php';
?>
