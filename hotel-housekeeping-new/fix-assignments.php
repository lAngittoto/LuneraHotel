<?php
require("includes/database.php");

echo "<h2>Fix Assignments</h2>";

// Option 1: Check if tasks exist in webdb
echo "<h3>Checking for tasks in webdb.tasks:</h3>";
$webdbTasksCheck = $conn->query("SHOW TABLES LIKE 'tasks'");
if ($webdbTasksCheck->num_rows > 0) {
    echo "✓ Tasks table exists in webdb<br>";
    $oldTasks = $conn->query("SELECT * FROM webdb.tasks");
    echo "Found " . $oldTasks->num_rows . " tasks in webdb.tasks<br><br>";
    
    if ($oldTasks->num_rows > 0) {
        echo "<h3>Migrating tasks to roomslunera_hotel.tasks:</h3>";
        while ($task = $oldTasks->fetch_assoc()) {
            // Check if task already exists
            $checkStmt = $connTasks->prepare("SELECT TaskID FROM tasks WHERE TaskID = ?");
            $checkStmt->bind_param('i', $task['TaskID']);
            $checkStmt->execute();
            $exists = $checkStmt->get_result()->num_rows > 0;
            $checkStmt->close();
            
            if (!$exists) {
                $stmt = $connTasks->prepare("INSERT INTO tasks (TaskID, Description, RoomID) VALUES (?, ?, ?)");
                $stmt->bind_param('isi', $task['TaskID'], $task['Description'], $task['RoomID']);
                if ($stmt->execute()) {
                    echo "✓ Migrated Task {$task['TaskID']}: {$task['Description']}<br>";
                } else {
                    echo "✗ Failed to migrate Task {$task['TaskID']}: " . $stmt->error . "<br>";
                }
                $stmt->close();
            } else {
                echo "- Task {$task['TaskID']} already exists<br>";
            }
        }
    }
} else {
    echo "✗ No tasks table found in webdb<br>";
    echo "<h3>Need to clean up orphaned assignments:</h3>";
    
    // Delete assignments that reference non-existent tasks
    $deleteResult = $conn->query("DELETE a FROM webdb.assignments a 
                                   WHERE NOT EXISTS (
                                       SELECT 1 FROM roomslunera_hotel.tasks t 
                                       WHERE t.TaskID = a.TaskID
                                   )");
    
    if ($deleteResult) {
        echo "✓ Cleaned up " . $conn->affected_rows . " orphaned assignments<br>";
    } else {
        echo "✗ Error cleaning assignments: " . $conn->error . "<br>";
    }
}

echo "<br><h3>Current Status:</h3>";
$tasksCount = $connTasks->query("SELECT COUNT(*) as c FROM tasks")->fetch_assoc()['c'];
$assignCount = $conn->query("SELECT COUNT(*) as c FROM assignments WHERE Status != 'Completed'")->fetch_assoc()['c'];
$validCount = $conn->query("SELECT COUNT(*) as c FROM webdb.assignments a 
                             JOIN roomslunera_hotel.tasks t ON a.TaskID = t.TaskID 
                             WHERE a.Status != 'Completed'")->fetch_assoc()['c'];

echo "Tasks in roomslunera_hotel.tasks: $tasksCount<br>";
echo "Assignments in webdb.assignments: $assignCount<br>";
echo "Valid assignments (with matching tasks): $validCount<br>";

if ($validCount > 0) {
    echo "<br><strong style='color:green;'>✓ Assignments should now appear!</strong><br>";
} else {
    echo "<br><strong style='color:orange;'>⚠ No valid assignments. Create new assignments from the admin dashboard.</strong><br>";
}
?>
<br><br>
<a href="admin-dashboard.php" style="padding:10px 20px; background:maroon; color:white; text-decoration:none; border-radius:4px;">Go to Admin Dashboard</a>
