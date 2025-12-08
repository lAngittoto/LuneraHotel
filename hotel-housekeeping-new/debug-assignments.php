<?php
require("includes/database.php");

echo "<h2>Debug Assignments</h2>";

// Check tasks table
echo "<h3>Tasks in roomslunera_hotel.tasks:</h3>";
$tasksResult = $connTasks->query("SELECT * FROM tasks");
echo "Count: " . $tasksResult->num_rows . "<br>";
while ($row = $tasksResult->fetch_assoc()) {
    echo "TaskID: {$row['TaskID']}, Description: {$row['Description']}, RoomID: {$row['RoomID']}<br>";
}

// Check assignments table
echo "<h3>Assignments in webdb.assignments:</h3>";
$assignResult = $conn->query("SELECT * FROM assignments WHERE Status != 'Completed'");
echo "Count: " . $assignResult->num_rows . "<br>";
while ($row = $assignResult->fetch_assoc()) {
    echo "AssignmentID: {$row['AssignmentID']}, TaskID: {$row['TaskID']}, Status: {$row['Status']}, HousekeeperID: {$row['HousekeeperID']}<br>";
}

// Check rooms table
echo "<h3>Rooms in roomslunera_hotel.rooms:</h3>";
$roomsResult = $connTasks->query("SELECT id, room_number, floor FROM rooms LIMIT 5");
echo "Count: " . $roomsResult->num_rows . "<br>";
while ($row = $roomsResult->fetch_assoc()) {
    echo "ID: {$row['id']}, RoomNumber: {$row['room_number']}, Floor: {$row['floor']}<br>";
}

// Test the full query
echo "<h3>Full Assignment Query:</h3>";
$taskQuery = "SELECT a.AssignmentID, a.Status as AssignmentStatus, t.TaskID, t.Description, r.room_number as RoomNumber, r.floor as Floor, r.status as RoomStatus, h.FullName
              FROM webdb.assignments a
              JOIN roomslunera_hotel.tasks t ON a.TaskID = t.TaskID
              JOIN roomslunera_hotel.rooms r ON t.RoomID = r.id
              LEFT JOIN webdb.housekeepers h ON a.HousekeeperID = h.HousekeeperID
              WHERE a.Status != 'Completed'
              ORDER BY r.floor, r.room_number";
$result = $conn->query($taskQuery);
if (!$result) {
    echo "ERROR: " . $conn->error . "<br>";
} else {
    echo "Count: " . $result->num_rows . "<br>";
    while ($row = $result->fetch_assoc()) {
        echo "Assignment {$row['AssignmentID']}: Room {$row['RoomNumber']}, Task: {$row['Description']}, Status: {$row['AssignmentStatus']}<br>";
    }
}
?>
