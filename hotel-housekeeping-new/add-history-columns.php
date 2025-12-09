<?php
require("includes/database.php");

echo "<h2>Add Assignment History Columns</h2>";
echo "<p>Adding columns to store room/task info for completed assignments</p>";

// Check if columns exist
$checkQuery = "SHOW COLUMNS FROM webdb.assignments LIKE 'RoomNumber'";
$result = $conn->query($checkQuery);

if ($result->num_rows == 0) {
    echo "<h3>Adding new columns...</h3>";
    
    // Add RoomNumber column
    $sql1 = "ALTER TABLE webdb.assignments ADD COLUMN RoomNumber VARCHAR(10) NULL AFTER TaskID";
    if ($conn->query($sql1)) {
        echo "<p style='color:green;'>✓ Added RoomNumber column</p>";
    } else {
        echo "<p style='color:red;'>✗ Error adding RoomNumber: " . $conn->error . "</p>";
    }
    
    // Add TaskDescription column
    $sql2 = "ALTER TABLE webdb.assignments ADD COLUMN TaskDescription VARCHAR(255) NULL AFTER RoomNumber";
    if ($conn->query($sql2)) {
        echo "<p style='color:green;'>✓ Added TaskDescription column</p>";
    } else {
        echo "<p style='color:red;'>✗ Error adding TaskDescription: " . $conn->error . "</p>";
    }
    
    echo "<br><p style='color:green; font-weight:bold;'>✓ Columns added successfully!</p>";
    echo "<p>Now updating existing assignments with room/task data...</p>";
    
    // Update existing assignments with room/task info from tasks table
    $updateSql = "UPDATE webdb.assignments a
                  JOIN roomslunera_hotel.tasks t ON a.TaskID = t.TaskID
                  JOIN roomslunera_hotel.rooms r ON t.RoomID = r.id
                  SET a.RoomNumber = r.room_number,
                      a.TaskDescription = t.Description
                  WHERE a.RoomNumber IS NULL";
    
    if ($conn->query($updateSql)) {
        $affected = $conn->affected_rows;
        echo "<p style='color:green;'>✓ Updated {$affected} existing assignments with room/task data</p>";
    } else {
        echo "<p style='color:orange;'>⚠ Could not update existing assignments: " . $conn->error . "</p>";
    }
    
} else {
    echo "<p style='color:green;'>✓ Columns already exist!</p>";
}

echo "<br><a href='admin-dashboard.php' style='padding:10px 20px; background:maroon; color:white; text-decoration:none; border-radius:4px;'>Go to Dashboard</a>";
?>
