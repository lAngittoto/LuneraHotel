    document.addEventListener('DOMContentLoaded', () => {
        const editor = document.getElementById('assign-editor');
        const overlay = document.getElementById('assign-overlay');
        let currentButton = null;

        function openEditor(btn) {
            currentButton = btn;
            editor.style.display = 'block';
            overlay.style.display = 'block';

            fetch(`./includes/get_staff.php?floor=${btn.dataset.floor}`)
                .then(res => res.json())
                .then(data => {
                    const onFloor = editor.querySelector('.staff-on-floor ul');
                    const other = editor.querySelector('.staff-other ul');
                    onFloor.innerHTML = '';
                    other.innerHTML = '';
                    const radioName = 'staff_' + btn.dataset.room;
                    
                    const getAvailabilityBadge = (availability) => {
                        const styles = {
                            'Available': 'background:#d1fae5;color:#065f46',
                            'On Break': 'background:#fef3c7;color:#92400e',
                            'Absent': 'background:#fee2e2;color:#991b1b',
                            'On Leave': 'background:#dbeafe;color:#1e40af',
                            'Unavailable': 'background:#e5e7eb;color:#374151'
                        };
                        const style = styles[availability] || styles['Available'];
                        return `<span style="font-size:0.7rem;padding:2px 6px;border-radius:4px;font-weight:600;margin-left:8px;${style}">${availability || 'Available'}</span>`;
                    };
                    
                    data.onFloor.forEach(s =>
                        onFloor.innerHTML += `<li style="margin-bottom:6px;"><label style="display:flex;align-items:center;"><input type="radio" name="${radioName}" value="${s.StaffID}" style="margin-right:8px;"> ${s.StaffMember}${getAvailabilityBadge(s.Availability)}</label></li>`
                    );
                    data.other.forEach(s =>
                        other.innerHTML += `<li style="margin-bottom:6px;"><label style="display:flex;align-items:center;"><input type="radio" name="${radioName}" value="${s.StaffID}" style="margin-right:8px;"> ${s.StaffMember}${getAvailabilityBadge(s.Availability)}</label></li>`
                    );
                });
        }

        function closeEditor() {
            editor.style.display = 'none';
            overlay.style.display = 'none';
        }

        document.addEventListener('click', e => {
            const btn = e.target.closest('.assign-btn');
            if (btn) {
                e.stopPropagation();
                openEditor(btn);
            } else if (!e.target.closest('#assign-editor')) {
                closeEditor();
            }
        });

        overlay.addEventListener('click', closeEditor);

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && editor.style.display === 'block') {
                closeEditor();
            }
        });

        // Assign button functionality
        editor.querySelector('#assign-confirm').addEventListener('click', function() {
            if (!currentButton) return;
            const taskId = currentButton.dataset.task;
            const roomNumber = currentButton.dataset.room;
            const radioName = 'staff_' + roomNumber;
            const selectedRadio = editor.querySelector(`input[name='${radioName}']:checked`);
            if (!selectedRadio) {
                alert('Please select a staff member to assign.');
                return;
            }
            // Get housekeeperId from radio button value
            const housekeeperId = selectedRadio.value;
            fetch('includes/assign-room-staff.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `taskId=${encodeURIComponent(taskId)}&housekeeperId=${encodeURIComponent(housekeeperId)}`
            })
            .then(res => res.json())
            .then(data => {
                console.log('Assignment response:', data); // Debug: see notification status
                if (data.success) {
                    closeEditor();
                    // Refresh assignments table in real-time instead of reloading page
                    if (typeof loadTable === 'function') {
                        loadTable();
                    }
                } else {
                    alert('Failed to assign staff: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('An error occurred while assigning staff.');
            });
        });
    });
