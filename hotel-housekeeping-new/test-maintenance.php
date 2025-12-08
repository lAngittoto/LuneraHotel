<?php
require("includes/database.php");

echo "<h2>Test Maintenance Request</h2>";

// Check if maintenancerequests table exists
echo "<h3>Checking maintenancerequests table...</h3>";
$result = $conn->query("SHOW TABLES LIKE 'maintenancerequests'");
if ($result->num_rows > 0) {
    echo "<p style='color:green;'>✓ Table exists</p>";
    
    // Show table structure
    $structure = $conn->query("DESCRIBE maintenancerequests");
    echo "<h4>Table Structure:</h4>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>✗ Table does NOT exist in webdb database</p>";
    
    // Check if it exists in roomslunera_hotel
    $result = $connTasks->query("SHOW TABLES LIKE 'maintenancerequests'");
    if ($result->num_rows > 0) {
        echo "<p style='color:orange;'>⚠ Found in roomslunera_hotel database instead!</p>";
        
        $structure = $connTasks->query("DESCRIBE maintenancerequests");
        echo "<h4>Table Structure (roomslunera_hotel):</h4>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td></tr>";
        }
        echo "</table>";
        
        echo "<p><strong>Solution:</strong> Use \$connTasks instead of \$conn for maintenance requests</p>";
    } else {
        echo "<p style='color:red;'>✗ Table doesn't exist in either database</p>";
        echo "<p><strong>Need to create the table first</strong></p>";
    }
}

// Test room lookup
echo "<hr><h3>Test Room Lookup</h3>";
$testRoom = '101';
$stmt = $connTasks->prepare("SELECT id as RoomID, room_number, status FROM roomslunera_hotel.rooms WHERE room_number = ?");
$stmt->bind_param('s', $testRoom);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
$stmt->close();

if ($room) {
    echo "<p style='color:green;'>✓ Found Room {$testRoom}: ID = {$room['RoomID']}, Status = {$room['status']}</p>";
} else {
    echo "<p style='color:red;'>✗ Room {$testRoom} not found</p>";
}

echo "<br><a href='admin-dashboard.php'>Back to Dashboard</a>";
?>
