<?php ob_start(); ?>
<header class="bg-[#8b2d2d] w-screen p-7 sm:p-10 flex flex-col sm:flex-row justify-between items-center sticky top-0 z-50">
  <h1 class="flex items-center gap-4 text-white text-4xl sm:text-5xl md:text-6xl cursor-default select-none mb-5 sm:mb-0">
    <img src="images/logo.jpg" alt="Lunera Hotel Logo" class="w-14 sm:w-16 md:w-20 rounded-full border-2 border-white">
    Lunera Hotel
  </h1>

  <nav class="flex flex-col sm:flex-row gap-5 sm:gap-10 justify-center items-center text-white font-light select-none">
    <a href="/LuneraHotel/App/Public/allrooms"><span class="text-[1.3rem]">Rooms</span></a>
    <a href="/LuneraHotel/App/Public/admin"><span class="text-[1.3rem]">Dashboard</span></a>

    <!-- Notification Dropdown -->
    <div class="relative cursor-pointer" id="notifDropdownContainer">
      <i class="fa-solid fa-bell text-2xl" id="notifIcon"></i>
      <span id="notifCount" class="absolute top-0 right-0 bg-red-600 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">0</span>
      <div id="notifDropdown" class="absolute right-0 mt-2 w-80 bg-white text-black rounded-lg shadow-lg hidden max-h-96 overflow-y-auto">
        <ul id="notifList" class="p-2"></ul>
      </div>
    </div>

    <a href="/LuneraHotel/App/Auth/Controllers/logout.php">
      <p class="text-[1rem] px-5 py-3 bg-[#ffffff] text-[#800000] rounded-2xl shadow-2xl">Log out</p>
    </a>
  </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const notifIcon = document.getElementById('notifIcon');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');
    const notifCount = document.getElementById('notifCount');

    notifIcon.addEventListener('click', () => {
        notifDropdown.classList.toggle('hidden');
    });

    async function fetchNotifications() {
        try {
            const res = await fetch('/LuneraHotel/App/Admin/Controllers/notifController.php');
            const data = await res.json();

            notifList.innerHTML = '';
            notifCount.textContent = data.length;

            if (data.length === 0) {
                notifList.innerHTML = '<li class="p-2 text-gray-500">No notifications</li>';
            } else {
                data.forEach(n => {
                    const li = document.createElement('li');
                    li.className = 'border-b last:border-b-0 p-2 hover:bg-gray-100';
                    li.innerHTML = `<strong>Room ${n.room_number}</strong> (${n.room_name}): ${n.message}<br><small class="text-gray-400">${n.created_at}</small>`;
                    notifList.appendChild(li);
                });
            }
        } catch(e) { console.error(e); }
    }

    fetchNotifications();
    setInterval(fetchNotifications, 5000);
});
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../../../App/layout.php'; ?>
