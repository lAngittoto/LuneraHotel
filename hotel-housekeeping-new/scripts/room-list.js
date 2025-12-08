function loadRoomList() {
    fetch('./includes/fetch-room-list.php')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('room-list-container');
            
            if (data.success && data.rooms.length > 0) {
                let html = '<table style="width:100%; border-collapse: collapse;">';
                html += '<thead><tr style="border-bottom: 2px solid #e0e0e0; text-align: left;">';
                html += '<th style="padding: 12px 8px; font-weight: 600; color: #333;">Room Number</th>';
                html += '<th style="padding: 12px 8px; font-weight: 600; color: #333;">Room Type</th>';
                html += '<th style="padding: 12px 8px; font-weight: 600; color: #333;">Floor</th>';
                html += '</tr></thead><tbody>';
                
                data.rooms.forEach(room => {
                    html += '<tr style="border-bottom: 1px solid #f0f0f0;">';
                    html += `<td style="padding: 12px 8px; font-weight: 600; color: #6a2323;">${room.RoomNumber}</td>`;
                    html += `<td style="padding: 12px 8px;">${room.RoomType || 'Standard'}</td>`;
                    html += `<td style="padding: 12px 8px;">Floor ${room.Floor}</td>`;
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p style="color: #666; padding: 20px; text-align: center;">No rooms found.</p>';
            }
        })
        .catch(err => {
            console.error('Error loading room list:', err);
            document.getElementById('room-list-container').innerHTML = '<p style="color: red;">Error loading room list.</p>';
        });
}

window.loadRoomList = loadRoomList;
