<?php
function getAllFloors($pdo)
{
    $stmt = $pdo->prepare("
        SELECT DISTINCT floor 
        FROM rooms 
        ORDER BY 
            CASE floor
    WHEN 'First Floor' THEN 1
    WHEN 'Second Floor' THEN 2
    WHEN 'Third Floor' THEN 3
    WHEN 'Fourth Floor' THEN 4
    WHEN 'Fifth Floor' THEN 5
    WHEN 'Sixth Floor' THEN 6
    WHEN 'Seventh Floor' THEN 7
    WHEN 'Eighth Floor' THEN 8
    WHEN 'Ninth Floor' THEN 9
    WHEN 'Tenth Floor' THEN 10
    ELSE 11
END

    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getRoomsByFloor($pdo, $floor)
{
    $stmt = $pdo->prepare("
        SELECT * 
        FROM rooms 
        WHERE floor = ? 
        AND status != 'Deactivated'
        ORDER BY room_number
    ");
    $stmt->execute([$floor]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
