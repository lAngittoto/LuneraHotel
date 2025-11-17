<?php


function getFilteredRooms($pdo, $status = '', $type = '', $floor = '')
{
    $query = "SELECT * FROM rooms WHERE 1=1";
    $params = [];

    // Status filter
    if ($status !== "") {
        $query .= " AND status = :status";
        $params[':status'] = $status;
    }

    // Room type filter
    if ($type !== "") {
        switch ($type) {
            case "Triple":
                $query .= " AND (room_type = 'Triple' OR room_type = 'Junior Suite')";
                break;
            case "Family":
                $query .= " AND (room_type = 'Family' OR room_type = 'Connecting Family Room')";
                break;
            case "Deluxe":
                $query .= " AND (room_type = 'Deluxe' OR room_type = 'Deluxe Double Room')";
                break;
            case "Double":
                $query .= " AND (room_type = 'Double' OR room_type = 'Standard Double Room')";
                break;
            case "Single":
                $query .= " AND (room_type = 'Single' OR room_type = 'Cozy Single Room')";
                break;
            default:
                $query .= " AND room_type = :type";
                $params[':type'] = $type;
                break;
        }
    }

    // Floor filter
    if ($floor !== "") {
        $query .= " AND floor = :floor";
        $params[':floor'] = $floor;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
