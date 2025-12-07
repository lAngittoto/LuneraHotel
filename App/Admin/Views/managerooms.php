<?php
ob_start();
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../../config/Helpers/colorcoding.php';

if (isset($_SESSION['success_message'])) {
    echo "<div class='text-green-800 p-3 rounded mb-4 text-center text-4xl'>"
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
                        grid grid-cols-3 md:grid-cols-7 gap-4 p-8 mt-10 font-semibold">
            <h1>Room Number</h1>
            <h1 class="hidden md:block">Room Name</h1>
            <h1 class="hidden md:block">Room Type</h1>
            <h1 class="hidden md:block">Floor</h1>
            <h1 class="hidden md:block">Capacity</h1>
            <h1 class="text-center">Status</h1>
            <h1 class="text-center">Actions</h1>
        </div>

        <!-- TABLE ROWS -->
        <div>
            <?php foreach ($rooms as $room): ?>
                <?php 
                    $statusClass = getStatusClass($room['status']); 
                    $statusLower = strtolower($room['status']);
                    $isInProgress = $statusLower === 'in progress';
                    $isPending = $statusLower === 'pending' || $statusLower === 'dirty';

                    // Minimal fix lang: display "Out of Order" for Deactivated
                    $displayStatus = $room['status'] === 'Deactivated' ? 'Out of Order' : ($isPending ? 'Pending' : $room['status']);
                    $backgroundColor = $isInProgress ? '#4f46e5' : ($isPending ? '#f59e0b' : ($room['status'] === 'Deactivated' ? '#9ca3af' : ''));
                ?>
                <div class="room-row bg-[#ffffff] border border-[#ebebeb] shadow-2xl
                                    text-[1rem] md:text-[1.2rem]
                                    grid grid-cols-3 md:grid-cols-7
                                    gap-5 p-5 mt-5 items-center"
                     data-room-id="<?= $room['id'] ?>"
                     data-room-number="<?= htmlspecialchars($room['room_number'], ENT_QUOTES) ?>"
                     data-room-status="<?= strtolower($room['status']) ?>">

                    <!-- Room Number -->
                    <p class="text-[#333333] font-semibold">
                        <?= htmlspecialchars($room['room_number']) ?>
                    </p>

                    <!-- Room Name -->
                    <p class="hidden md:block"><?= htmlspecialchars($room['room_type']) ?></p>

                    <!-- Room Type -->
                    <p class="text-[#333333] font-semibold hidden md:block">
                        <?= htmlspecialchars($room['type_name']) ?>
                    </p>

                    <!-- Floor -->
                    <p class="hidden md:block"><?= htmlspecialchars($room['floor']) ?></p>

                    <!-- Capacity -->
                    <p class="text-[#333333] font-semibold hidden md:block">
                        <?= htmlspecialchars($room['people']) ?>
                    </p>

                    <!-- STATUS & CLEANING BUTTON -->
                    <div class="flex flex-col gap-2 items-center">
                        <span class="<?= htmlspecialchars($statusClass) ?> px-4 py-2 rounded-xl text-white text-center block w-full"
                              style="background: <?= $backgroundColor ?>; color: #fff;">
                            <?= htmlspecialchars($displayStatus) ?>
                        </span>

                        <?php if ($room['status'] !== 'Deactivated'): ?>
                            <?php if ($room['has_cleaning_task'] || $isInProgress): ?>
                                <button type="button"
                                    disabled
                                    style="background-color: #059669; color: white; padding: 8px 12px; border-radius: 8px; font-size: 14px; cursor: not-allowed; border: none; width: 100%;"
                                    title="Cleaning task already exists or in progress">
                                    <i class="fa-solid fa-check-circle"></i> Rooms to Be Cleaned
                                </button>
                            <?php elseif ($isPending): ?>
                                <button type="button" 
                                    onclick="openCleaningPopup(<?= $room['id'] ?>, '<?= htmlspecialchars($room['room_number'], ENT_QUOTES) ?>')"
                                    style="background-color: #9333ea; color: white; padding: 8px 12px; border-radius: 8px; font-size: 14px; cursor: pointer; border: none; width: 100%;"
                                    onmouseover="this.style.backgroundColor='#7e22ce'" 
                                    onmouseout="this.style.backgroundColor='#9333ea'">
                                    <i class="fa-solid fa-broom"></i> Submit Cleaning Task
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

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

<!-- Cleaning Task Popup Modal -->
<div id="cleaningPopup" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl w-[90%] max-w-md relative">
        <button onclick="closeCleaningPopup()"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="p-8">
            <h2 class="text-2xl font-bold text-[#333333] mb-2">Set Cleaning Task</h2>
            <p class="text-gray-600 mb-4">Room: <span id="popupRoomNumber" class="font-semibold"></span></p>

            <form id="cleaningTaskForm" onsubmit="submitCleaningTask(event)">
                <input type="hidden" id="taskRoomId" name="room_id">

                <div class="mb-6">
                    <label for="taskDescription" class="block text-lg font-semibold mb-2">Task Description</label>
                    <textarea id="taskDescription" name="description" rows="4"
                        class="w-full border border-gray-300 rounded-xl p-3 outline-none focus:border-purple-500"
                        placeholder="Enter cleaning task details..." required></textarea>
                </div>

                <div id="taskMessage" class="mb-4 hidden"></div>

                <div class="flex gap-3">
                    <button type="submit"
                        style="flex: 1; background-color: #9333ea; color: white; padding: 12px 24px; border-radius: 12px; font-weight: 600; border: none; cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#7e22ce'"
                        onmouseout="this.style.backgroundColor='#9333ea'">
                        <i class="fa-solid fa-plus"></i> Create Task
                    </button>
                    <button type="button" onclick="closeCleaningPopup()"
                        style="flex: 1; background-color: #d1d5db; color: #374151; padding: 12px 24px; border-radius: 12px; font-weight: 600; border: none; cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#9ca3af'"
                        onmouseout="this.style.backgroundColor='#d1d5db'">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCleaningPopup(roomId, roomNumber) {
    document.getElementById('cleaningPopup').classList.remove('hidden');
    document.getElementById('taskRoomId').value = roomId;
    document.getElementById('popupRoomNumber').textContent = roomNumber;
    document.getElementById('taskDescription').value = '';
    document.getElementById('taskMessage').classList.add('hidden');
}

function closeCleaningPopup() {
    document.getElementById('cleaningPopup').classList.add('hidden');
}

function submitCleaningTask(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const messageDiv = document.getElementById('taskMessage');

    fetch('/LuneraHotel/App/Admin/Controllers/createCleaningTaskController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        messageDiv.classList.remove('hidden');
        if (data.success) {
            messageDiv.className = 'mb-4 bg-green-100 text-green-800 p-3 rounded-lg';
            messageDiv.textContent = data.message;
            setTimeout(() => {
                closeCleaningPopup();
                location.reload();
            }, 1500);
        } else {
            messageDiv.className = 'mb-4 bg-red-100 text-red-800 p-3 rounded-lg';
            messageDiv.textContent = data.message || 'Failed to create task';
        }
    })
    .catch(error => {
        messageDiv.classList.remove('hidden');
        messageDiv.className = 'mb-4 bg-red-100 text-red-800 p-3 rounded-lg';
        messageDiv.textContent = 'Error: ' + error.message;
    });
}

// Auto popup for Pending rooms without task
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.room-row').forEach(row => {
        const status = row.dataset.roomStatus;
        const roomId = row.dataset.roomId;
        const roomNumber = row.dataset.roomNumber;
        const hasTask = row.querySelector('button[title*="Cleaning task"]') !== null;

        if ((status === 'dirty' || status === 'pending') && !hasTask) {
            openCleaningPopup(roomId, roomNumber);
        }
    });
});

document.getElementById('cleaningPopup')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCleaningPopup();
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>
