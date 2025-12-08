<?php ob_start(); ?>
<header class="bg-[#8b2d2d] w-screen p-7 sm:p-10 flex flex-col sm:flex-row justify-between items-center sticky top-0 z-50 shadow-md">
  <h1 class="flex items-center gap-4 text-white text-4xl sm:text-5xl md:text-6xl cursor-default select-none mb-5 sm:mb-0">
    <img src="images/logo.jpg" alt="Lunera Hotel Logo" class="w-14 sm:w-16 md:w-20 rounded-full border-2 border-white shadow-sm">
    Lunera Hotel
  </h1>

  <nav class="flex flex-col sm:flex-row gap-5 sm:gap-10 justify-center items-center text-white font-light select-none">
    <a href="/LuneraHotel/App/Public/allrooms" class="text-[1.3rem] hover:text-gray-200 transition duration-200">Rooms</a>
    <a href="/LuneraHotel/App/Public/admin" class="text-[1.3rem] hover:text-gray-200 transition duration-200">Dashboard</a>

    <div class="relative">
      <button id="notifIcon" class="relative p-2 rounded-full hover:bg-white/10 transition">
        <i class="fa-solid fa-bell text-2xl text-white"></i>
        <span id="notifCount" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full hidden"></span>
      </button>

      <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white text-black rounded-xl shadow-xl max-h-96 overflow-y-auto border border-gray-200">
        <h3 class="text-sm font-semibold text-gray-700 p-4 border-b border-gray-200">Notifications</h3>
        <div id="notifList" class="space-y-2 p-4"></div>
        <div id="notifEmpty" class="text-sm text-gray-500 text-center py-4 hidden">No notifications available.</div>
      </div>
    </div>

    <a href="/LuneraHotel/App/Auth/Controllers/logout.php">
      <p class="text-[1rem] px-5 py-3 bg-white text-[#800000] rounded-2xl shadow-lg hover:shadow-2xl transition duration-200">Log out</p>
    </a>
  </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const notifIcon = document.getElementById('notifIcon');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');
    const notifCount = document.getElementById('notifCount');
    const notifEmpty = document.getElementById('notifEmpty');

    notifIcon.addEventListener('click', () => {
        notifDropdown.classList.toggle('hidden');
    });

    async function fetchNotifications() {
        try {
            const res = await fetch('/LuneraHotel/App/Admin/Controllers/notifController.php');
            const data = await res.json();

            notifList.innerHTML = '';

            if (!data || data.length === 0) {
                notifEmpty.classList.remove('hidden');
                notifCount.classList.add('hidden');
                return;
            } else {
                notifEmpty.classList.add('hidden');
                notifCount.textContent = data.length;
                notifCount.classList.remove('hidden');
            }

            data.forEach(n => {
                const li = document.createElement('div');
                li.className = 'p-3 bg-gray-50 rounded-xl border border-gray-200 flex flex-col gap-1 shadow-sm hover:bg-gray-100 transition';
                li.innerHTML = `
                    <span class="text-green-700 text-xs font-semibold">${n.status.toUpperCase()}</span>
                    <p class="text-sm text-gray-800 font-medium">${n.description}</p>
                    <p class="text-xs text-gray-500">${n.completed_at}</p>
                `;
                notifList.appendChild(li);
            });
        } catch(e) {
            console.error("Fetch failed: ", e);
        }
    }

    fetchNotifications();
    setInterval(fetchNotifications, 5000);
});
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../../../App/layout.php'; ?>
