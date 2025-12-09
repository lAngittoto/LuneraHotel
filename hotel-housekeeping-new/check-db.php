<?php
$conn = new mysqli('localhost', 'root', 'P@ssw0rd');
$result = $conn->query("SHOW DATABASES LIKE 'roomslunera%'");
echo "Databases found:\n";
while ($row = $result->fetch_array()) {
    echo "- " . $row[0] . "\n";
}

// Check tasks table
$dbCheck1 = @new mysqli('localhost', 'root', 'P@ssw0rd', 'roomslunera_hotel');
if (!$dbCheck1->connect_error) {
    echo "\nroomslunera_hotel exists\n";
    $tables = $dbCheck1->query("SHOW TABLES");
    echo "Tables: ";
    while ($row = $tables->fetch_array()) {
        echo $row[0] . " ";
    }
    echo "\n";
    
    $count = $dbCheck1->query("SELECT COUNT(*) as c FROM tasks");
    if ($count) {
        $row = $count->fetch_assoc();
        echo "Tasks count: " . $row['c'] . "\n";
    }
}

$dbCheck2 = @new mysqli('localhost', 'root', 'P@ssw0rd', 'roomslunera_Hotel');
if (!$dbCheck2->connect_error) {
    echo "\nroomslunera_Hotel exists\n";
    $tables = $dbCheck2->query("SHOW TABLES");
    echo "Tables: ";
    while ($row = $tables->fetch_array()) {
        echo $row[0] . " ";
    }
}
?>
