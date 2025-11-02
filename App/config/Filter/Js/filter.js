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

// 🧱 Render rooms
function renderRooms(rooms) {
  roomsContainer.className = "p-10 w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-10 items-stretch";
  roomsContainer.innerHTML = "";

  rooms.forEach(room => {
    const div = document.createElement('div');
    div.className = "bg-[#ffffff] rounded-t-2xl border border-[#dcdcdc] flex flex-col sm:gap-3 select-none h-full shadow-md hover:shadow-lg transition duration-300";

    div.innerHTML = `
      <img src="${room.img}" alt="Room Image" class="rounded-t-2xl w-full sm:h-[400px] object-cover">
      
      <div class="flex flex-row justify-between items-center p-3">
        <h1 class="lg:text-2xl sm:text-[1.2rem] md:text-[1.6rem] text-[#333333]">${room.room_type}</h1>
        <h1 class="lg:text-[1rem] lg:px-5 lg:py-2 md:text-[0.8rem] md:px-4 md:py-1 text-[0.7rem] px-3 py-1 rounded-4xl ${getStatusClass(room.status)}">${room.status}</h1>
      </div>

      <p class="p-5 sm:text-[1.1rem] md:text-[1.2rem] lg:text-[1.3rem] text-[#333333]">${room.description}</p>

      <div class="flex flex-row justify-between p-3 mt-auto">
        <h1 class="lg:text-2xl sm:text-[1.2rem] md:text-[1.6rem] text-[#333333]"><i class='fa-solid fa-door-closed text-[#800000]'></i>
          Room: ${room.room_number}
        </h1>
        <h1 class="lg:text-2xl sm:text-[1.2rem] md:text-[1.6rem] text-[#333333]">
          <i class="fa-regular fa-user text-[#800000]"></i> ${room.people} Up to Person
        </h1>
      </div>

      <div class="flex justify-center w-full">
        <a href="index.php?page=viewdetails&room=${room.id}" class="w-full text-center px-5 py-3 bg-[#800000] text-white hover:bg-red-900 transition duration-300">
          View Details <i class="fa-regular fa-file-lines"></i>
        </a>
      </div>
    `;
    roomsContainer.appendChild(div);
  });
}

// 🧩 Fetch rooms mula sa PHP
function fetchRooms() {
  if (roomTypeSelect.value === "" && floorSelect.value === "" && !checkAvailable.checked) {
    roomsContainer.innerHTML = "";
    return;
  }

  const params = new URLSearchParams();
  if (checkAvailable.checked) params.append("status", "Available");
  if (roomTypeSelect.value !== "") params.append("type", roomTypeSelect.value);
  if (floorSelect.value !== "") params.append("floor", floorSelect.value);

  fetch('../Config/Filter/filterrooms.php?' + params.toString())
    .then(res => res.json())
    .then(data => {
      // ✅ Handle empty result
      if (!Array.isArray(data) || data.length === 0) {
        roomsContainer.className = "flex flex-col justify-center items-center text-center w-full ";
        roomsContainer.innerHTML = `
          <i class="fa-solid fa-bed text-6xl mb-5 text-[#800000]"></i>
          <p class="text-[#333333] text-2xl font-semibold">
            Currently, no rooms are available based on your filters.
          </p>
        `;
        return;
      }

      renderRooms(data);
    })
    .catch(err => {
      console.error(err);
      roomsContainer.className = "flex flex-col justify-center items-center text-center w-full ";
      roomsContainer.innerHTML = `
        <i class="fa-solid fa-triangle-exclamation text-6xl mb-5 text-[#800000]"></i>
        <p class="text-[#333333] text-xl font-semibold">
          An error occurred while loading rooms. Please try again later.
        </p>
      `;
    });
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

// 🕹 Initial fetch
fetchRooms();
