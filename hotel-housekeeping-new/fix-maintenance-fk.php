<?php
require("includes/database.php");

echo "<h2>Fix Maintenance Requests Foreign Key</h2>";

// Remove the foreign key constraint
$dropFK = "ALTER TABLE webdb.maintenancerequests DROP FOREIGN KEY maintenancerequests_ibfk_1";
if ($conn->query($dropFK)) {
    echo "<p style='color:green;'>✓ Removed foreign key constraint</p>";
} else {
    echo "<p style='color:red;'>✗ Error: " . $conn->error . "</p>";
}

// Now maintenance requests can use RoomID from roomslunera_hotel without constraint
echo "<p style='color:green;'><strong>✓ Fixed!</strong> Maintenance requests can now reference rooms from roomslunera_hotel database.</p>";

echo "<br><a href='admin-dashboard.php' style='padding:10px 20px; background:maroon; color:white; text-decoration:none; border-radius:4px;'>Go to Dashboard</a>";
?>
