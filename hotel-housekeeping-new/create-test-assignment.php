<?php
require("includes/database.php");

echo "<h2>Create Test Assignment</h2>";

// Get available rooms from roomslunera_hotel
$rooms = $connTasks->query("SELECT id, room_number, floor FROM rooms ORDER BY floor, room_number");

echo "<form method='post' style='max-width:400px;'>";
echo "<div style='margin-bottom:15px;'>";
echo "<label>Room:</label><br>";
echo "<select name='roomId' required style='width:100%; padding:8px; margin-top:5px;'>";
while ($room = $rooms->fetch_assoc()) {
    echo "<option value='{$room['id']}'>Room {$room['room_number']} (Floor {$room['floor']})</option>";
}
echo "</select>";
echo "</div>";

echo "<div style='margin-bottom:15px;'>";
echo "<label>Task Description:</label><br>";
echo "<input type='text' name='description' value='Deep Cleaning' required style='width:100%; padding:8px; margin-top:5px;'>";
echo "</div>";

// Get housekeepers
$housekeepers = $conn->query("SELECT HousekeeperID, FullName FROM housekeepers");
echo "<div style='margin-bottom:15px;'>";
echo "<label>Assign to Staff (optional):</label><br>";
echo "<select name='housekeeperId' style='width:100%; padding:8px; margin-top:5px;'>";
echo "<option value=''>-- Unassigned --</option>";
while ($housekeeper = $housekeepers->fetch_assoc()) {
    echo "<option value='{$housekeeper['HousekeeperID']}'>{$housekeeper['FullName']}</option>";
}
echo "</select>";
echo "</div>";

echo "<button type='submit' name='create' style='padding:10px 20px; background:maroon; color:white; border:none; border-radius:4px; cursor:pointer;'>Create Assignment</button>";
echo "</form>";

if (isset($_POST['create'])) {
    $roomId = $_POST['roomId'];
    $description = $_POST['description'];
    $housekeeperId = !empty($_POST['housekeeperId']) ? $_POST['housekeeperId'] : null;
    
    echo "<hr><h3>Creating Assignment...</h3>";
    
    // Step 1: Create task in roomslunera_hotel
    $stmt = $connTasks->prepare("INSERT INTO tasks (Description, RoomID) VALUES (?, ?)");
    $stmt->bind_param('si', $description, $roomId);
    
    if ($stmt->execute()) {
        $taskId = $connTasks->insert_id;
        echo "<p style='color:green;'>✓ Task created in roomslunera_hotel.tasks (TaskID: {$taskId})</p>";
        $stmt->close();
        
        // Step 2: Create assignment in webdb
        if ($housekeeperId) {
            $stmt = $conn->prepare("INSERT INTO assignments (HousekeeperID, TaskID, AssignedDate, Status) VALUES (?, ?, CURDATE(), 'Pending')");
            $stmt->bind_param('ii', $housekeeperId, $taskId);
        } else {
            $stmt = $conn->prepare("INSERT INTO assignments (HousekeeperID, TaskID, AssignedDate, Status) VALUES (NULL, ?, CURDATE(), 'Pending')");
            $stmt->bind_param('i', $taskId);
        }
        
        if ($stmt->execute()) {
            $assignmentId = $conn->insert_id;
            echo "<p style='color:green;'>✓ Assignment created in webdb.assignments (AssignmentID: {$assignmentId})</p>";
            $stmt->close();
            
            // Step 3: Test the JOIN query
            echo "<h3>Testing Cross-Database JOIN:</h3>";
            $testQuery = "SELECT a.AssignmentID, h.FullName, r.room_number AS RoomNumber, 
                          t.Description AS TaskDescription, a.Status, r.floor AS Floor
                          FROM webdb.assignments a
                          JOIN roomslunera_hotel.tasks t ON a.TaskID = t.TaskID
                          JOIN roomslunera_hotel.rooms r ON t.RoomID = r.id
                          LEFT JOIN webdb.housekeepers h ON a.HousekeeperID = h.HousekeeperID
                          WHERE a.AssignmentID = ?";
            
            $stmt = $conn->prepare($testQuery);
            $stmt->bind_param('i', $assignmentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<p style='color:green;'>✓ JOIN query successful!</p>";
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>Assignment ID</th><th>Room</th><th>Floor</th><th>Task</th><th>Staff</th><th>Status</th></tr>";
                echo "<tr>";
                echo "<td>{$row['AssignmentID']}</td>";
                echo "<td>{$row['RoomNumber']}</td>";
                echo "<td>{$row['Floor']}</td>";
                echo "<td>{$row['TaskDescription']}</td>";
                echo "<td>" . ($row['FullName'] ?: 'Unassigned') . "</td>";
                echo "<td>{$row['Status']}</td>";
                echo "</tr>";
                echo "</table>";
                
                echo "<p style='color:green; font-weight:bold; margin-top:20px;'>🎉 SUCCESS! Assignment created and visible through JOIN query.</p>";
                echo "<p><a href='admin-dashboard.php' style='padding:10px 20px; background:maroon; color:white; text-decoration:none; border-radius:4px;'>Go to Admin Dashboard</a></p>";
            } else {
                echo "<p style='color:red;'>✗ JOIN query returned no results - something is wrong!</p>";
            }
            $stmt->close();
            
        } else {
            echo "<p style='color:red;'>✗ Failed to create assignment: " . $conn->error . "</p>";
        }
        
    } else {
        echo "<p style='color:red;'>✗ Failed to create task: " . $connTasks->error . "</p>";
        $stmt->close();
    }
}
?>
