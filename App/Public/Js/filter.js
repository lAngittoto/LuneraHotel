const checkAvailable = document.querySelector('#checkAvailable');
const roomTypeSelect = document.querySelector('#roomTypeSelect');
const floorSelect = document.querySelector('#floorSelect');
const resetBtn = document.querySelector('#resetBtn');
const roomsContainer = document.querySelector('#rooms-container');

function getStatusClass(status) {
  switch (status.toLowerCase()) {
    case 'available':
      return 'bg-green-500 text-white';
    case 'booked':
      return 'bg-blue-500 text-white';
    case 'under maintenance':
      return 'bg-yellow-500 text-black';
    case 'occupied':
      return 'bg-red-500 text-white';
    default:
      return 'bg-gray-500 text-white';
  }
}

// 🧱 Render rooms (same structure as PHP Rooms::displayRoom)
function renderRooms(rooms) {
  roomsContainer.className = "p-10 w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-10 items-stretch";
  roomsContainer.innerHTML = "";

  rooms.forEach(room => {
    const div = document.createElement('div');
    div.className = "bg-[#ffffff] rounded-t-2xl border border-[#dcdcdc] flex flex-col sm:gap-3 select-none h-full shadow-md hover:shadow-lg transition";

    div.innerHTML = `
      <img src="${room.img}" alt="Room Image" class="rounded-t-2xl w-full sm:h-[400px] object-cover">
      
      <div class="flex flex-row justify-between items-center p-3">
        <h1 class="lg:text-2xl sm:text-[1.2rem] md:text-[1.6rem]">${room.room_type}</h1>
        <h1 class="lg:text-[1rem] lg:px-5 lg:py-2 md:text-[0.8rem] md:px-4 md:py-1 text-[0.7rem] px-3 py-1 rounded-4xl ${getStatusClass(room.status)}">${room.status}</h1>
      </div>

      <p class="p-5 sm:text-[1.1rem] md:text-[1.2rem] lg:text-[1.3rem] text-[#333333]">${room.description}</p>

      <div class="flex flex-row justify-between p-3 mt-auto">
        <h1 class="lg:text-2xl sm:text-[1.2rem] md:text-[1.6rem] text-[#333333]">Room: ${room.room_number}</h1>
        <h1 class="lg:text-2xl sm:text-[1.2rem] md:text-[1.6rem] text-[#333333]"><i class="fa-regular fa-user"></i> ${room.people} People</h1>
      </div>

      <div class="flex justify-center w-full">
        <a href="index.php?page=viewdetails&room=${room.id}" class="w-full text-center px-5 py-3 bg-[#800000] text-white hover:bg-red-900 transition">
          View Details <i class="fa-regular fa-file-lines"></i>
        </a>
      </div>
    `;

    roomsContainer.appendChild(div);
  });
}

// 🧩 Fetch rooms mula sa PHP (same logic)
function fetchRooms() {
  if (roomTypeSelect.value === "" && floorSelect.value === "" && !checkAvailable.checked) {
    roomsContainer.innerHTML = "";
    return;
  }

  const params = new URLSearchParams();
  if (checkAvailable.checked) params.append("status", "Available");
  if (roomTypeSelect.value !== "") params.append("type", roomTypeSelect.value);
  if (floorSelect.value !== "") params.append("floor", floorSelect.value);

  fetch('/LuneraHotel/App/End-User/Controllers/filterrooms.php?' + params.toString())
    .then(res => res.json())
    .then(data => {
      roomsContainer.innerHTML = "";
      if (data.length === 0) return;
      renderRooms(data);
    })
    .catch(err => console.error(err));
}

// 🧠 Event listeners
checkAvailable.addEventListener('change', fetchRooms);
roomTypeSelect.addEventListener('change', fetchRooms);
floorSelect.addEventListener('change', fetchRooms);

resetBtn.addEventListener('click', () => {
  roomTypeSelect.value = "";
  floorSelect.value = "";
  checkAvailable.checked = false;
  setTimeout(fetchRooms, 100);
});

fetchRooms();
