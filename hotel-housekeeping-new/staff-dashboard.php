<?php
session_start();
require('includes/database.php');
// Protect staff dashboard: must be logged in as staff
if (!isset($_SESSION['username']) || $_SESSION['accType'] !== 'staff') {
    header('Location: index.php');
    exit();
}
// Get staff info from session
$staffName = $_SESSION['staffName'] ?? 'Staff';
$housekeeperId = $_SESSION['housekeeperID'] ?? 0;

// If no housekeeperID in session, try to get it from database
if ($housekeeperId === 0) {
    $stmt = $conn->prepare("SELECT HousekeeperID FROM users WHERE Username = ?");
    $stmt->bind_param('s', $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $housekeeperId = $userData['HousekeeperID'] ?? 0;
    $stmt->close();
}

// DEBUG: Uncomment to see what's happening
// echo "HousekeeperID: " . $housekeeperId . "<br>";

// Fetch assigned tasks for this housekeeper from assignments table
$sql = "SELECT r.room_number as RoomNumber, r.status as Status, r.room_type as RoomType, r.floor as Floor, t.TaskID, t.Description as TaskDescription, a.AssignmentID, a.Status as AssignmentStatus
        FROM webdb.assignments a
        JOIN roomslunera_hotel.tasks t ON a.TaskID = t.TaskID
        JOIN roomslunera_hotel.rooms r ON t.RoomID = r.id 
        WHERE a.HousekeeperID = ?
        ORDER BY r.room_number";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $housekeeperId);
$stmt->execute();
$result = $stmt->get_result();
$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}
$stmt->close();

// DEBUG: Uncomment to see how many tasks found
// echo "Tasks found: " . count($rooms) . "<br>";
// print_r($rooms);

// If no housekeeper ID, show warning
if ($housekeeperId === 0) {
    $rooms = [];
    $errorMessage = "Your account is not linked to a housekeeper profile. Please contact administration.";
}

