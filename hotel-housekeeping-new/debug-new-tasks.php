<?php
require("includes/database.php");

echo "<h2>Debug New Task Creation</h2>";

// Show latest tasks
echo "<h3>Latest Tasks in roomslunera_hotel.tasks:</h3>";
$tasksResult = $connTasks->query("SELECT t.*, r.room_number, r.floor 
                                   FROM tasks t 
                                   LEFT JOIN roomslunera_hotel.rooms r ON t.RoomID = r.id 
                                   ORDER BY t.TaskID DESC LIMIT 10");
echo "<table border='1' style='border-collapse:collapse;'>";
echo "<tr><th>TaskID</th><th>Description</th><th>RoomID</th><th>Room Number</th><th>Floor</th></tr>";
while ($row = $tasksResult->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['TaskID']}</td>";
    echo "<td>{$row['Description']}</td>";
    echo "<td>{$row['RoomID']}</td>";
    echo "<td>" . ($row['room_number'] ?? 'NOT FOUND') . "</td>";
    echo "<td>" . ($row['floor'] ?? 'N/A') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Show latest assignments
echo "<h3>Latest Assignments in webdb.assignments:</h3>";
$assignResult = $conn->query("SELECT a.*, h.FullName 
                               FROM assignments a 
                               LEFT JOIN housekeepers h ON a.HousekeeperID = h.HousekeeperID 
                               ORDER BY a.AssignmentID DESC LIMIT 10");
echo "<table border='1' style='border-collapse:collapse;'>";
echo "<tr><th>AssignmentID</th><th>TaskID</th><th>Status</th><th>HousekeeperID</th><th>Staff Name</th></tr>";
while ($row = $assignResult->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['AssignmentID']}</td>";
    echo "<td>{$row['TaskID']}</td>";
    echo "<td>{$row['Status']}</td>";
    echo "<td>" . ($row['HousekeeperID'] ?? 'NULL') . "</td>";
    echo "<td>" . ($row['FullName'] ?? 'Unassigned') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Show the JOIN result (what actually appears)
echo "<h3>Joined Result (What Shows on Website):</h3>";
$joinQuery = "SELECT a.AssignmentID, a.Status as AssignmentStatus, t.TaskID, t.Description, 
              r.room_number as RoomNumber, r.floor as Floor, h.FullName
              FROM webdb.assignments a
              JOIN roomslunera_hotel.tasks t ON a.TaskID = t.TaskID
              JOIN roomslunera_hotel.rooms r ON t.RoomID = r.id
              LEFT JOIN webdb.housekeepers h ON a.HousekeeperID = h.HousekeeperID
              WHERE a.Status != 'Completed'
              ORDER BY a.AssignmentID DESC LIMIT 10";
$joinResult = $conn->query($joinQuery);

if ($joinResult->num_rows == 0) {
    echo "<p style='color:red;'><strong>NO RESULTS - This is why assignments don't appear!</strong></p>";
    
    // Diagnose the issue
    echo "<h4>Diagnosis:</h4>";
    $orphanedCheck = $conn->query("SELECT a.AssignmentID, a.TaskID, 
                                    (SELECT COUNT(*) FROM roomslunera_hotel.tasks t WHERE t.TaskID = a.TaskID) as TaskExists
                                    FROM webdb.assignments a 
                                    WHERE a.Status != 'Completed'");
    echo "<ul>";
    while ($row = $orphanedCheck->fetch_assoc()) {
        if ($row['TaskExists'] == 0) {
            echo "<li style='color:red;'>Assignment {$row['AssignmentID']} references TaskID {$row['TaskID']} which DOES NOT EXIST in roomslunera_hotel.tasks</li>";
        } else {
            echo "<li style='color:green;'>Assignment {$row['AssignmentID']} references TaskID {$row['TaskID']} ✓</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<table border='1' style='border-collapse:collapse;'>";
    echo "<tr><th>AssignmentID</th><th>TaskID</th><th>Description</th><th>Room</th><th>Floor</th><th>Staff</th><th>Status</th></tr>";
    while ($row = $joinResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['AssignmentID']}</td>";
        echo "<td>{$row['TaskID']}</td>";
        echo "<td>{$row['Description']}</td>";
        echo "<td>{$row['RoomNumber']}</td>";
        echo "<td>{$row['Floor']}</td>";
        echo "<td>" . ($row['FullName'] ?? 'Unassigned') . "</td>";
        echo "<td>{$row['AssignmentStatus']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p style='color:green;'><strong>✓ These assignments should appear on the website</strong></p>";
}
?>
<br><br>
<a href="admin-dashboard.php" style="padding:10px 20px; background:maroon; color:white; text-decoration:none; border-radius:4px;">Go to Admin Dashboard</a>
