<?php
$servername = "localhost";
$dbUsername = "root";
$dbPass = "P@ssw0rd";
$dbName = "webdb";

try{
    $conn = new mysqli($servername, $dbUsername, $dbPass, $dbName);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}catch(Exception $e){
    die("Can't Connect: " . $e->getMessage());
}

// Create second connection for roomslunera_Hotel database (for tasks)
$dbNameTasks = "roomslunera_hotel";
try{
    $connTasks = new mysqli($servername, $dbUsername, $dbPass, $dbNameTasks);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}catch(Exception $e){
    die("Can't Connect to Tasks DB: " . $e->getMessage());
}

// Add Availability column if it doesn't exist
$checkColumn = @$conn->query("SHOW COLUMNS FROM housekeepers LIKE 'Availability'");
if ($checkColumn && $checkColumn->num_rows == 0) {
    @$conn->query("ALTER TABLE housekeepers ADD COLUMN Availability ENUM('Available','On Break','Absent','On Leave','Unavailable') DEFAULT 'Available' AFTER AssignedFloor");
}
?>