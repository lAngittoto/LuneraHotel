<?php
ob_start();
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../../config/Helpers/colorcoding.php';

if (isset($_SESSION['success_message'])) {
  echo "<div class='bg-green-100 text-green-800 p-3 rounded mb-4 text-center text-4xl'>"
    . htmlspecialchars($_SESSION['success_message']) . "</div>";
  unset($_SESSION['success_message']);
}
?>

<div class="p-10">
  <h1 class="md:text-4xl text-3xl text-[#333333] font-bold">Manage Rooms</h1>

  <div class="flex flex-row justify-between mt-5 items-center">
    <p class="md:text-[1.2rem] text-[#333333]">View, Create, Edit, and manage all hotel rooms.</p>

    <a href="/LuneraHotel/App/Public/createrooms"
      class="text-[#ffffff] bg-[#800000] py-3 px-4 rounded-2xl shadow-2xl">
      <i class="fa-solid fa-circle-plus"></i> Create New Rooms
    </a>
  </div>

  <div>
    <!-- TABLE HEADER -->
    <div class="bg-[#ffffff] border border-[#cccccc] shadow-2xl
                    md:text-[1.2rem] text-[1rem]
                    grid grid-cols-2 lg:grid-cols-7 gap-4 p-8 mt-10 font-semibold">
      <h1>Room Number</h1>
      <h1>Room Name</h1>
      <h1>Room Type</h1>
      <h1>Floor</h1>
      <h1>Capacity</h1>
      <h1 class="text-center">Status</h1>
      <h1 class="text-center">Actions</h1>
    </div>

    <!-- TABLE ROWS -->
    <div>
      <?php foreach ($rooms as $room): ?>
        <?php $statusClass = getStatusClass($room['status']); ?>

        <div class="bg-[#ffffff] border border-[#ebebeb] shadow-2xl
                            text-[1rem] md:text-[1.2rem]
                            grid grid-cols-2 lg:grid-cols-7
                            gap-5 p-5 mt-5 items-center">

          <!-- Room Number -->
          <p class="text-[#333333] font-semibold">
            <?= htmlspecialchars($room['room_number']) ?>
          </p>

          <!-- Room Name -->
          <p><?= htmlspecialchars($room['room_type']) ?></p>

          <!-- Room Type -->
          <p class="text-[#333333] font-semibold">
            <?= htmlspecialchars($room['type_name']) ?>
          </p>

          <!-- Floor -->
          <p><?= htmlspecialchars($room['floor']) ?></p>

          <!-- Capacity -->
          <p class="text-[#333333] font-semibold">
            <?= htmlspecialchars($room['people']) ?>
          </p>

          <!-- STATUS -->
          <span class="<?= htmlspecialchars($statusClass) ?>
                                 px-4 py-2 rounded-xl text-white text-center block">
            <?= htmlspecialchars($room['status']) ?>
          </span>

          <!-- ACTION BUTTONS -->
          <div class="flex flex-col sm:flex-col lg:flex-row justify-center items-center gap-2">

            <!-- UPDATE -->
            <?php if ($room['status'] !== 'Deactivated'): ?>
              <a href="updaterooms?id=<?= $room['id'] ?>"
                class="text-center text-white bg-blue-600 hover:bg-blue-700
                                      transition duration-200 hover:scale-105
                                      px-3 py-2 text-xs sm:text-sm rounded-lg w-full lg:w-auto">
                Update
              </a>
            <?php else: ?>
              <span class="text-center text-white bg-gray-400 cursor-not-allowed
                                         px-3 py-2 text-xs sm:text-sm rounded-lg w-full lg:w-auto">
                Update
              </span>
            <?php endif; ?>

            <!-- DEACTIVATE -->
            <?php if ($room['status'] !== 'Deactivated'): ?>
              <form method="POST" action="updaterooms?id=<?= $room['id'] ?>" class="w-full lg:w-auto">
                <button type="submit" name="deactivate_room"
                  class="text-center text-white bg-red-600 hover:bg-red-700
                                               transition duration-200 hover:scale-105 cursor-pointer
                                               px-3 py-2 text-xs sm:text-sm rounded-lg w-full lg:w-auto">
                  Deactivate
                </button>
              </form>
            <?php endif; ?>

            <!-- REACTIVATE -->
            <?php if ($room['status'] === 'Deactivated'): ?>
              <form method="POST" action="updaterooms?id=<?= $room['id'] ?>" class="w-full lg:w-auto">
                <button type="submit" name="reactivate_room"
                  class="text-center text-white bg-green-600 hover:bg-green-700
                                               transition duration-200 hover:scale-105
                                               px-3 py-2 text-xs sm:text-sm rounded-lg w-full lg:w-auto">
                  Reactivate
                </button>
              </form>
            <?php endif; ?>

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