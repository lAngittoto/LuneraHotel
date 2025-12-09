function loadStaffList() {
    fetch('./includes/fetch-staff-list.php')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('staff-list-container');
            
            if (data.success && data.staff.length > 0) {
                let html = '<table style="width:100%; border-collapse: collapse;">';
                html += '<thead><tr style="border-bottom: 2px solid #e0e0e0; text-align: left;">';
                html += '<th style="padding: 12px 8px; font-weight: 600; color: #333;">Name</th>';
                html += '<th style="padding: 12px 8px; font-weight: 600; color: #333;">UUID</th>';
                html += '<th style="padding: 12px 8px; font-weight: 600; color: #333;">Email</th>';
                html += '<th style="padding: 12px 8px; font-weight: 600; color: #333;">Phone</th>';
                html += '<th style="padding: 12px 8px; font-weight: 600; color: #333;">Hire Date</th>';
                html += '<th style="padding: 12px 8px; font-weight: 600; color: #333;">Availability</th>';
                html += '<th style="padding: 12px 8px; font-weight: 600; color: #333;">Account</th>';
                html += '<th style="padding: 12px 8px; font-weight: 600; color: #333;">Actions</th>';
                html += '</tr></thead><tbody>';
                
                data.staff.forEach(staff => {
                    const hasAccount = staff.haveAccount == 1;
                    const accountBadge = hasAccount 
                        ? '<span style="background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">✓ Yes</span>'
                        : '<span style="background: #fee; color: #c33; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">✗ No</span>';
                    
                    // Availability badge with color coding
                    let availabilityBadge = '';
                    const availability = staff.Availability || 'Available';
                    switch(availability) {
                        case 'Available':
                            availabilityBadge = '<span style="background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">Available</span>';
                            break;
                        case 'On Break':
                            availabilityBadge = '<span style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">On Break</span>';
                            break;
                        case 'Absent':
                            availabilityBadge = '<span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">Absent</span>';
                            break;
                        case 'On Leave':
                            availabilityBadge = '<span style="background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">On Leave</span>';
                            break;
                        case 'Unavailable':
                            availabilityBadge = '<span style="background: #e5e7eb; color: #374151; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">Unavailable</span>';
                            break;
                    }
                    
                    html += '<tr style="border-bottom: 1px solid #f0f0f0;">';
                    html += `<td style="padding: 12px 8px;">${staff.FullName}</td>`;
                    html += `<td style="padding: 12px 8px;">${staff.UUID || '-'}</td>`;
                    html += `<td style="padding: 12px 8px;">${staff.Email || '-'}</td>`;
                    html += `<td style="padding: 12px 8px;">${staff.Phone || '-'}</td>`;
                    html += `<td style="padding: 12px 8px;">${staff.HireDate || '-'}</td>`;
                    html += `<td style="padding: 12px 8px;">${availabilityBadge}</td>`;
                    html += `<td style="padding: 12px 8px;">${accountBadge}</td>`;
                    html += `<td style="padding: 12px 8px;">
                        <button onclick="editStaff(${staff.HousekeeperID})" style="background: maroon; color: white; border: none; border-radius: 4px; padding: 4px 10px; font-size: 0.85rem; cursor: pointer; margin-right: 6px;">Edit</button>
                        <button onclick="deleteStaff(${staff.HousekeeperID}, '${staff.FullName}')" style="background: white; color: maroon; border: 1px solid maroon; border-radius: 4px; padding: 4px 10px; font-size: 0.85rem; cursor: pointer;">Delete</button>
                    </td>`;
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p style="color: #666; padding: 20px; text-align: center;">No staff members found.</p>';
            }
        })
        .catch(err => {
            console.error('Error loading staff list:', err);
            document.getElementById('staff-list-container').innerHTML = '<p style="color: red;">Error loading staff list.</p>';
        });
}

function editStaff(housekeeperId) {
    fetch(`./includes/get-staff-details.php?id=${housekeeperId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const staff = data.staff;
                
                // Fill the add staff form with existing data
                document.querySelector('#add-staff-form input[name="fullName"]').value = staff.FullName;
                document.querySelector('#add-staff-form input[name="phone"]').value = staff.Phone || '';
                document.querySelector('#add-staff-form input[name="email"]').value = staff.Email || '';
                document.querySelector('#add-staff-form input[name="uuid"]').value = staff.UUID || '';
                document.querySelector('#add-staff-form input[name="hireDate"]').value = staff.HireDate || '';
                document.querySelector('#add-staff-form select[name="assignedFloor"]').value = staff.AssignedFloor || '';
                
                // Store the ID for update
                document.getElementById('add-staff-form').dataset.editId = housekeeperId;
                
                // Change modal title
                document.querySelector('#add-staff-modal h2').textContent = 'Edit Staff Member';
                document.querySelector('#add-staff-form button[type="submit"]').textContent = 'Update Staff';
                
                // Show modal
                document.getElementById('add-staff-overlay').style.display = 'flex';
            }
        })
        .catch(err => console.error('Error loading staff details:', err));
}

function deleteStaff(housekeeperId, name) {
    if (confirm(`Are you sure you want to delete ${name}? This action cannot be undone.`)) {
        fetch('./includes/delete-staff.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `housekeeperId=${housekeeperId}`
        })
        .then(res => {
            return res.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Server returned invalid JSON: ' + text.substring(0, 100));
                }
            });
        })
        .then(data => {
            if (data.success) {
                alert('Staff member deleted successfully');
                loadStaffList();
            } else {
                alert('Error deleting staff: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Error deleting staff:', err);
            alert('Error deleting staff: ' + err.message);
        });
    }
}

// Add click handler for the staff list add button
document.addEventListener('DOMContentLoaded', function() {
    const addStaffListBtn = document.getElementById('add-staff-list-btn');
    if (addStaffListBtn) {
        addStaffListBtn.addEventListener('click', function() {
            // Reset form and show modal
            document.getElementById('add-staff-form').reset();
            delete document.getElementById('add-staff-form').dataset.editId;
            document.querySelector('#add-staff-modal h2').textContent = 'Add New Staff Member';
            document.querySelector('#add-staff-form button[type="submit"]').textContent = 'Add Staff Member';
            document.getElementById('add-staff-overlay').style.display = 'flex';
        });
    }
});

window.loadStaffList = loadStaffList;
window.editStaff = editStaff;
window.deleteStaff = deleteStaff;
