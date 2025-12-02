<section class="w-screen flex flex-col p-10 items-center gap-5 select-none">
  <h1 class="text-4xl sm:text-5xl">Find Your Perfect Space</h1>
  <p class="text-[1rem] sm:text-2xl text-[#333333]">
    Browse our available rooms and find the perfect one for your needs.
  </p>
</section>

<section class="w-screen justify-center items-center flex select-none">
  <div class="w-[80%] border border-[#b1b1b1] shadow-2xl bg-[#ffffff] rounded-4xl p-10">
    <form id="filterForm">
      <div class="flex sm:flex-row justify-around items-start flex-col">
        <!-- Checkbox -->
        <div class="flex flex-row gap-5 mt-13">
          <input type="checkbox" id="checkAvailable" class="w-8 h-8 accent-green-400" />
          <p class="sm:text-2xl text-[1.2rem]">Show Available Only</p>
        </div>

        <!-- Room Type -->
        <div class="flex flex-col gap-5">
          <label for="roomTypeSelect" class="sm:text-2xl text-[1.2rem]">Room Type</label>
          <select id="roomTypeSelect" class="border border-[#dcdcdc] p-2 md:w-[200px] w-[150px]">
            <option value="">Select None</option>
            <option value="Single">Single Room</option>
            <option value="Double">Double Room</option>
            <option value="Deluxe">Deluxe Room</option>
            <option value="Triple">Triple Room</option>
            <option value="Family">Family Room</option>
          </select>
        </div>

        <!-- Floor -->
        <div class="flex flex-col gap-5">
          <label for="floorSelect" class="sm:text-2xl text-[1.2rem]">Floor</label>
<select id="floorSelect" class="border border-[#dcdcdc] rounded p-2 md:w-[200px] w-[150px]">
    <option value="">Select None</option>
  <?php foreach ($floors as $floorValue): ?>
    <?php
    $activeRooms = getRoomsByFloor($pdo, $floorValue);
    if (empty($activeRooms)) continue; // hindi lalabas kung wala nang active rooms
    ?>
    <option value="<?= htmlspecialchars($floorValue) ?>">
        <?= htmlspecialchars(convertFloor($floorValue)) ?>
    </option>
<?php endforeach; ?>

</select>



          <button type="reset" id="resetBtn" class="md:w-[200px] w-[150px] border border-[#dcdcdc] rounded p-2 mt-10 cursor-pointer">
            Reset Filters
          </button>
        </div>
      </div>
    </form>
  </div>
</section>

<!-- 3-column grid -->
<section class="p-10 w-screen gap-10 items-stretch">
  <div id="rooms-container" class="grid grid-cols-3 gap-5"></div>
</section>
<script src="../Config/Filter/Js/filter.js"></script>