<?php
require("includes/database.php");

echo "<h2>Debug: Why Assignments Don't Appear</h2>";

// Test 1: Check assignments table
echo "<h3>Test 1: Assignments in webdb.assignments</h3>";
$result = $conn->query("SELECT * FROM webdb.assignments WHERE Status != 'Completed'");
echo "<p>Active assignments found: <strong>" . $result->num_rows . "</strong></p>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>AssignmentID</th><th>HousekeeperID</th><th>TaskID</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['AssignmentID']}</td><td>{$row['HousekeeperID']}</td><td>{$row['TaskID']}</td><td>{$row['Status']}</td></tr>";
    }
    echo "</table>";
}

// Test 2: Check tasks in roomslunera_hotel
echo "<h3>Test 2: Tasks in roomslunera_hotel.tasks</h3>";
$result = $connTasks->query("SELECT * FROM tasks");
echo "<p>Tasks found: <strong>" . $result->num_rows . "</strong></p>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>TaskID</th><th>Description</th><th>RoomID</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['TaskID']}</td><td>{$row['Description']}</td><td>{$row['RoomID']}</td></tr>";
    }
    echo "</table>";
}

// Test 3: Check rooms in roomslunera_hotel
echo "<h3>Test 3: Rooms in roomslunera_hotel.rooms</h3>";
$result = $connTasks->query("SELECT * FROM rooms LIMIT 10");
echo "<p>Rooms found: <strong>" . $result->num_rows . "</strong></p>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>id</th><th>room_number</th><th>floor</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['room_number']}</td><td>{$row['floor']}</td></tr>";
    }
    echo "</table>";
}

// Test 4: The actual JOIN query used in fetch-assignmentsTable.php
echo "<h3>Test 4: Cross-Database JOIN Query (What admin dashboard uses)</h3>";
$floor = 1; // Test with floor 1
$query = "SELECT a.AssignmentID, h.FullName, r.room_number AS RoomNumber, 
          t.Description AS TaskDescription, a.Status, a.TimeCompleted,
          r.floor AS Floor
          FROM webdb.assignments a
          JOIN roomslunera_hotel.tasks t ON a.TaskID = t.TaskID
          JOIN roomslunera_hotel.rooms r ON t.RoomID = r.id
          LEFT JOIN webdb.housekeepers h ON a.HousekeeperID = h.HousekeeperID
          WHERE a.Status != 'Completed' AND r.floor = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $floor);
$stmt->execute();
$result = $stmt->get_result();

echo "<p>Floor {$floor} assignments from JOIN: <strong>" . $result->num_rows . "</strong></p>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>AssignmentID</th><th>Housekeeper</th><th>Room</th><th>Task</th><th>Floor</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['AssignmentID']}</td>";
        echo "<td>{$row['FullName']}</td>";
        echo "<td>{$row['RoomNumber']}</td>";
        echo "<td>{$row['TaskDescription']}</td>";
        echo "<td>{$row['Floor']}</td>";
        echo "<td>{$row['Status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>❌ No results from JOIN query!</p>";
    
    // Debug: Check which assignments have matching tasks
    echo "<h4>Debug: Which assignments have matching tasks?</h4>";
    $result = $conn->query("SELECT a.AssignmentID, a.TaskID, 
                            (SELECT COUNT(*) FROM roomslunera_hotel.tasks t WHERE t.TaskID = a.TaskID) as task_exists
                            FROM webdb.assignments a 
                            WHERE a.Status != 'Completed'");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>AssignmentID</th><th>TaskID</th><th>Task Exists in roomslunera_hotel?</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $color = $row['task_exists'] ? 'green' : 'red';
        echo "<tr style='color:{$color};'>";
        echo "<td>{$row['AssignmentID']}</td>";
        echo "<td>{$row['TaskID']}</td>";
        echo "<td>" . ($row['task_exists'] ? '✓ Yes' : '✗ No - MISSING!') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<br><br><a href='admin-dashboard.php'>Back to Dashboard</a>";
?>
