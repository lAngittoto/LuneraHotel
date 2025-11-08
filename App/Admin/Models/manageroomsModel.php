<?php
function getRoomsSummary($pdo)
{
    $stmt = $pdo->prepare("
        SELECT `room_number`,`room_type`, `floor`, `people`, `status`
        FROM rooms
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
