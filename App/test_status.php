<?php
require_once __DIR__ . '/config/db.php';

// Check the status column definition
$stmt = $pdo->query('DESCRIBE rooms');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($row['Field'] === 'status') {
        echo "Status column definition:\n";
        print_r($row);
    }
}

// Test if we can set Dirty status
$stmt = $pdo->prepare("SELECT id, room_number, status FROM rooms LIMIT 1");
$stmt->execute();
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if ($room) {
    echo "\n\nCurrent room:\n";
    print_r($room);
    
    // Try to update to Dirty
    echo "\n\nAttempting to set status to 'Dirty'...\n";
    $stmt = $pdo->prepare("UPDATE rooms SET status = 'Dirty' WHERE id = ?");
    $result = $stmt->execute([$room['id']]);
    
    if ($result) {
        echo "Update successful!\n";
        
        // Verify the update
        $stmt = $pdo->prepare("SELECT id, room_number, status FROM rooms WHERE id = ?");
        $stmt->execute([$room['id']]);
        $updatedRoom = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nRoom after update:\n";
        print_r($updatedRoom);
    } else {
        echo "Update failed!\n";
    }
}
?>
