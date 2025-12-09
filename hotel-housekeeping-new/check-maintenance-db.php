<?php
require("includes/database.php");

echo "<h2>Check Maintenance Table Location & Structure</h2>";

// Check webdb
echo "<h3>webdb database:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'maintenancerequests'");
if ($result->num_rows > 0) {
    echo "<p style='color:green;'>✓ maintenancerequests exists in webdb</p>";
    
    $structure = $conn->query("DESCRIBE maintenancerequests");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Key']}</td></tr>";
    }
    echo "</table>";
    
    // Check foreign keys
    $fkQuery = "SELECT 
                    CONSTRAINT_NAME,
                    COLUMN_NAME,
                    REFERENCED_TABLE_SCHEMA,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = 'webdb'
                AND TABLE_NAME = 'maintenancerequests'
                AND REFERENCED_TABLE_NAME IS NOT NULL";
    
    $fkResult = $conn->query($fkQuery);
    if ($fkResult->num_rows > 0) {
        echo "<h4>Foreign Keys:</h4>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Constraint</th><th>Column</th><th>References</th></tr>";
        while ($row = $fkResult->fetch_assoc()) {
            echo "<tr><td>{$row['CONSTRAINT_NAME']}</td><td>{$row['COLUMN_NAME']}</td><td>{$row['REFERENCED_TABLE_SCHEMA']}.{$row['REFERENCED_TABLE_NAME']}.{$row['REFERENCED_COLUMN_NAME']}</td></tr>";
        }
        echo "</table>";
        echo "<p style='color:red;'>⚠️ <strong>Issue Found:</strong> Foreign key references prevent cross-database inserts!</p>";
    } else {
        echo "<p style='color:green;'>✓ No foreign key constraints</p>";
    }
}

// Check roomslunera_hotel
echo "<hr><h3>roomslunera_hotel database:</h3>";
$result = $connTasks->query("SHOW TABLES LIKE 'maintenancerequests'");
if ($result->num_rows > 0) {
    echo "<p style='color:green;'>✓ maintenancerequests exists in roomslunera_hotel</p>";
    echo "<p><strong>Solution:</strong> Use \$connTasks instead of \$conn</p>";
} else {
    echo "<p style='color:orange;'>Table not found in roomslunera_hotel</p>";
}

echo "<br><a href='admin-dashboard.php'>Back to Dashboard</a>";
?>
