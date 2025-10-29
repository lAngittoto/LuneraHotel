  <?php
    ob_start();
    ?>

  <div class="bg-[#8b2d2d] not-visited:w-screen px-10 py-5 flex flex-row justify-between text-[#ffffff] font-mono items-center select">
      <h1 class="text-3xl">Lunera Hotel</h1>
      <div class=" flex flex-row gap-10 text-[1.3rem] items-center">
          <a href="/LuneraHotel/App/Public/admin">Dashboard</a>
          <a href="/LuneraHotel/App/Public/allrooms">Rooms</a>
          <a href="/LuneraHotel/App/Auth/Controllers/logout.php">Log out</a>
      </div>

  </div>
  <?php
    $content = ob_get_clean();
    include __DIR__ . '/../../../App/layout.php';
    ?>