// Status filter options
$statusOptions = ['All', 'Dirty', 'In Progress', 'Clean'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="icon" type="image/svg+xml" href='data:image/svg+xml, <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="maroon"><path d="M484-80q-84 0-157.5-32t-128-86.5Q144-253 112-326.5T80-484q0-146 93-257.5T410-880q-18 99 11 193.5T521-521q71 71 165.5 100T880-410q-26 144-138 237T484-80Zm0-80q88 0 163-44t118-121q-86-8-163-43.5T464-465q-61-61-97-138t-43-163q-77 43-120.5 118.5T160-484q0 135 94.5 229.5T484-160Zm-20-305Z"/></svg>'>
    <style>
        body {
            background: #fcf9f6;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        .header {
            background: #6a2323;
            color: #fff;
            padding: 18px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #e0d6d6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .header .right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .tasks-section {
            padding: 32px;
        }

        .tasks-section h2 {
            margin-bottom: 0;
        }

        .tasks-section p {
            color: #6a2323;
            margin-top: 6px;
            margin-bottom: 18px;
        }

        .filter-btns {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }

        .filter-btns button {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 1rem;
            cursor: pointer;
        }

        .filter-btns button.active {
            background: #6a2323;
            color: #fff;
            border-color: #6a2323;
        }

        .room-cards {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }

        .room-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 24px;
            min-width: 260px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .room-card .room-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #6a2323;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 20px;
        }

        .room-card .status-badge {
            background: #eaf1ff;
            color: #2a5adf;
            border-radius: 12px;
            padding: 2px 10px;
            font-size: 0.95rem;
            margin-left: 8px;
        }

        .room-card .room-type {
            color: #888;
            margin-bottom: 20px;
        }

        .room-card .actions {
            display: flex;
            gap: 12px;
            margin-bottom: 0px;
        }

        .room-card .actions button {
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 1rem;
            cursor: pointer;
        }

        .room-card .actions .report {
            background: #fff;
            color: #6a2323;
            border: 1px solid #6a2323;
        }

        .room-card .actions .start {
            background: #6a2323;
            color: #fff;
        }

        .room-card .status-badge {
            background: #eaf1ff;
            color: #fff;
            border-radius: 12px;
            padding: 2px 10px;
            font-size: 0.95rem;
            margin-left: 8px;
        }

        .room-card .badge-dirty {
            background: red !important;
            color: #fff !important;
        }

        .room-card .badge-clean {
            background: #27ae60 !important;
            color: #fff !important;
        }

        .room-card .badge-inprogress {
            background: #5dade2 !important;
            color: #fff !important;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>
            <h3 style="color: white;">Welcome, <?php echo htmlspecialchars($staffName); ?></h3>
        </div>
        <div class="right" style="position:relative;">
            <span id="notification-bell" style="position:relative; cursor:pointer; margin-right:18px;">
                <svg xmlns="http://www.w3.org/2000/svg" height="22" viewBox="0 0 24 24" width="22" fill="#fff">
                    <path d="M12 22c1.1 0 2-.9 2-2h-4a2 2 0 0 0 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4a1.5 1.5 0 0 0-3 0v.68C7.63 5.36 6 7.92 6 11v5l-1.7 1.7c-.14.14-.3.33-.3.6V19h16v-.7c0-.27-.16-.46-.3-.6L18 16z" />
                </svg>
                <span id="notification-badge" style="position:absolute;top:-8px;right:-8px;background:#fff;color:#6a2323;border-radius:50%;width:18px;height:18px;display:none;align-items:center;justify-content:center;font-size:0.75rem;font-weight:bold;">0</span>
            </span>
            <div id="notification-dropdown" style="display:none; position:absolute; right:60px; top:58px; background:white; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.15); min-width:320px; max-width:400px; max-height:500px; overflow-y:auto; z-index:10000;">
                <div style="padding:16px 20px; border-bottom:1px solid #f0f0f0; display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-weight:600; font-size:1rem; color:#333;">Notifications</div>
                    <button id="mark-all-read" style="background:none; border:none; color:#6a2323; font-size:0.85rem; cursor:pointer; text-decoration:underline;">Mark all read</button>
                </div>
                <div id="notification-list" style="max-height:400px; overflow-y:auto;">
                    <div style="padding:20px; text-align:center; color:#888;">Loading...</div>
                </div>
            </div>
            <div id="profile-avatar" style="cursor:pointer; width:40px; height:40px; display:flex; align-items:center; justify-content:center; border-radius:8px; transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='transparent'">
                <svg xmlns="http://www.w3.org/2000/svg" height="22" viewBox="0 0 24 24" width="22" fill="#fff">
                    <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" />
                </svg>
            </div>
            <div id="profile-dropdown" style="display:none; position:absolute; right:0; top:58px; background:white; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.15); min-width:240px; z-index:10000; padding:16px 0;">
                <div style="padding:12px 20px; border-bottom:1px solid #f0f0f0;">
                    <div style="font-weight:600; font-size:1.05rem; color:#333; margin-bottom:4px;"><?php echo htmlspecialchars($staffName); ?></div>
                    <div style="font-size:0.85rem; color:#888;">Staff</div>
                </div>

                <div style="padding:12px 20px; border-bottom:1px solid #f0f0f0;">
                    <div style="font-size:0.85rem; color:#666; margin-bottom:8px; font-weight:500;">Availability Status</div>
                    <select id="availability-select" style="width:100%; padding:8px 12px; border-radius:6px; border:1px solid #ddd; font-size:0.9rem; background:#fff; cursor:pointer; font-weight:500;">
                        <option value="Available">Available</option>
                        <option value="On Break">On Break</option>
                        <option value="Absent">Absent</option>
                        <option value="On Leave">On Leave</option>
                        <option value="Unavailable">Unavailable</option>
                    </select>
                </div>

                <div id="my-stats-btn" style="padding:12px 20px; border-bottom:1px solid #f0f0f0; cursor:pointer; display:flex; align-items:center; gap:12px; color:#555; transition:background 0.2s;" onmouseover="this.style.background='#f8f8f8'" onmouseout="this.style.background='transparent'">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 24 24" width="20" fill="#888">
                        <path d="M16,11V3H8v6H2v12h20V11H16z M10,5h4v14h-4V5z M4,11h4v8H4V11z M20,19h-4v-6h4V19z" />
                    </svg>
                    <span style="font-size:0.95rem;">My Stats</span>
                </div>

                <div id="profile-settings-btn" style="padding:12px 20px; border-bottom:1px solid #f0f0f0; cursor:pointer; display:flex; align-items:center; gap:12px; color:#555; transition:background 0.2s;" onmouseover="this.style.background='#f8f8f8'" onmouseout="this.style.background='transparent'">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 24 24" width="20" fill="#888">
                        <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z" />
                    </svg>
                    <span style="font-size:0.95rem;">Profile Settings</span>
                </div>

                <div style="padding:12px 20px; border-bottom:1px solid #f0f0f0;">
                    <div style="display:flex; align-items:center; gap:12px; color:#555;">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 24 24" width="20" fill="#888">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                        </svg>
                        <div style="font-size:0.9rem;">Mon-Fri, 08:00am-04:00pm</div>
                    </div>
                </div>

                <form action="logout.php" method="post" style="margin:0;">
                    <button type="submit" style="width:100%; background:none; border:none; padding:12px 20px; text-align:left; font-size:0.95rem; color:#d63031; cursor:pointer; display:flex; align-items:center; gap:12px; transition:background 0.2s;" onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='transparent'">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 24 24" width="20" fill="#d63031">
                            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="tasks-section">
        <script>
            // Profile avatar dropdown logic
            const profileAvatar = document.getElementById('profile-avatar');
            const profileDropdown = document.getElementById('profile-dropdown');
            profileAvatar.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.style.display = profileDropdown.style.display === 'block' ? 'none' : 'block';
            });
            document.addEventListener('click', function(e) {
                if (!profileAvatar.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.style.display = 'none';
                }
            });

            // Load current availability from database
            fetch('includes/get-availability.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.availability) {
                        document.getElementById('availability-select').value = data.availability;
                    }
                })
                .catch(err => console.error('Error loading availability:', err));

            // Handle availability change
            const availabilitySelect = document.getElementById('availability-select');
            availabilitySelect.addEventListener('change', function(e) {
                const newStatus = e.target.value;

                // Statuses that require a reason
                const requiresReason = ['Absent', 'On Leave', 'Unavailable'];
                let reason = '';

                if (requiresReason.includes(newStatus)) {
                    reason = prompt(`Please provide a reason for setting your status to "${newStatus}":`);

                    // If user cancels or enters empty reason, revert selection
                    if (!reason || reason.trim() === '') {
                        alert('A reason is required for this status.');
                        // Revert to previous value
                        fetch('includes/get-availability.php')
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && data.availability) {
                                    availabilitySelect.value = data.availability;
                                }
                            });
                        return;
                    }
                }

                fetch('includes/update-availability.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'availability=' + encodeURIComponent(newStatus) + '&reason=' + encodeURIComponent(reason)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Show brief confirmation
                            const originalBg = availabilitySelect.style.background;
                            availabilitySelect.style.background = '#d4edda';
                            setTimeout(() => {
                                availabilitySelect.style.background = originalBg;
                            }, 500);
                        } else {
                            alert('Error updating availability: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(err => {
                        alert('Error updating availability: ' + err.message);
                    });
            });

            // Notifications functionality
            const notificationBell = document.getElementById('notification-bell');
            const notificationDropdown = document.getElementById('notification-dropdown');
            const notificationBadge = document.getElementById('notification-badge');
            const notificationList = document.getElementById('notification-list');

            function loadNotifications() {
                fetch('includes/get-notifications.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const notifications = data.notifications || [];
                            const unreadCount = data.unreadCount || 0;

                            // Update badge
                            if (unreadCount > 0) {
                                notificationBadge.textContent = unreadCount;
                                notificationBadge.style.display = 'flex';
                            } else {
                                notificationBadge.style.display = 'none';
                            }

                            // Render notifications
                            if (notifications.length === 0) {
                                notificationList.innerHTML = '<div style="padding:20px; text-align:center; color:#888;">No notifications</div>';
                            } else {
                                let html = '';
                                notifications.forEach(notif => {
                                    const isRead = notif.IsRead == 1;
                                    const bgColor = isRead ? '#fff' : '#f9fafb';
                                    const typeColor = notif.Type === 'warning' ? '#f59e0b' : notif.Type === 'error' ? '#ef4444' : '#6a2323';

                                    html += `<div class="notification-item" data-id="${notif.NotificationID}" style="padding:12px 20px; border-bottom:1px solid #f0f0f0; cursor:pointer; background:${bgColor}; transition:background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='${bgColor}'">
                                    <div style="display:flex; align-items:start; gap:8px;">
                                        <div style="width:8px; height:8px; border-radius:50%; background:${typeColor}; margin-top:6px; ${isRead ? 'opacity:0.3;' : ''}"></div>
                                        <div style="flex:1;">
                                            <div style="font-size:0.9rem; color:#333; ${isRead ? '' : 'font-weight:600;'}">${notif.Message}</div>
                                            <div style="font-size:0.75rem; color:#888; margin-top:4px;">${new Date(notif.CreatedAt).toLocaleString()}</div>
                                        </div>
                                    </div>
                                </div>`;
                                });
                                notificationList.innerHTML = html;

                                // Add click handlers to mark as read
                                document.querySelectorAll('.notification-item').forEach(item => {
                                    item.addEventListener('click', function() {
                                        const notifId = this.dataset.id;
                                        fetch('includes/mark-notification-read.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded'
                                                },
                                                body: `notificationId=${notifId}`
                                            })
                                            .then(() => loadNotifications());
                                    });
                                });
                            }
                        }
                    })
                    .catch(err => console.error('Error loading notifications:', err));
            }

            // Toggle notification dropdown
            notificationBell.addEventListener('click', function(e) {
                e.stopPropagation();
                const isVisible = notificationDropdown.style.display === 'block';
                notificationDropdown.style.display = isVisible ? 'none' : 'block';
                if (!isVisible) {
                    loadNotifications();
                }
            });

            // Mark all as read
            document.getElementById('mark-all-read').addEventListener('click', function() {
                fetch('includes/mark-all-notifications-read.php', {
                        method: 'POST'
                    })
                    .then(() => loadNotifications());
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.style.display = 'none';
                }
            });

            // Load notifications on page load and refresh every 30 seconds
            loadNotifications();
            setInterval(loadNotifications, 30000);
        </script>
        <h2>Your Tasks</h2>
        <p>Filter and manage your assigned rooms.</p>
        <?php if (isset($errorMessage)): ?>
            <div style="background: #fee; border: 1px solid #fcc; color: #c33; padding: 16px; border-radius: 6px; margin-bottom: 20px;">
                <strong>⚠️ Account Issue:</strong> <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>
        <?php if (empty($rooms) && !isset($errorMessage)): ?>
            <div style="background: #fffbea; border: 1px solid #ffd93d; color: #856404; padding: 16px; border-radius: 6px; margin-bottom: 20px;">
                <strong>ℹ️ No Tasks:</strong> You currently have no assigned cleaning tasks.
            </div>
        <?php endif; ?>
        <div class="filter-btns">
            <?php foreach ($statusOptions as $status): ?>
                <button class="<?php echo $status === 'Dirty' ? 'active' : ''; ?>" onclick="filterRooms('<?php echo $status; ?>')"><?php echo $status; ?></button>
            <?php endforeach; ?>
        </div>
        <div id="no-rooms-indicator" style="display:none; background:#f0f0f0; border:1px solid #ddd; border-radius:8px; padding:32px; text-align:center; color:#666;">
            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#999" style="margin-bottom:12px;">
                <path d="M480-280q17 0 28.5-11.5T520-320q0-17-11.5-28.5T480-360q-17 0-28.5 11.5T440-320q0 17 11.5 28.5T480-280Zm-40-160h80v-240h-80v240Zm40 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
            </svg>
            <h3 style="margin:0 0 8px 0; color:#555;">No rooms found</h3>
            <p style="margin:0; font-size:0.95rem;">There are no rooms with this status.</p>
        </div>
        <div class="room-cards" id="roomCards">
            <?php foreach ($rooms as $room): ?>
                <div class="room-card" data-status="<?php echo htmlspecialchars($room['Status'] ?? ''); ?>" style="<?php echo (strtolower($room['Status'] ?? '') !== 'dirty') ? 'display:none;' : ''; ?>">
                    <div class="room-title">
                        <span>🛏️ Room <?php echo htmlspecialchars($room['RoomNumber'] ?? ''); ?></span>
                        <?php
                        $stat = strtolower($room['Status'] ?? '');
                        $badgeClass = '';
                        if ($stat === 'dirty') {
                            $badgeClass = 'badge-dirty';
                        } elseif ($stat === 'clean') {
                            $badgeClass = 'badge-clean';
                        } elseif ($stat === 'in progress') {
                            $badgeClass = 'badge-inprogress';
                        }
                        ?>
                        <span class="status-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($room['Status'] ?? ''); ?></span>
                    </div>
                    <div class="room-type">
                        <?php echo htmlspecialchars($room['RoomType'] ?? ''); ?>
                        <span class="cleaning-timer" style="display:none; font-weight:bold; color:#27ae60; margin-left:10px;"></span>
                    </div>
                    <div class="actions">
                        <button class="report">🔗 Report Issue</button>
                        <?php
                        $isClean = strtolower($room['Status'] ?? '') === 'clean';
                        ?>
                        <button class="start" <?php echo $isClean ? 'disabled' : ''; ?>><?php echo $isClean ? 'Cleaned' : '▶ Start Cleaning'; ?></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="cleaning-confirm-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:99999; align-items:center; justify-content:center;">
        <div style="background:white; padding:32px 24px; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,0.2); min-width:280px; text-align:center;">
            <h3 id="cleaning-confirm-title" style="margin-bottom:18px; color:#6a2323;">Start cleaning this room?</h3>
            <button id="cleaning-confirm-yes" style="background:#27ae60; color:white; border:none; border-radius:6px; padding:8px 24px; margin-right:12px; cursor:pointer;">Yes</button>
            <button id="cleaning-confirm-no" style="background:#e74c3c; color:white; border:none; border-radius:6px; padding:8px 24px; cursor:pointer;">No</button>
        </div>
    </div>

    <div id="report-issue-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:99999; align-items:center; justify-content:center;">
        <div style="background:white; padding:28px 24px; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,0.2); min-width:320px; display:flex; flex-direction:column; align-items:flex-start; gap:18px;">
            <h3 style="margin:0 0 12px 0; color:#6a2323; text-align:left; width:100%; font-size:1.15rem;">Report Issue</h3>
            <textarea id="report-issue-input" rows="4" style="width:100%; margin:0 0 12px 0; border-radius:6px; border:1px solid #ccc; padding:10px;"></textarea>
            <div style="width:100%; display:flex; justify-content:flex-end; gap:12px;">
                <button id="report-issue-cancel" style="background:#e74c3c; color:white; border:none; border-radius:6px; padding:8px 24px; cursor:pointer;">Cancel</button>
                <button id="report-issue-submit" style="background:#27ae60; color:white; border:none; border-radius:6px; padding:8px 24px; cursor:pointer;">Submit</button>
            </div>
        </div>
    </div>
    <script>
        function filterRooms(status) {
            document.querySelectorAll('.filter-btns button').forEach(btn => {
                btn.classList.toggle('active', btn.textContent === status);
            });

            let visibleCount = 0;
            document.querySelectorAll('.room-card').forEach(card => {
                // Compare lowercased stat for robustness
                if (status === 'All' || (card.dataset.status && card.dataset.status.toLowerCase() === status.toLowerCase())) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide no rooms indicator
            const indicator = document.getElementById('no-rooms-indicator');
            if (visibleCount === 0) {
                indicator.style.display = 'block';
            } else {
                indicator.style.display = 'none';
            }
        }

        // Custom modal confirmation and timer for Start/Done Cleaning
        let cleaningTarget = null;
        let cleaningStep = 'start';
        let reportTarget = null;
        document.addEventListener('DOMContentLoaded', function() {
            // Check if default filter shows any rooms
            const visibleCards = Array.from(document.querySelectorAll('.room-card')).filter(card => card.style.display !== 'none');
            const indicator = document.getElementById('no-rooms-indicator');
            if (visibleCards.length === 0) {
                indicator.style.display = 'block';
            }

            document.querySelectorAll('.room-card .start').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    const card = btn.closest('.room-card');
                    const timerSpan = card.querySelector('.cleaning-timer');
                    cleaningTarget = {
                        btn,
                        card,
                        timerSpan
                    };
                    if (btn.textContent === '▶ Start Cleaning') {
                        cleaningStep = 'start';
                        document.getElementById('cleaning-confirm-title').textContent = 'Start cleaning this room?';
                        document.getElementById('cleaning-confirm-modal').style.display = 'flex';
                    } else if (btn.textContent === 'Done Cleaning') {
                        cleaningStep = 'done';
                        document.getElementById('cleaning-confirm-title').textContent = 'Finish cleaning this room?';
                        document.getElementById('cleaning-confirm-modal').style.display = 'flex';
                    }
                });
            });

            // Report Issue button functionality
            document.querySelectorAll('.room-card .report').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    reportTarget = btn.closest('.room-card');
                    document.getElementById('report-issue-input').value = '';
                    document.getElementById('report-issue-modal').style.display = 'flex';
                });
            });

            document.getElementById('report-issue-submit').onclick = function() {
                if (!reportTarget) return;
                const roomNumber = reportTarget.querySelector('.room-title span').textContent.replace(/[^\d]/g, '');
                const issueText = document.getElementById('report-issue-input').value.trim();
                if (!issueText) {
                    alert('Please enter an issue description.');
                    return;
                }
                // AJAX to save issue in DB
                fetch('includes/report-room-issue.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `roomNumber=${encodeURIComponent(roomNumber)}&issueText=${encodeURIComponent(issueText)}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('report-issue-modal').style.display = 'none';
                            alert('Issue reported for room ' + roomNumber + ': ' + issueText);
                        } else {
                            alert('Failed to report issue.');
                        }
                        reportTarget = null;
                    });
            };
            document.getElementById('report-issue-cancel').onclick = function() {
                document.getElementById('report-issue-modal').style.display = 'none';
                reportTarget = null;
            };

            document.getElementById('cleaning-confirm-yes').onclick = function() {
                if (!cleaningTarget) return;
                let {
                    btn,
                    card,
                    timerSpan
                } = cleaningTarget;
                if (cleaningStep === 'start') {
                    // Update assignment status to In Progress
                    const roomNumber = card.querySelector('.room-title span').textContent.replace(/[^\d]/g, '');
                    fetch('includes/update-assignment-status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `roomNumber=${encodeURIComponent(roomNumber)}&newStatus=In Progress`
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                // Start timer
                                let seconds = 0;
                                timerSpan.style.display = '';
                                btn.textContent = 'Done Cleaning';
                                btn.disabled = false;
                                timerSpan.textContent = 'Timer: 00:00';
                                // Store timer state on card
                                card._cleaningSeconds = 0;
                                const interval = setInterval(function() {
                                    seconds++;
                                    card._cleaningSeconds = seconds;
                                    const min = String(Math.floor(seconds / 60)).padStart(2, '0');
                                    const sec = String(seconds % 60).padStart(2, '0');
                                    timerSpan.textContent = `Timer: ${min}:${sec}`;
                                }, 1000);
                                card._cleaningInterval = interval;

                                // Update room status badge to In Progress
                                const badge = card.querySelector('.status-badge');
                                badge.textContent = 'In Progress';
                                badge.classList.remove('badge-dirty', 'badge-clean');
                                badge.classList.add('badge-inprogress');
                                // Update data-status attribute for filtering
                                card.dataset.status = 'In Progress';
                            } else {
                                alert('Failed to start cleaning: ' + (data.error || 'Unknown error'));
                            }
                        })
                        .catch(err => {
                            alert('Error starting cleaning: ' + err.message);
                        });
                } else if (cleaningStep === 'done') {
                    // Stop timer
                    if (card._cleaningInterval) clearInterval(card._cleaningInterval);
                    timerSpan.style.display = 'none';
                    // AJAX update Status in DB and save cleaningTime
                    const roomNumber = card.querySelector('.room-title span').textContent.replace(/[^\d]/g, '');
                    const cleaningTime = card._cleaningSeconds || 0;

                    console.log('Sending:', {
                        roomNumber,
                        newStatus: 'Clean',
                        cleaningTime
                    });

                    fetch('includes/update-room-status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `roomNumber=${encodeURIComponent(roomNumber)}&newStatus=Clean&cleaningTime=${encodeURIComponent(cleaningTime)}`
                        })
                        .then(res => {
                            return res.text().then(text => {
                                console.log('Raw response:', text);
                                try {
                                    return JSON.parse(text);
                                } catch (e) {
                                    console.error('Failed to parse JSON:', text);
                                    throw new Error('Invalid JSON response: ' + text.substring(0, 100));
                                }
                            });
                        })
                        .then(data => {
                            console.log('Response:', data);

                            if (data.success) {

                                // UI Update
                                btn.textContent = 'Cleaned';
                                btn.disabled = true;

                                const badge = card.querySelector('.status-badge');
                                badge.textContent = 'Clean';
                                badge.classList.remove('badge-dirty', 'badge-inprogress');
                                badge.classList.add('badge-clean');

                                card.dataset.status = 'Clean';

                                // STEP 2 → send notification
                                fetch('add_notification.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        body: `room_id=${card.dataset.roomId}&message=Room ${card.dataset.roomNumber} cleaned`
                                    })
                                    .then(res => res.json())
                                    .then(nData => {
                                        console.log("Notif Response:", nData);
                                    });

                            } else {
                                alert('Failed to update room status: ' + (data.error || 'Unknown error'));
                            }
                        })

                        .catch(err => {
                            console.error('Error:', err);
                            alert('Error updating room status: ' + err.message);
                        });
                }
                document.getElementById('cleaning-confirm-modal').style.display = 'none';
                cleaningTarget = null;
            };
            document.getElementById('cleaning-confirm-no').onclick = function() {
                document.getElementById('cleaning-confirm-modal').style.display = 'none';
                cleaningTarget = null;
            };

            // My Stats Modal
            const myStatsBtn = document.getElementById('my-stats-btn');
            const statsModal = document.getElementById('stats-modal');
            const statsOverlay = document.getElementById('stats-overlay');
            const closeStats = document.getElementById('close-stats');

            myStatsBtn.addEventListener('click', function() {
                // Close profile dropdown
                profileDropdown.style.display = 'none';
                // Load stats data
                fetch('includes/get-staff-stats.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const stats = data.stats;
                            document.getElementById('stat-total-completed').textContent = stats.totalCompleted;
                            document.getElementById('stat-total-pending').textContent = stats.totalPending;
                            document.getElementById('stat-avg-time').textContent = stats.avgCleaningTime;
                            document.getElementById('stat-today').textContent = stats.completedToday;
                            document.getElementById('stat-week').textContent = stats.completedThisWeek;
                            document.getElementById('stat-month').textContent = stats.completedThisMonth;

                            // Display recent tasks
                            const recentTasksList = document.getElementById('recent-tasks-list');
                            if (stats.recentTasks.length === 0) {
                                recentTasksList.innerHTML = '<div style="text-align:center; padding:40px; color:#999;">No completed tasks yet</div>';
                            } else {
                                recentTasksList.innerHTML = stats.recentTasks.map(task => {
                                    const completedDate = new Date(task.completedAt);
                                    const dateStr = completedDate.toLocaleDateString('en-US', {
                                        month: 'short',
                                        day: 'numeric',
                                        year: 'numeric'
                                    });
                                    const timeStr = completedDate.toLocaleTimeString('en-US', {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    });
                                    return `
                                        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:16px; display:flex; justify-content:space-between; align-items:center;">
                                            <div>
                                                <div style="font-weight:600; color:#6a2323; margin-bottom:4px;">Room ${task.roomNumber}</div>
                                                <div style="font-size:0.9rem; color:#666;">${task.taskDescription || 'Cleaning task'}</div>
                                                <div style="font-size:0.85rem; color:#999; margin-top:4px;">${dateStr} at ${timeStr}</div>
                                            </div>
                                            <div style="background:#d1fae5; color:#065f46; padding:6px 12px; border-radius:6px; font-weight:600; font-size:0.9rem;">
                                                ${task.cleaningTime ? task.cleaningTime + ' min' : 'N/A'}
                                            </div>
                                        </div>
                                    `;
                                }).join('');
                            }
                        } else {
                            console.error('Error loading stats:', data.error);
                            alert('Error loading statistics: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching stats:', err);
                        alert('Error loading statistics');
                    });
                statsModal.style.display = 'block';
                statsOverlay.style.display = 'block';
            });

            closeStats.addEventListener('click', function() {
                statsModal.style.display = 'none';
                statsOverlay.style.display = 'none';
            });

            statsOverlay.addEventListener('click', function() {
                statsModal.style.display = 'none';
                statsOverlay.style.display = 'none';
            });

            // Profile Settings Modal
            const profileSettingsBtn = document.getElementById('profile-settings-btn');
            const profileSettingsModal = document.getElementById('profile-settings-modal');
            const profileSettingsOverlay = document.getElementById('profile-settings-overlay');
            const closeProfileSettings = document.getElementById('close-profile-settings');

            profileSettingsBtn.addEventListener('click', function() {
                // Close profile dropdown
                profileDropdown.style.display = 'none';
                // Load current user data
                fetch('includes/get-staff-profile.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('profile-username').value = data.username || '';
                            document.getElementById('profile-email').value = data.email || '';
                            document.getElementById('profile-phone').value = data.phone || '';
                        } else {
                            console.error('Error loading profile:', data.error);
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching profile:', err);
                    });
                profileSettingsModal.style.display = 'block';
                profileSettingsOverlay.style.display = 'block';
            });

            closeProfileSettings.addEventListener('click', function() {
                profileSettingsModal.style.display = 'none';
                profileSettingsOverlay.style.display = 'none';
            });

            profileSettingsOverlay.addEventListener('click', function() {
                profileSettingsModal.style.display = 'none';
                profileSettingsOverlay.style.display = 'none';
            });

            // Save profile changes
            document.getElementById('save-profile-btn').addEventListener('click', function() {
                const username = document.getElementById('profile-username').value.trim();
                const email = document.getElementById('profile-email').value.trim();
                const phone = document.getElementById('profile-phone').value.trim();
                const currentPassword = document.getElementById('profile-current-password').value;
                const newPassword = document.getElementById('profile-new-password').value;
                const confirmPassword = document.getElementById('profile-confirm-password').value;

                // Validate passwords if changing
                if (newPassword || confirmPassword) {
                    if (!currentPassword) {
                        alert('Please enter your current password to change your password.');
                        return;
                    }
                    if (newPassword !== confirmPassword) {
                        alert('New passwords do not match.');
                        return;
                    }
                    if (newPassword.length < 6) {
                        alert('New password must be at least 6 characters.');
                        return;
                    }
                }

                // Validate email format
                if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                    alert('Please enter a valid email address.');
                    return;
                }

                const formData = new URLSearchParams();
                formData.append('username', username);
                formData.append('email', email);
                formData.append('phone', phone);
                if (currentPassword) formData.append('currentPassword', currentPassword);
                if (newPassword) formData.append('newPassword', newPassword);

                fetch('includes/update-staff-profile.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: formData.toString()
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Profile updated successfully!');
                            profileSettingsModal.style.display = 'none';
                            profileSettingsOverlay.style.display = 'none';
                            // Clear password fields
                            document.getElementById('profile-current-password').value = '';
                            document.getElementById('profile-new-password').value = '';
                            document.getElementById('profile-confirm-password').value = '';
                            // Update displayed username in real-time if changed
                            if (data.usernameChanged) {
                                const headerUsername = document.querySelector('.header h3');
                                if (headerUsername) {
                                    const newUsername = document.getElementById('profile-username').value;
                                    headerUsername.textContent = 'Welcome, ' + newUsername;
                                }
                            }
                        } else {
                            alert('Error: ' + (data.error || 'Failed to update profile'));
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        alert('An error occurred while updating profile.');
                    });
            });
        });
    </script>

    <!-- My Stats Modal -->
    <div id="stats-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9998;"></div>
    <div id="stats-modal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.2); width:90%; max-width:700px; z-index:9999; max-height:90vh; overflow-y:auto;">
        <div style="padding:24px; border-bottom:1px solid #f0f0f0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="margin:0; color:#6a2323; font-size:1.5rem;">My Performance Stats</h2>
            <button id="close-stats" style="background:none; border:none; cursor:pointer; font-size:1.5rem; color:#999; width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:50%; transition:background 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='transparent'">&times;</button>
        </div>
        <div style="padding:24px;">
            <!-- Stats Grid -->
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:32px;">
                <div style="background:#f0f9ff; padding:20px; border-radius:10px; border-left:4px solid #3b82f6;">
                    <div style="color:#3b82f6; font-size:0.85rem; font-weight:600; margin-bottom:8px;">TOTAL COMPLETED</div>
                    <div id="stat-total-completed" style="font-size:2rem; font-weight:700; color:#1e40af;">0</div>
                </div>
                <div style="background:#fef3c7; padding:20px; border-radius:10px; border-left:4px solid #f59e0b;">
                    <div style="color:#f59e0b; font-size:0.85rem; font-weight:600; margin-bottom:8px;">PENDING TASKS</div>
                    <div id="stat-total-pending" style="font-size:2rem; font-weight:700; color:#92400e;">0</div>
                </div>
                <div style="background:#d1fae5; padding:20px; border-radius:10px; border-left:4px solid #10b981;">
                    <div style="color:#10b981; font-size:0.85rem; font-weight:600; margin-bottom:8px;">AVG TIME (MIN)</div>
                    <div id="stat-avg-time" style="font-size:2rem; font-weight:700; color:#065f46;">0</div>
                </div>
            </div>

            <!-- Period Stats -->
            <div style="background:#f9fafb; padding:20px; border-radius:10px; margin-bottom:32px;">
                <h3 style="margin:0 0 16px 0; color:#6a2323; font-size:1.1rem;">Completed Tasks by Period</h3>
                <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px;">
                    <div style="text-align:center;">
                        <div style="font-size:0.85rem; color:#666; margin-bottom:4px;">Today</div>
                        <div id="stat-today" style="font-size:1.8rem; font-weight:700; color:#6a2323;">0</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:0.85rem; color:#666; margin-bottom:4px;">This Week</div>
                        <div id="stat-week" style="font-size:1.8rem; font-weight:700; color:#6a2323;">0</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:0.85rem; color:#666; margin-bottom:4px;">This Month</div>
                        <div id="stat-month" style="font-size:1.8rem; font-weight:700; color:#6a2323;">0</div>
                    </div>
                </div>
            </div>

            <!-- Recent Tasks -->
            <div>
                <h3 style="margin:0 0 16px 0; color:#6a2323; font-size:1.1rem;">Recent Completed Tasks</h3>
                <div id="recent-tasks-list" style="display:flex; flex-direction:column; gap:12px;">
                    <div style="text-align:center; padding:40px; color:#999;">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Settings Modal -->
    <div id="profile-settings-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9998;"></div>
    <div id="profile-settings-modal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.2); width:90%; max-width:500px; z-index:9999; max-height:90vh; overflow-y:auto;">
        <div style="padding:24px 28px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="margin:0; font-size:1.5rem; color:#333;">Profile Settings</h2>
            <button id="close-profile-settings" style="background:none; border:none; cursor:pointer; font-size:1.8rem; color:#888; line-height:1; padding:0; width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:50%; transition:background 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='transparent'">&times;</button>
        </div>
        <div style="padding:28px;">
            <div style="margin-bottom:24px;">
                <label style="display:block; margin-bottom:8px; font-weight:500; color:#555; font-size:0.95rem;">Username</label>
                <input type="text" id="profile-username" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:1rem; box-sizing:border-box;">
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; margin-bottom:8px; font-weight:500; color:#555; font-size:0.95rem;">Email</label>
                <input type="email" id="profile-email" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:1rem; box-sizing:border-box;">
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; margin-bottom:8px; font-weight:500; color:#555; font-size:0.95rem;">Phone Number</label>
                <input type="tel" id="profile-phone" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:1rem; box-sizing:border-box;">
            </div>

            <hr style="border:none; border-top:1px solid #eee; margin:32px 0;">

            <div style="margin-bottom:16px;">
                <h3 style="margin:0 0 16px 0; font-size:1.1rem; color:#333;">Change Password</h3>
                <p style="margin:0 0 20px 0; font-size:0.9rem; color:#888;">Leave blank if you don't want to change your password</p>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:8px; font-weight:500; color:#555; font-size:0.95rem;">Current Password</label>
                <input type="password" id="profile-current-password" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:1rem; box-sizing:border-box;">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:8px; font-weight:500; color:#555; font-size:0.95rem;">New Password</label>
                <input type="password" id="profile-new-password" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:1rem; box-sizing:border-box;">
            </div>

            <div style="margin-bottom:28px;">
                <label style="display:block; margin-bottom:8px; font-weight:500; color:#555; font-size:0.95rem;">Confirm New Password</label>
                <input type="password" id="profile-confirm-password" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:1rem; box-sizing:border-box;">
            </div>

            <button id="save-profile-btn" style="width:100%; padding:14px; background:#6a2323; color:white; border:none; border-radius:8px; font-size:1.05rem; font-weight:600; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#551c1c'" onmouseout="this.style.background='#6a2323'">Save Changes</button>
        </div>
    </div>
</body>

</html>