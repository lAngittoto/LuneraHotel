<?php
require("includes/database.php");

echo "<h2>Assignments Table Structure</h2>";

// Show table structure
$result = $conn->query("DESCRIBE webdb.assignments");
echo "<h3>Current Columns:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "<td>{$row['Extra']}</td>";
    echo "</tr>";
}
echo "</table>";

// Show sample data
echo "<h3>Sample Assignment Data:</h3>";
$result = $conn->query("SELECT * FROM webdb.assignments LIMIT 5");
echo "<table border='1' cellpadding='5'>";
$first = true;
while ($row = $result->fetch_assoc()) {
    if ($first) {
        echo "<tr>";
        foreach (array_keys($row) as $col) {
            echo "<th>{$col}</th>";
        }
        echo "</tr>";
        $first = false;
    }
    echo "<tr>";
    foreach ($row as $val) {
        echo "<td>{$val}</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Check if TaskID column exists
$hasTaskID = $conn->query("SELECT TaskID FROM webdb.assignments LIMIT 1");
if (!$hasTaskID) {
    echo "<h3 style='color:red;'>❌ TaskID column is missing!</h3>";
    echo "<p>Need to add TaskID column back to assignments table.</p>";
    
    echo "<form method='post'>";
    echo "<button type='submit' name='addTaskID' style='padding:10px 20px; background:maroon; color:white; border:none; border-radius:4px; cursor:pointer;'>Add TaskID Column</button>";
    echo "</form>";
    
    if (isset($_POST['addTaskID'])) {
        $alterSQL = "ALTER TABLE webdb.assignments ADD COLUMN TaskID INT NOT NULL AFTER HousekeeperID";
        if ($conn->query($alterSQL)) {
            echo "<p style='color:green;'>✓ TaskID column added successfully!</p>";
            echo "<script>setTimeout(() => location.reload(), 1000);</script>";
        } else {
            echo "<p style='color:red;'>✗ Error: " . $conn->error . "</p>";
        }
    }
} else {
    echo "<h3 style='color:green;'>✓ TaskID column exists</h3>";
}

echo "<br><br><a href='admin-dashboard.php'>Back to Dashboard</a>";
?>
