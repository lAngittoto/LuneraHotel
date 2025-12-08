<?php
require("includes/database.php");

echo "<h2>Remove Foreign Key Constraint (Better Solution)</h2>";
echo "<p>This allows assignments to reference tasks in a different database without duplicating data.</p>";

// Check and remove foreign key on assignments.TaskID
$fkQuery = "SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = 'webdb'
            AND TABLE_NAME = 'assignments'
            AND COLUMN_NAME = 'TaskID'
            AND REFERENCED_TABLE_NAME IS NOT NULL";

$result = $conn->query($fkQuery);

if ($result->num_rows > 0) {
    echo "<h3>Removing Foreign Key Constraint:</h3>";
    while ($row = $result->fetch_assoc()) {
        $constraintName = $row['CONSTRAINT_NAME'];
        $dropSQL = "ALTER TABLE webdb.assignments DROP FOREIGN KEY `{$constraintName}`";
        
        if ($conn->query($dropSQL)) {
            echo "<p style='color:green;'>✓ Removed foreign key constraint: {$constraintName}</p>";
        } else {
            echo "<p style='color:red;'>✗ Failed: " . $conn->error . "</p>";
        }
    }
    echo "<p style='color:green;'><strong>✓ Done! Assignments can now reference tasks from roomslunera_hotel database.</strong></p>";
} else {
    echo "<p style='color:green;'>✓ No foreign key constraint found. System is already configured correctly.</p>";
}

// Check if webdb.tasks table exists (we don't need it anymore)
$tasksCheck = $conn->query("SHOW TABLES LIKE 'tasks'");
if ($tasksCheck->num_rows > 0) {
    echo "<hr><h3>Optional: webdb.tasks table exists</h3>";
    echo "<p>Since tasks are now stored in roomslunera_hotel.tasks, you can optionally drop webdb.tasks table.</p>";
    echo "<p style='color:orange;'>⚠️ Only do this if you're sure all data is in roomslunera_hotel.tasks</p>";
}

echo "<hr>";
echo "<h3>Architecture Summary:</h3>";
echo "<ul>";
echo "<li>✓ Tasks stored in: <strong>roomslunera_hotel.tasks</strong></li>";
echo "<li>✓ Assignments stored in: <strong>webdb.assignments</strong></li>";
echo "<li>✓ Rooms stored in: <strong>roomslunera_hotel.rooms</strong></li>";
echo "<li>✓ Cross-database JOINs work without foreign keys</li>";
echo "</ul>";

echo "<br><a href='admin-dashboard.php' style='padding:10px 20px; background:maroon; color:white; text-decoration:none; border-radius:4px;'>Go to Admin Dashboard</a>";
?>
