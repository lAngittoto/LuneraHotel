// Load staff list for filter dropdown
function loadHistoryStaffList() {
    fetch('includes/fetch-staff-list.php')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.staff) {
                const select = document.getElementById('history-staff-filter');
                data.staff.forEach(staff => {
                    const option = document.createElement('option');
                    option.value = staff.FullName;
                    option.textContent = staff.FullName;
                    select.appendChild(option);
                });
            }
        })
        .catch(err => console.error('Error loading staff list:', err));
}

// Load assignment history
function loadAssignmentHistory() {
    const staffFilter = document.getElementById('history-staff-filter').value;
    const roomFilter = document.getElementById('history-room-filter').value;
    const dateFrom = document.getElementById('history-date-from').value;
    const dateTo = document.getElementById('history-date-to').value;
    
    const params = new URLSearchParams();
    if (staffFilter) params.append('staff', staffFilter);
    if (roomFilter) params.append('room', roomFilter);
    if (dateFrom) params.append('dateFrom', dateFrom);
    if (dateTo) params.append('dateTo', dateTo);
    
    fetch(`includes/fetch-assignment-history.php?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('history-table-body');
            tbody.innerHTML = '';
            
            if (!data.success || data.history.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="padding:20px; text-align:center; color:#888;">No completed assignments found.</td></tr>';
                return;
            }
            
            data.history.forEach(record => {
                const row = document.createElement('tr');
                row.style.borderBottom = '1px solid #eee';
                
                const formatDate = (dateString) => {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                };
                
                const statusColor = {
                    'Completed': '#27ae60',
                    'Missed': '#e74c3c',
                    'Reassigned': '#f39c12'
                };
                
                row.innerHTML = `
                    <td style="padding:12px; color:#555;">${formatDate(record.AssignedDate)}</td>
                    <td style="padding:12px; color:#555; font-weight:500;">${record.RoomNumber || '-'}</td>
                    <td style="padding:12px; color:#555;">${record.StaffName || '-'}</td>
                    <td style="padding:12px; color:#555;">${record.TaskDescription || '-'}</td>
                    <td style="padding:12px;">
                        <span style="background:${statusColor[record.Status] || '#888'}; color:white; padding:4px 10px; border-radius:4px; font-size:0.85rem; font-weight:500;">
                            ${record.Status}
                        </span>
                    </td>
                    <td style="padding:12px; color:#555;">${record.TimeCompleted || '-'}</td>
                    <td style="padding:12px; color:#666; font-size:0.9rem;">-</td>
                `;
                
                tbody.appendChild(row);
            });
        })
        .catch(err => {
            console.error('Error loading assignment history:', err);
            const tbody = document.getElementById('history-table-body');
            tbody.innerHTML = '<tr><td colspan="7" style="padding:20px; text-align:center; color:#e74c3c;">Error loading history data.</td></tr>';
        });
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Load staff list for filter
    loadHistoryStaffList();
    
    // Filter button click handler
    const filterBtn = document.getElementById('history-filter-btn');
    if (filterBtn) {
        filterBtn.addEventListener('click', loadAssignmentHistory);
    }
});
