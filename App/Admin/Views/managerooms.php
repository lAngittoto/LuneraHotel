<?php 
ob_start(); 
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../../config/Helpers/colorcoding.php';
?>

<div class="p-10">
  <h1 class="text-4xl text-[#333333] font-bold">Manage Rooms</h1>

  <div class="flex flex-row justify-between mt-5 items-center">
    <p class="text-[1.2rem] text-[#333333]">View, Create, Edit, and manage all hotel rooms.</p>
    <a href="">
      <span class="text-[1.2rem] text-[#ffffff] bg-[#800000] py-3 px-4 rounded-2xl shadow-2xl">
        <i class="fa-solid fa-circle-plus"></i> Create New Rooms
      </span>
    </a>
  </div>

  <div>
    <!-- Table Header -->
    <div class="bg-[#ffffff] border border-[#999999] shadow-2xl text-[1.2rem] grid grid-cols-6 gap-4 p-5 mt-10 font-semibold">
      <h1>Room Number</h1>
      <h1>Room Type</h1>
      <h1>Floor</h1>
      <h1>Capacity</h1>
      <h1 class="text-center">Status</h1>
      <h1 class="text-right pr-5">Actions</h1>
    </div>

    <!-- Table Rows -->
    <div>
      <?php foreach ($rooms as $room): ?>
        <?php $statusClass = getStatusClass($room['status']); ?>

        <div class="bg-[#ffffff] border border-[#999999] shadow-2xl text-[1.2rem] grid grid-cols-6 gap-4 p-5 mt-5 items-center">
          <p><?= htmlspecialchars($room['room_number']) ?></p>
          <p><?= htmlspecialchars($room['room_type']) ?></p>
          <p><?= htmlspecialchars($room['floor']) ?></p>
          <p><?= htmlspecialchars($room['people']) ?></p>
          
          <span class="<?= htmlspecialchars($statusClass) ?> px-3 py-1 rounded-4xl text-white text-center">
            <?= htmlspecialchars($room['status']) ?>
          </span>

          <div class="flex flex-row justify-end space-x-5 pr-5">
            <i class="fa-regular fa-pen-to-square cursor-pointer hover:text-blue-600"></i>
            <i class="fa-solid fa-trash cursor-pointer hover:text-red-600"></i>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>
