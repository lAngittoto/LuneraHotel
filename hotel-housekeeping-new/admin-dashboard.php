<?php
session_start();
require("includes/database.php");
require("includes/admin-dashboard(inc).php");
// Redirect to login if not authenticated
if (!isset($_SESSION['username']) || $_SESSION['accType'] !== 'admin') {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/svg+xml" href='data:image/svg+xml, <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="maroon"><path d="M484-80q-84 0-157.5-32t-128-86.5Q144-253 112-326.5T80-484q0-146 93-257.5T410-880q-18 99 11 193.5T521-521q71 71 165.5 100T880-410q-26 144-138 237T484-80Zm0-80q88 0 163-44t118-121q-86-8-163-43.5T464-465q-61-61-97-138t-43-163q-77 43-120.5 118.5T160-484q0 135 94.5 229.5T484-160Zm-20-305Z"/></svg>'>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/admin-dashboard.css">
    <link rel="stylesheet" href="styles/admin-assignments.css">
    <link rel="stylesheet" href="styles/admin-StaffManagement.css">
</head>
<body>
    <header>
        <a class="mobile-menu" href="#"><svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="white"><path d="M120-240v-80h520v80H120Zm664-40L584-480l200-200 56 56-144 144 144 144-56 56ZM120-440v-80h400v80H120Zm0-200v-80h520v80H120Z"/></svg></a>
        <h3>Housekeeping System</h3>
        <div class="items">
            <nav>
                <ul>
                    <li id="notification-bell" style="position:relative; cursor:pointer;">
                        <a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="white"><path d="M200-200q-17 0-28.5-11.5T160-240q0-17 11.5-28.5T200-280h40v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v28q80 20 130 84.5T720-560v280h40q17 0 28.5 11.5T800-240q0 17-11.5 28.5T760-200H200Zm280-300Zm0 420q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80ZM320-280h320v-280q0-66-47-113t-113-47q-66 0-113 47t-47 113v280Z"/></svg>
                        </a>
                        <span id="notification-badge" style="position:absolute;top:2px;right:-4px;background:#fff;color:maroon;border-radius:50%;width:18px;height:18px;display:none;align-items:center;justify-content:center;font-size:0.75rem;font-weight:bold;">0</span>
                    </li>
                    <div id="notification-dropdown" style="display:none; position:absolute; right:60px; top:50px; background:white; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.15); min-width:320px; max-width:400px; max-height:500px; overflow-y:auto; z-index:10000;">
                        <div style="padding:16px 20px; border-bottom:1px solid #f0f0f0; display:flex; justify-content:space-between; align-items:center;">
                            <div style="font-weight:600; font-size:1rem; color:#333;">Notifications</div>
                            <button id="mark-all-read" style="background:none; border:none; color:maroon; font-size:0.85rem; cursor:pointer; text-decoration:underline;">Mark all read</button>
                        </div>
                        <div id="notification-list" style="max-height:400px; overflow-y:auto;">
                            <div style="padding:20px; text-align:center; color:#888;">Loading...</div>
                        </div>
                    </div>
                    <li class="burger-menu" style="position:relative;">
                        <a href="#" id="burger-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22" fill="white"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>
                        </a>
                        <div id="burger-dropdown" style="display:none; position:absolute; right:0; top:30px; background:white; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.12); min-width:120px; z-index:10000;">
                            <form action="logout.php" method="post" style="margin:0;">
                                <button type="submit" style="width:100%; background:none; border:none; padding:12px 18px; text-align:left; font-size:1rem; color:maroon; cursor:pointer;">Logout</button>
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="leftNavArea">
        <a href="index.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960" fill="white"><path d="M484-80q-84 0-157.5-32t-128-86.5Q144-253 112-326.5T80-484q0-146 93-257.5T410-880q-18 99 11 193.5T521-521q71 71 165.5 100T880-410q-26 144-138 237T484-80Zm0-80q88 0 163-44t118-121q-86-8-163-43.5T464-465q-61-61-97-138t-43-163q-77 43-120.5 118.5T160-484q0 135 94.5 229.5T484-160Zm-20-305Z"/></svg><h1>Lunera Hotel</h1></a>
        <nav>
            <ul>
                <li><a href="#" id="dashboard" class="active"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M513.33-580v-260H840v260H513.33ZM120-446.67V-840h326.67v393.33H120ZM513.33-120v-393.33H840V-120H513.33ZM120-120v-260h326.67v260H120Zm66.67-393.33H380v-260H186.67v260ZM580-186.67h193.33v-260H580v260Zm0-460h193.33v-126.66H580v126.66Zm-393.33 460H380v-126.66H186.67v126.66ZM380-513.33Zm200-133.34Zm0 200ZM380-313.33Z"/></svg>Dashboard</a></li>
                <li><a href="#" id="assignments"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M186.67-120q-27.5 0-47.09-19.58Q120-159.17 120-186.67v-586.66q0-27.5 19.58-47.09Q159.17-840 186.67-840h192.66q7.67-35.33 35.84-57.67Q443.33-920 480-920t64.83 22.33Q573-875.33 580.67-840h192.66q27.5 0 47.09 19.58Q840-800.83 840-773.33v586.66q0 27.5-19.58 47.09Q800.83-120 773.33-120H186.67Zm0-66.67h586.66v-586.66H186.67v586.66ZM280-280h275.33v-66.67H280V-280Zm0-166.67h400v-66.66H280v66.66Zm0-166.66h400V-680H280v66.67Zm200-181.34q13.67 0 23.5-9.83t9.83-23.5q0-13.67-9.83-23.5t-23.5-9.83q-13.67 0-23.5 9.83t-9.83 23.5q0 13.67 9.83 23.5t23.5 9.83Zm-293.33 608v-586.66 586.66Z"/></svg>Assignments</a></li>
                <li><a href="#" id="assignment-history"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-120q-138 0-240.5-91.5T122-440h82q14 104 92.5 172T480-200q117 0 198.5-81.5T760-480q0-117-81.5-198.5T480-760q-69 0-129 32t-101 88h110v80H120v-240h80v94q51-64 124.5-99T480-840q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-120Zm112-192L440-464v-216h80v184l128 128-56 56Z"/></svg>Assignment History</a></li>
                <li><a href="#" id="staff"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-480.67q-66 0-109.67-43.66Q326.67-568 326.67-634t43.66-109.67Q414-787.33 480-787.33t109.67 43.66Q633.33-700 633.33-634t-43.66 109.67Q546-480.67 480-480.67ZM160-160v-100q0-36.67 18.5-64.17T226.67-366q65.33-30.33 127.66-45.5 62.34-15.17 125.67-15.17t125.33 15.5q62 15.5 127.28 45.3 30.54 14.42 48.96 41.81Q800-296.67 800-260v100H160Zm66.67-66.67h506.66V-260q0-14.33-8.16-27-8.17-12.67-20.5-19-60.67-29.67-114.34-41.83Q536.67-360 480-360t-111 12.17Q314.67-335.67 254.67-306q-12.34 6.33-20.17 19-7.83 12.67-7.83 27v33.33ZM480-547.33q37 0 61.83-24.84Q566.67-597 566.67-634t-24.84-61.83Q517-720.67 480-720.67t-61.83 24.84Q393.33-671 393.33-634t24.84 61.83Q443-547.33 480-547.33Zm0-86.67Zm0 407.33Z"/></svg>Staff Management</a></li>
                <li><a href="#" id="staff-list"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm0-80h640v-400H160v400Zm40-80h240v-80H200v80Zm382-80q25 0 42.5-17.5T642-460q0-25-17.5-42.5T582-520q-25 0-42.5 17.5T522-460q0 25 17.5 42.5T582-400Zm-80 120q17 0 28.5-11.5T542-320q-3-29-26.5-49.5T462-390h-80q-30 0-53.5 20.5T302-320q0 17 11.5 28.5T342-280h160Zm218-40h80v-80h-80v80ZM200-480h240v-80H200v80Zm382-80q25 0 42.5-17.5T642-620q0-25-17.5-42.5T582-680q-25 0-42.5 17.5T522-620q0 25 17.5 42.5T582-560Zm138 80h80v-80h-80v80ZM160-240v-400 400Z"/></svg>Staff List</a></li>
                <li><a href="#" id="room-list"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M120-120v-560h160v-160h400v320h160v400h-80v-80H200v80h-80Zm80-440h80v-80h-80v80Zm0 160h80v-80h-80v80Zm0 160h80v-80h-80v80Zm160-320h80v-80h-80v80Zm0 160h80v-80h-80v80Zm0 160h80v-80h-80v80Zm160-320h80v-80h-80v80Zm0 160h80v-80h-80v80Zm0 160h80v-80h-80v80Zm160 0h80v-80h-80v80Zm0-160h80v-80h-80v80Z"/></svg>Room List</a></li>
                <li><a href="#" id="reports"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M130.67-220 80-270.67l300-300L540-410l293.33-330L880-694 540-310 380-469.33 130.67-220Z"/></svg>Staff Reports</a></li>
                <li><a href="#" id="maintenance-reports"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M756-120 537-339l84-84 219 219-84 84Zm-552 0-84-84 276-276-68-68-28 28-51-51v82l-28 28-121-121 28-28h82l-50-50 142-142q20-20 43-29t47-9q24 0 47 9t43 29l-92 92 50 50-28 28 68 68 90-90q-4-11-6.5-23t-2.5-24q0-59 40.5-99.5T701-841q15 0 28.5 3t27.5 9l-99 99 72 72 99-99q7 14 9.5 27.5T841-701q0 59-40.5 99.5T701-561q-12 0-24-2t-23-7L204-120Z"/></svg>Maintenance Reports</a></li>

            </ul>
        </nav>
    </div>
    <div class="dashboard-content" id="admin-dashboard">
        <div class="overview">
            <h2>Overview</h2>
            <p>Real-time status of rooms</p>
            <div class="total">
                <p>Total Rooms</p>
                <p class="countTotal"><?php echo $totalRooms; ?></p>
            </div>
            <div class="total-room-cards">
                <div class="clean">
                    <p class="p-label">Clean</p>
                    <p class="count" style="color: green;"><?php echo $counts["Clean"]; ?></p>
                </div>
                <div class="dirty">
                    <p class="p-label">Dirty</p>
                    <p class="count" style="color: red;"><?php echo $counts["Dirty"]; ?></p>
                </div>
                <div class="inProgress">
                    <p class="p-label">In Progress</p>
                    <p class="count" style="color: blue;"><?php echo $counts["In Progress"]; ?></p>
                </div>
                <div class="maintenance">
                    <p class="p-label">Under Maintenance</p>
                    <p class="count" style="color: orange;"><?php echo $counts["Maintenance"]; ?></p>
                </div>
            </div>
        </div>
        <div class="room-status-board">
            <div class="search-area">
                <div class="filter">
                    <h2>Room Status Board</h2>
                    <select id="statusFilter">
                        <option value="All">All Statuses</option>
                        <option value="Clean">Clean</option>
                        <option value="Dirty">Dirty</option>
                        <option value="InProgress">In Progress</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                <input class="search" id="roomSearch" type="search" placeholder="Search by room number...">
            </div>
            <div class="overview-items" id="tableContainer"></div>
        </div>
    </div>
    <div class="assignments-content" id="admin-assignments" style="overflow: scroll;">
        <div class="cleaning-assignments">
            <div class="cleaning-assignment-label">
                <div>
                    <h2>Cleaning Assignments</h2>
                    <p>View incoming cleaning requests and assign staff</p>
                </div>
            </div>
            <div class="floorContents" id="AssignmentsTableContainer"></div>
        </div>
    </div>
    <div class="staff-content" id="admin-staff">
        <div class="staff-overview">
            <div class="staff-overview-label">
                <h2>Staff Overview</h2>
                <p>Staff assignments and schedules by floor</p>
            </div>
            <div class="staff-floors" id="staffOverviewContainer">
                <!-- Staff overview will be loaded here dynamically -->
            </div>
        </div>
        
        <div class="staff-management">
            <div class="staff-management-label" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2>Staff Management</h2>
                    <p>List of all staff and their current workload</p>
                </div>
                <button id="create-shift-btn" style="background: maroon; color: white; border: none; border-radius: 4px; padding: 6px 12px; font-size: 0.875rem; font-weight: 600; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.1); transition: background 0.2s;">
                    + Create Shift
                </button>
            </div>
            <div class="staffContents" id="staffTableContainer"></div>
        </div>
    </div>
    
    <!-- Assignment History Section -->
    <div class="assignment-history-content" id="admin-assignment-history" style="display:none; padding:32px 32px 0 32px; min-height:calc(100vh - 80px); background:#fcf9f6;">
        <div style="background-color:white; border:1.5px solid var(--border-color); box-shadow:0 1px 2px 0 rgb(0 0 0 / 0.05); padding:32px 32px 40px 32px; border-radius:10px;">
            <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:8px; color:black;">Assignment History</h2>
            <div style="color:#666; font-size:0.87rem; margin-bottom:28px;">View all completed assignment records.</div>
            
            <!-- Filters -->
            <div style="display:flex; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:500; color:#555; font-size:0.9rem;">Staff Member</label>
                    <select id="history-staff-filter" style="padding:10px 14px; border-radius:6px; border:1px solid #ddd; font-size:0.95rem; background:#fff; cursor:pointer; min-width:200px;">
                        <option value="">All Staff</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:500; color:#555; font-size:0.9rem;">Room</label>
                    <input type="text" id="history-room-filter" placeholder="Room number..." style="padding:10px 14px; border-radius:6px; border:1px solid #ddd; font-size:0.95rem; width:150px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:500; color:#555; font-size:0.9rem;">Date From</label>
                    <input type="date" id="history-date-from" style="padding:10px 14px; border-radius:6px; border:1px solid #ddd; font-size:0.95rem;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:500; color:#555; font-size:0.9rem;">Date To</label>
                    <input type="date" id="history-date-to" style="padding:10px 14px; border-radius:6px; border:1px solid #ddd; font-size:0.95rem;">
                </div>
                <div style="display:flex; align-items:flex-end;">
                    <button id="history-filter-btn" style="background:#6a2323; color:white; border:none; border-radius:6px; padding:10px 20px; font-size:0.95rem; cursor:pointer; font-weight:500;">Apply Filters</button>
                </div>
            </div>
            
            <!-- History Table -->
            <div style="overflow-x:auto;">
                <table id="history-table" style="width:100%; border-collapse:collapse; font-size:0.95rem;">
                    <thead>
                        <tr style="background:#f8f8f8; border-bottom:2px solid #ddd;">
                            <th style="padding:12px; text-align:left; font-weight:600; color:#555;">Date</th>
                            <th style="padding:12px; text-align:left; font-weight:600; color:#555;">Room</th>
                            <th style="padding:12px; text-align:left; font-weight:600; color:#555;">Staff Member</th>
                            <th style="padding:12px; text-align:left; font-weight:600; color:#555;">Task</th>
                            <th style="padding:12px; text-align:left; font-weight:600; color:#555;">Status</th>
                            <th style="padding:12px; text-align:left; font-weight:600; color:#555;">Time Completed</th>
                            <th style="padding:12px; text-align:left; font-weight:600; color:#555;">Maintenance Issue</th>
                        </tr>
                    </thead>
                    <tbody id="history-table-body">
                        <tr><td colspan="7" style="padding:20px; text-align:center; color:#888;">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="reports-content" id="admin-reports" style="padding:32px 32px 0 32px; min-height:calc(100vh - 80px); background:#fcf9f6;">
        <div style="background-color:white; border:1.5px solid var(--border-color); box-shadow:0 1px 2px 0 rgb(0 0 0 / 0.05); padding:32px 32px 40px 32px; border-radius:10px;">
            <h2 style="font-size:2rem; font-weight:700; margin-bottom:8px; color:#6a2323;">Staff Performance Reports</h2>
            <div style="color:#666; font-size:1.08rem; margin-bottom:28px;">Select a staff member to view their performance data.</div>
            <?php
            // Fetch housekeepers list
            $staffList = [];
            $staffSql = "SELECT FullName FROM housekeepers ORDER BY FullName";
            $staffRes = $conn->query($staffSql);
            while ($row = $staffRes->fetch_assoc()) {
                $staffList[] = $row['FullName'];
            }
            ?>
            <div style="max-width:340px; margin-bottom:12px;">
                <select id="report-staff-select" style="width:100%; padding:12px 16px; border-radius:6px; border:1px solid #ccc; font-size:1.08rem; background:#fff; color:#6a2323; font-weight:500; cursor:pointer;">
                    <option value="">Select a Staff Member</option>
                    <?php foreach ($staffList as $staff): ?>
                        <option value="<?php echo htmlspecialchars($staff); ?>"><?php echo htmlspecialchars($staff); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <div id="staff-report-content"></div>
        </div>
    </div>

    <div class="maintenance-reports-content" id="admin-maintenance-reports" style="display:none; padding:32px 32px 0 32px; min-height:calc(100vh - 80px); background:#fcf9f6;">
        <div style="background-color:white; border:1.5px solid var(--border-color); box-shadow:0 1px 2px 0 rgb(0 0 0 / 0.05); padding:32px 32px 40px 32px; border-radius:10px;">
            <h2 style="font-size:2rem; font-weight:700; margin-bottom:8px; color:#6a2323;">Maintenance Reports</h2>
            <div style="color:#666; font-size:1.08rem; margin-bottom:28px;">View all maintenance requests reported by housekeeping staff.</div>
            
            <!-- Statistics -->
            <div id="maintenance-stats" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:32px;">
                <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; padding:20px;">
                    <div style="font-size:0.875rem; color:#0369a1; font-weight:600; margin-bottom:4px;">Total Requests</div>
                    <div id="stat-total" style="font-size:2rem; font-weight:700; color:#0c4a6e;">0</div>
                </div>
                <div style="background:#fef3c7; border:1px solid #fde047; border-radius:8px; padding:20px;">
                    <div style="font-size:0.875rem; color:#ca8a04; font-weight:600; margin-bottom:4px;">Open</div>
                    <div id="stat-open" style="font-size:2rem; font-weight:700; color:#a16207;">0</div>
                </div>
                <div style="background:#d1fae5; border:1px solid #6ee7b7; border-radius:8px; padding:20px;">
                    <div style="font-size:0.875rem; color:#047857; font-weight:600; margin-bottom:4px;">Resolved</div>
                    <div id="stat-resolved" style="font-size:2rem; font-weight:700; color:#065f46;">0</div>
                </div>
            </div>

            <!-- Requests Table -->
            <div id="maintenance-report-content">
                <p style="color:#888; text-align:center; padding:20px;">Loading maintenance reports...</p>
            </div>
        </div>
    </div>

    <div class="staff-list-content" id="admin-staff-list" style="display:none;">
        <div style="padding:32px; min-height:calc(100vh - 80px); background:#fcf9f6;">
            <div style="background-color:white; border:1.5px solid var(--border-color); box-shadow:0 1px 2px 0 rgb(0 0 0 / 0.05); padding:32px; border-radius:10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:8px; color:black;">Staff List</h2>
                        <p style="color:#666; font-size:0.87rem; margin:0;">Manage all staff members - add, edit, or remove</p>
                    </div>
                    <button id="add-staff-list-btn" style="background: maroon; color: white; border: none; border-radius: 4px; padding: 8px 16px; font-size: 0.9rem; font-weight: 600; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                        + Add Staff
                    </button>
                </div>
                <div id="staff-list-container"></div>
            </div>
        </div>
    </div>

    <div class="room-list-content" id="admin-room-list" style="display:none;">
        <div style="padding:32px; min-height:calc(100vh - 80px); background:#fcf9f6;">
            <div style="background-color:white; border:1.5px solid var(--border-color); box-shadow:0 1px 2px 0 rgb(0 0 0 / 0.05); padding:32px; border-radius:10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:8px; color:black;">Room List</h2>
                        <p style="color:#666; font-size:0.87rem; margin:0;">View all rooms with their details</p>
                    </div>
                </div>
                <div id="room-list-container"></div>
            </div>
        </div>
    </div>

    <div id="assign-overlay"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
                background: rgba(0,0,0,0.3);
                z-index:9998;">
    </div>

    <div id="assign-editor"
        style="
            display:none; 
            position:fixed; 
            left:50%; 
            top:50%;
            transform:translate(-50%, -50%);
            border:1px solid #e0e0e0; 
            background:white; 
            padding:24px 20px 16px 20px;
            border-radius:10px; 
            box-shadow:0 4px 16px rgba(0,0,0,0.10);
            z-index:9999; 
            min-width:260px;
            max-width:340px;
        ">
        <div class="staff-on-floor" style="margin-bottom:18px;">
            <div style="font-weight:600; font-size:1.08rem; margin-bottom:2px;">Staff on this floor</div>
            <div style="font-size:0.93rem; color:#888; margin-bottom:8px;">
                Select a staff member to handle the cleaning task.
            </div>
            <ul style="list-style:none; padding:0; margin:0 0 8px 0;"></ul>
        </div>
        <hr style="border:none; border-top:1px solid #eee; margin:10px 0;">
        <div class="staff-other" style="margin-bottom:14px;">
            <div style="font-weight:500; font-size:1rem; margin-bottom:4px;">Other staff</div>
            <ul style="list-style:none; padding:0; margin:0;"></ul>
        </div>
        <button id="assign-confirm"
            style="
                width:100%; 
                background:var(--primary-color, maroon); 
                color:white; 
                border:none; 
                border-radius:6px; 
                padding:10px 0; 
                font-size:1rem; 
                font-weight:600; 
                cursor:pointer;
                box-shadow:0 1px 4px rgba(0,0,0,0.06);
                transition:background 0.2s;
            "
        >Assign</button>
    </div>

    <!-- Create Shift Modal -->
    <div id="create-shift-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
        <div id="create-shift-modal" style="background:white; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,0.15); width:90%; max-width:500px; max-height:90vh; overflow-y:auto;">
            <div style="padding:24px; border-bottom:1px solid #eee;">
                <h2 style="margin:0; color:#2c5282; font-size:1.5rem;">Create New Shift</h2>
            </div>
            <form id="create-shift-form" style="padding:24px;">
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Start Day *</label>
                    <select name="startDay" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">End Day *</label>
                    <select name="endDay" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday" selected>Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Start Time *</label>
                    <select name="startTime" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                        <option value="12:00 AM">12:00 AM</option>
                        <option value="01:00 AM">01:00 AM</option>
                        <option value="02:00 AM">02:00 AM</option>
                        <option value="03:00 AM">03:00 AM</option>
                        <option value="04:00 AM">04:00 AM</option>
                        <option value="05:00 AM">05:00 AM</option>
                        <option value="06:00 AM">06:00 AM</option>
                        <option value="07:00 AM">07:00 AM</option>
                        <option value="08:00 AM" selected>08:00 AM</option>
                        <option value="09:00 AM">09:00 AM</option>
                        <option value="10:00 AM">10:00 AM</option>
                        <option value="11:00 AM">11:00 AM</option>
                        <option value="12:00 PM">12:00 PM</option>
                        <option value="01:00 PM">01:00 PM</option>
                        <option value="02:00 PM">02:00 PM</option>
                        <option value="03:00 PM">03:00 PM</option>
                        <option value="04:00 PM">04:00 PM</option>
                        <option value="05:00 PM">05:00 PM</option>
                        <option value="06:00 PM">06:00 PM</option>
                        <option value="07:00 PM">07:00 PM</option>
                        <option value="08:00 PM">08:00 PM</option>
                        <option value="09:00 PM">09:00 PM</option>
                        <option value="10:00 PM">10:00 PM</option>
                        <option value="11:00 PM">11:00 PM</option>
                    </select>
                </div>
                <div style="margin-bottom:24px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">End Time *</label>
                    <select name="endTime" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                        <option value="01:00 AM">01:00 AM</option>
                        <option value="02:00 AM">02:00 AM</option>
                        <option value="03:00 AM">03:00 AM</option>
                        <option value="04:00 AM">04:00 AM</option>
                        <option value="05:00 AM">05:00 AM</option>
                        <option value="06:00 AM">06:00 AM</option>
                        <option value="07:00 AM">07:00 AM</option>
                        <option value="08:00 AM">08:00 AM</option>
                        <option value="09:00 AM">09:00 AM</option>
                        <option value="10:00 AM">10:00 AM</option>
                        <option value="11:00 AM">11:00 AM</option>
                        <option value="12:00 PM">12:00 PM</option>
                        <option value="01:00 PM">01:00 PM</option>
                        <option value="02:00 PM">02:00 PM</option>
                        <option value="03:00 PM">03:00 PM</option>
                        <option value="04:00 PM">04:00 PM</option>
                        <option value="05:00 PM" selected>05:00 PM</option>
                        <option value="06:00 PM">06:00 PM</option>
                        <option value="07:00 PM">07:00 PM</option>
                        <option value="08:00 PM">08:00 PM</option>
                        <option value="09:00 PM">09:00 PM</option>
                        <option value="10:00 PM">10:00 PM</option>
                        <option value="11:00 PM">11:00 PM</option>
                        <option value="12:00 AM">12:00 AM</option>
                    </select>
                </div>
                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" id="cancel-create-shift" style="padding:10px 24px; border:1px solid #ddd; background:white; color:#666; border-radius:6px; font-size:1rem; cursor:pointer;">
                        Cancel
                    </button>
                    <button type="submit" style="padding:10px 24px; border:none; background:#2c5282; color:white; border-radius:6px; font-size:1rem; font-weight:600; cursor:pointer;">
                        Create Shift
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Room Modal -->
    <div id="add-room-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
        <div id="add-room-modal" style="background:white; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,0.15); width:90%; max-width:500px; max-height:90vh; overflow-y:auto;">
            <div style="padding:24px; border-bottom:1px solid #eee;">
                <h2 style="margin:0; color:black; font-size:1.5rem;">Add New Room</h2>
            </div>
            <form id="add-room-form" style="padding:24px;">
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Room Number *</label>
                    <input type="text" name="roomNumber" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;" placeholder="Enter Room Number">
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Room Type *</label>
                    <select name="roomType" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                        <option value="">Select Room Type</option>
                        <option value="Standard">Standard</option>
                        <option value="Deluxe">Deluxe</option>
                        <option value="Suite">Suite</option>
                        <option value="VIP">VIP</option>
                    </select>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Floor *</label>
                    <input type="number" name="floor" required min="1" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;" placeholder="Enter Floor Number">
                </div>
                <div style="margin-bottom:24px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Status *</label>
                    <select name="status" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                        <option value="Clean" selected>Clean</option>
                        <option value="Dirty">Dirty</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" id="cancel-add-room" style="padding:10px 24px; border:1px solid #ddd; background:white; color:#666; border-radius:6px; font-size:1rem; cursor:pointer;">
                        Cancel
                    </button>
                    <button type="submit" style="padding:10px 24px; border:none; background:maroon; color:white; border-radius:6px; font-size:1rem; font-weight:600; cursor:pointer;">
                        Add Room
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <div id="add-staff-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
        <div id="add-staff-modal" style="background:white; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,0.15); width:90%; max-width:500px; max-height:90vh; overflow-y:auto;">
            <div style="padding:24px; border-bottom:1px solid #eee;">
                <h2 style="margin:0; color:black; font-size:1.5rem;">Add New Staff Member</h2>
            </div>
            <form id="add-staff-form" style="padding:24px;">
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Full Name *</label>
                    <input type="text" name="fullName" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;" placeholder="Enter Full Name">
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Phone</label>
                    <input type="tel" name="phone" maxlength="11" pattern="[0-9]{11}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;" placeholder="0912-345-6789">
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Email</label>
                    <input type="email" name="email" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;" placeholder="staff@example.com">
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">UUID</label>
                    <div style="display:flex; gap:8px;">
                        <input type="text" name="uuid" readonly style="flex:1; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem; background:#f9f9f9;" placeholder="Click Generate">
                        <button type="button" id="generate-uuid-btn" style="padding:10px 20px; border:none; background:#2c5282; color:white; border-radius:6px; font-size:0.9rem; font-weight:600; cursor:pointer; white-space:nowrap;">
                            Generate
                        </button>
                    </div>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Assigned Floor</label>
                    <select name="assignedFloor" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                        <option value="">Not Assigned</option>
                        <?php
                        // Get distinct floors from rooms table
                        $floorResult = $connTasks->query("SELECT DISTINCT floor FROM roomslunera_hotel.rooms ORDER BY floor");
                        while ($floorRow = $floorResult->fetch_assoc()) {
                            $floorNum = (int) $floorRow['floor'];
                            echo '<option value="' . $floorNum . '">Floor ' . $floorNum . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div style="margin-bottom:24px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Hire Date</label>
                    <input type="date" name="hireDate" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                </div>
                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" id="cancel-add-staff" style="padding:10px 24px; border:1px solid #ddd; background:white; color:#666; border-radius:6px; font-size:1rem; cursor:pointer;">
                        Cancel
                    </button>
                    <button type="submit" style="padding:10px 24px; border:none; background:maroon; color:white; border-radius:6px; font-size:1rem; font-weight:600; cursor:pointer;">
                        Add Staff Member
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Room Modal -->
    <div id="edit-room-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
        <div style="background:white; padding:32px; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.3); min-width:400px; max-width:500px;">
            <h3 style="margin-top:0; margin-bottom:24px; color:#6a2323;">Edit Room</h3>
            <form id="edit-room-form">
                <input type="hidden" id="edit-room-id">
                <div style="margin-bottom:18px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Room Number</label>
                    <input type="text" id="edit-room-number" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                </div>
                <div style="margin-bottom:18px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Floor</label>
                    <input type="number" id="edit-floor" required min="1" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                </div>
                <div style="margin-bottom:18px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Room Type</label>
                    <select id="edit-room-type" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                        <option value="Standard">Standard</option>
                        <option value="Deluxe">Deluxe</option>
                        <option value="Suite">Suite</option>
                    </select>
                </div>
                <div style="margin-bottom:24px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Status</label>
                    <select id="edit-status" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1rem;">
                        <option value="Dirty">Dirty</option>
                        <option value="Clean">Clean</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" id="cancel-edit-room" style="padding:10px 24px; border:1px solid #ddd; background:white; color:#666; border-radius:6px; font-size:1rem; cursor:pointer;">
                        Cancel
                    </button>
                    <button type="submit" style="padding:10px 24px; border:none; background:#3498db; color:white; border-radius:6px; font-size:1rem; font-weight:600; cursor:pointer;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="scripts/update-database.js"></script>
    <script src="scripts/fetch-Staff_table.js"></script>
    <script src="scripts/fetch-staff-overview.js"></script>
    <script src="scripts/staff-list.js"></script>
    <script src="scripts/room-list.js"></script>
    <script src="scripts/assign-staff.js"></script>
    <script src="scripts/fetch-Assignments_table.js"></script>
    <script src="scripts/fetch-Overview_table.js"></script>
    <script src="scripts/maintenance-reports.js"></script>
    <script src="scripts/assignment-history.js"></script>
    <script src="scripts/leftNav-selection.js"></script>
    <script>
    // Staff report loader for raw text report
    const select = document.getElementById('report-staff-select');
    select.addEventListener('change', function() {
        const content = document.getElementById('staff-report-content');
        if (this.value) {
            content.innerHTML = '<div style="color:#888;">Loading...</div>';
            fetch('includes/fetch-staff-report.php?staff=' + encodeURIComponent(this.value))
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        content.innerHTML = `<div style="color:red;">Error: ${data.error}</div>`;
                    } else if (data.name && data.report) {
                        content.innerHTML =
                            `<div style='font-weight:600; font-size:1.13rem; margin-bottom:8px;'>Raw Performance Data for ${data.name}</div>` +
                            `<textarea readonly style='width:100%; min-height:120px; font-family:monospace; font-size:1rem; border-radius:8px; border:1.5px solid var(--border-color); background:#fcfcfc; padding:16px 12px; resize:vertical;'>${data.report}</textarea>`;
                    } else {
                        content.innerHTML = '<div style="color:red;">Invalid data received from server.</div>';
                    }
                })
                .catch((err) => {
                    console.error('Fetch error:', err);
                    content.innerHTML = '<div style="color:red;">Failed to load report.</div>';
                });
        } else {
            content.innerHTML = '';
        }
    });
    </script>
    <script>
    // Burger icon dropdown logic
    const burgerIcon = document.getElementById('burger-icon');
    const burgerDropdown = document.getElementById('burger-dropdown');
    burgerIcon.addEventListener('click', function(e) {
        e.preventDefault();
        burgerDropdown.style.display = burgerDropdown.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', function(e) {
        if (!burgerIcon.contains(e.target) && !burgerDropdown.contains(e.target)) {
            burgerDropdown.style.display = 'none';
        }
    });
    </script>
    <script>
    // Add Staff Modal Logic
    const addStaffOverlay = document.getElementById('add-staff-overlay');
    const addStaffForm = document.getElementById('add-staff-form');
    const cancelAddStaff = document.getElementById('cancel-add-staff');
    const generateUuidBtn = document.getElementById('generate-uuid-btn');

    // Generate UUID function
    generateUuidBtn.addEventListener('click', function() {
        // Only show confirmation if editing (form has editId)
        if (addStaffForm.dataset.editId) {
            if (!confirm('Generate a new UUID? This will replace the existing UUID.')) {
                return;
            }
        }
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let uuid = '';
        for (let i = 0; i < 12; i++) {
            uuid += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.querySelector('#add-staff-form input[name="uuid"]').value = uuid;
    });

    cancelAddStaff.addEventListener('click', function() {
        addStaffOverlay.style.display = 'none';
    });

    addStaffOverlay.addEventListener('click', function(e) {
        if (e.target === addStaffOverlay) {
            addStaffOverlay.style.display = 'none';
        }
    });

    addStaffForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(addStaffForm);
        
        // Add editId if editing
        if (addStaffForm.dataset.editId) {
            formData.append('editId', addStaffForm.dataset.editId);
        }
        
        fetch('includes/add-staff.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Staff member saved successfully!');
                addStaffOverlay.style.display = 'none';
                addStaffForm.reset();
                delete addStaffForm.dataset.editId;
                
                // Reload appropriate view
                if (typeof loadStaffList === 'function') {
                    loadStaffList();
                }
                if (typeof loadStaffTable === 'function') {
                    loadStaffTable();
                }
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
        });
    });

    // Create Shift Modal Logic
    const createShiftBtn = document.getElementById('create-shift-btn');
    const createShiftOverlay = document.getElementById('create-shift-overlay');
    const createShiftForm = document.getElementById('create-shift-form');
    const cancelCreateShift = document.getElementById('cancel-create-shift');

    createShiftBtn.addEventListener('click', function() {
        createShiftOverlay.style.display = 'flex';
        createShiftForm.reset();
    });

    cancelCreateShift.addEventListener('click', function() {
        createShiftOverlay.style.display = 'none';
    });

    createShiftOverlay.addEventListener('click', function(e) {
        if (e.target === createShiftOverlay) {
            createShiftOverlay.style.display = 'none';
        }
    });

    createShiftForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(createShiftForm);
        
        fetch('includes/create-shift.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Shift created successfully! It will now appear in the schedule dropdowns.');
                createShiftOverlay.style.display = 'none';
                createShiftForm.reset();
                // Reload staff table to refresh shift dropdowns in real-time
                if (typeof loadStaffTable === 'function') {
                    loadStaffTable();
                }
            } else {
                alert('Error creating shift: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
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
                            
                            // Severity colors for admin
                            let severityColor = 'maroon';
                            if (notif.Severity === 'warning') severityColor = '#f59e0b';
                            if (notif.Severity === 'critical') severityColor = '#ef4444';
                            if (notif.Severity === 'info') severityColor = '#3b82f6';
                            
                            html += `<div class="notification-item" data-id="${notif.NotificationID}" style="padding:12px 20px; border-bottom:1px solid #f0f0f0; cursor:pointer; background:${bgColor}; transition:background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='${bgColor}'">
                                <div style="display:flex; align-items:start; gap:8px;">
                                    <div style="width:8px; height:8px; border-radius:50%; background:${severityColor}; margin-top:6px; ${isRead ? 'opacity:0.3;' : ''}"></div>
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
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
</body>
</html>