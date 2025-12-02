<?php

function createCleaningTask($pdo, $pdoWeb, $roomId, $description, $housekeeperId = null) {
    // Step 1: Create the task in roomslunera_hotel database
    $stmt = $pdo->prepare("
        INSERT INTO tasks (Description, RoomID)
        VALUES (?, ?)
    ");
    $stmt->execute([$description, $roomId]);
    $taskId = $pdo->lastInsertId();
    
    if (!$taskId) {
        throw new Exception('Failed to create task');
    }
    
    // Step 2: Create the assignment in webdb
    if ($housekeeperId) {
        // Assigned to specific staff
        $stmt = $pdoWeb->prepare("
            INSERT INTO assignments (HousekeeperID, TaskID, AssignedDate, Status)
            VALUES (?, ?, CURDATE(), 'Pending')
        ");
        $stmt->execute([$housekeeperId, $taskId]);
    } else {
        // Unassigned (NULL housekeeper)
        $stmt = $pdoWeb->prepare("
            INSERT INTO assignments (HousekeeperID, TaskID, AssignedDate, Status)
            VALUES (NULL, ?, CURDATE(), 'Pending')
        ");
        $stmt->execute([$taskId]);
    }
    
    return $taskId;
}

function getTasksByRoom($pdo, $roomId) {
    $stmt = $pdo->prepare("
        SELECT TaskID, Description, RoomID
        FROM tasks
        WHERE RoomID = ?
        ORDER BY TaskID DESC
    ");
    $stmt->execute([$roomId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
