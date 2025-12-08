function loadMaintenanceReports() {
    fetch('./includes/fetch-maintenance-reports.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update statistics
                document.getElementById('stat-total').textContent = data.stats.total;
                document.getElementById('stat-open').textContent = data.stats.open;
                document.getElementById('stat-resolved').textContent = data.stats.resolved;

                const container = document.getElementById('maintenance-report-content');
                
                if (data.requests.length > 0) {
                    let html = '<table style="width:100%; border-collapse: collapse;">';
                    html += '<thead><tr style="border-bottom: 2px solid #e0e0e0;">';
                    html += '<th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #666;">Request ID</th>';
                    html += '<th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #666;">Room</th>';
                    html += '<th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #666;">Description</th>';
                    html += '<th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #666;">Reported Date</th>';
                    html += '<th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #666;">Status</th>';
                    html += '<th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #666;">Action</th>';
                    html += '</tr></thead><tbody>';
                    
                    data.requests.forEach(req => {
                        // Status badge colors
                        let statusColor = '';
                        if (req.Status === 'Open') {
                            statusColor = 'background: #fef3c7; color: #92400e; border: 1px solid #fde047;';
                        } else if (req.Status === 'Resolved') {
                            statusColor = 'background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7;';
                        }

                        html += '<tr style="border-bottom: 1px solid #f0f0f0;">';
                        html += `<td style="padding: 12px 8px;">#${req.RequestID}</td>`;
                        html += `<td style="padding: 12px 8px;">Room ${req.RoomNumber} (Floor ${req.Floor})</td>`;
                        html += `<td style="padding: 12px 8px;">${req.Description}</td>`;
                        html += `<td style="padding: 12px 8px;">${req.ReportedDate}</td>`;
                        html += `<td style="padding: 12px 8px;"><span style="padding: 4px 12px; border-radius: 12px; font-size: 0.875rem; font-weight: 500; ${statusColor}">${req.Status}</span></td>`;
                        
                        // Action button - only show if not resolved
                        if (req.Status !== 'Resolved') {
                            html += `<td style="padding: 12px 8px;">
                                        <button class="resolve-btn" data-request-id="${req.RequestID}" style="background: #16a34a; color: white; border: none; padding: 6px 16px; border-radius: 6px; cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                                            Mark Resolved
                                        </button>
                                     </td>`;
                        } else {
                            html += `<td style="padding: 12px 8px; color: #999;">-</td>`;
                        }
                        
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table>';
                    container.innerHTML = html;
                    
                    // Attach event listeners to resolve buttons
                    document.querySelectorAll('.resolve-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const requestId = this.getAttribute('data-request-id');
                            if (confirm('Mark this maintenance request as resolved?')) {
                                resolveMaintenanceRequest(requestId);
                            }
                        });
                    });
                } else {
                    container.innerHTML = '<p style="color: #666; padding: 20px; text-align: center;">No maintenance requests found.</p>';
                }
            } else {
                document.getElementById('maintenance-report-content').innerHTML = '<p style="color: red; text-align: center;">Error loading maintenance reports.</p>';
            }
        })
        .catch(err => {
            console.error('Error loading maintenance reports:', err);
            document.getElementById('maintenance-report-content').innerHTML = '<p style="color: red; text-align: center;">Error loading maintenance reports.</p>';
        });
}

// Make function globally available
window.loadMaintenanceReports = loadMaintenanceReports;

// Function to resolve maintenance request
function resolveMaintenanceRequest(requestId) {
    fetch('includes/resolve-maintenance-request.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `requestId=${requestId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Maintenance request marked as resolved!');
            loadMaintenanceReports(); // Reload the table
        } else {
            alert('Failed to resolve request: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(err => {
        console.error('Error resolving request:', err);
        alert('Error resolving maintenance request.');
    });
}