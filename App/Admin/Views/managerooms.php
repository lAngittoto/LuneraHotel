<?php 
ob_start(); 
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../../config/Helpers/colorcoding.php';
?>

<div class="p-10">
  <h1 class="md:text-4xl text-3xl text-[#333333] font-bold">Manage Rooms</h1>

  <div class="flex flex-row justify-between mt-5 items-center">
    <p class="md:text-[1.2rem] text-[#333333]">View, Create, Edit, and manage all hotel rooms.</p>
    <a href="">
      <a href="/LuneraHotel/App/Public/createrooms" class="text-[#ffffff] bg-[#800000] py-3 px-4 rounded-2xl shadow-2xl">
        <i class="fa-solid fa-circle-plus"></i> Create New Rooms
</a>
    </a>
  </div>

  <div>
    <!-- Table Header -->
    <div class="bg-[#ffffff] border border-[#cccccc] shadow-2xl md:text-[1.2rem] text-[1rem] grid lg:grid-cols-7 grid-cols-2 gap-4 p-8 mt-10 font-semibold ">
      <h1>Room Number</h1>
      <h1>Room Name</h1>
      <h1>Room Type</h1>
      <h1>Floor</h1>
      <h1>Capacity</h1>
      <h1 class="lg:text-center">Status</h1>
      <h1 class="lg:text-right pr-5">Actions</h1>
    </div>

    <!-- Table Rows -->
    <div>
      <?php foreach ($rooms as $room): ?>
        <?php $statusClass = getStatusClass($room['status']); ?>

        <div class="bg-[#ffffff] border border-[#ebebeb] shadow-2xl md:text-[1.2rem] text-[1rem] grid lg:grid-cols-7 grid-cols-2 md:grid-cols-3 gap-5 p-5 mt-5 items-center">
          <p class=" text-[#333333] font-semibold"><?= htmlspecialchars($room['room_number']) ?></p>
          <p><?= htmlspecialchars($room['room_type']) ?></p>
          <p class=" text-[#333333] font-semibold"><?= htmlspecialchars($room['type_name']) ?></p>
          <p><?= htmlspecialchars($room['floor']) ?></p>
          <p class=" text-[#333333] font-semibold"><?= htmlspecialchars($room['people']) ?></p>
          
          <span class="<?= htmlspecialchars($statusClass) ?> px-5 py-3 rounded-4xl text-[0.9rem] text-white text-center">
            <?= htmlspecialchars($room['status']) ?>
          </span>
<div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-5 pr-5 space-y-3 sm:space-y-0">

    <!-- Update -->
    <a href="updaterooms?id=<?= $room['id'] ?>"
       class="cursor-pointer text-blue-600 hover:text-blue-800 
              transition duration-200 hover:scale-110
              text-lg sm:text-base font-medium">
        Update
    </a>

    <!-- Deactivate -->
    <a href="#"
       class="cursor-pointer text-red-600 hover:text-red-800
              transition duration-200 hover:scale-110
              text-lg sm:text-base font-medium">
        Deactivate
    </a>

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
