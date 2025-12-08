const statusFilter = document.getElementById("statusFilter");
const roomSearch = document.getElementById("roomSearch");
const tableContainer = document.getElementById("tableContainer");

function loadOverviewTable() {
    const status = statusFilter.value;
    const search = roomSearch.value;
    fetch(`./includes/fetch-Overviewtable.php?status=${status}&search=${search}`)
        .then(res => res.text())
        .then(html => {
            tableContainer.innerHTML = html;
        });
}

loadOverviewTable();

statusFilter.addEventListener("input", loadOverviewTable);

// Edit room modal handlers
document.getElementById('cancel-edit-room').addEventListener('click', function() {
    document.getElementById('edit-room-modal').style.display = 'none';
});

document.getElementById('edit-room-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const roomId = document.getElementById('edit-room-id').value;
    const roomNumber = document.getElementById('edit-room-number').value;
    const floor = document.getElementById('edit-floor').value;
    const roomType = document.getElementById('edit-room-type').value;
    const status = document.getElementById('edit-status').value;
    
    fetch('includes/edit-room.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `roomId=${encodeURIComponent(roomId)}&roomNumber=${encodeURIComponent(roomNumber)}&floor=${encodeURIComponent(floor)}&roomType=${encodeURIComponent(roomType)}&status=${encodeURIComponent(status)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Room updated successfully');
            document.getElementById('edit-room-modal').style.display = 'none';
            loadOverviewTable();
            // Refresh room list if it exists and is visible
            if (typeof loadRoomList === 'function' && document.getElementById('admin-room-list') && document.getElementById('admin-room-list').style.display !== 'none') {
                loadRoomList();
            }
        } else {
            alert('Failed to update room: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(err => alert('Error updating room: ' + err.message));
});

setInterval(function() {
    loadOverviewTable();
    // Also refresh room list if it exists and is visible
    if (typeof loadRoomList === 'function' && document.getElementById('admin-room-list') && document.getElementById('admin-room-list').style.display !== 'none') {
        loadRoomList();
    }
}, 500);