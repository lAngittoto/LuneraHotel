const container = document.getElementById('staffTableContainer');

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('floorSelect')) {
        const staffId = e.target.dataset.id;
        const newFloor = e.target.value;

        fetch('./includes/update_floor.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'staffID=' + encodeURIComponent(staffId) + '&floor=' + encodeURIComponent(newFloor)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update the local row
                e.target.dataset.savedFloor = newFloor;
                // Refresh staff table and staff overview in real-time
                if (typeof loadStaffTable === 'function') {
                    loadStaffTable();
                }
                if (typeof loadStaffOverview === 'function') {
                    loadStaffOverview();
                }
            } else {
                console.error('Floor update failed:', data.error);
                alert('Failed to update floor assignment');
            }
        })
        .catch(err => {
            console.error('Error updating floor:', err);
            alert('Error updating floor assignment');
        });
    }
    
    if (e.target.classList.contains('shiftSelect')) {
        const staffId = e.target.dataset.id;
        const shiftId = e.target.value;

        if (!shiftId) {
            // Remove shift assignment
            fetch('./includes/update_schedule.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'staffID=' + encodeURIComponent(staffId) + '&shiftID='
            }).then(resp => {
                if (resp.ok) {
                    console.log('Shift removed successfully');
                    // Refresh staff table and overview in real-time
                    if (typeof loadStaffTable === 'function') {
                        loadStaffTable();
                    }
                    if (typeof loadStaffOverview === 'function') {
                        loadStaffOverview();
                    }
                }
            }).catch(err => console.error('Error removing shift:', err));
        } else {
            // Assign shift
            fetch('./includes/update_schedule.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'staffID=' + encodeURIComponent(staffId) + '&shiftID=' + encodeURIComponent(shiftId)
            }).then(resp => {
                if (resp.ok) {
                    console.log('Shift assigned successfully');
                    // Refresh staff table and overview in real-time
                    if (typeof loadStaffTable === 'function') {
                        loadStaffTable();
                    }
                    if (typeof loadStaffOverview === 'function') {
                        loadStaffOverview();
                    }
                }
            }).catch(err => console.error('Error assigning shift:', err));
        }
    }
});

// Removed old schedule editor click handlers - now using dropdown selection