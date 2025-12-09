<?php
ob_clean();
header('Content-Type: application/json');
require_once 'database.php';

try {
    if (!isset($_GET['staff']) || trim($_GET['staff']) === '') {
        echo json_encode(['error' => 'No staff selected.']);
        exit;
    }
    $staff = trim($_GET['staff']);
    if ($staff === '') {
        echo json_encode(['error' => 'No staff selected.']);
        exit;
    }

    // Get HousekeeperID from FullName
    $stmt = $conn->prepare("SELECT HousekeeperID FROM housekeepers WHERE FullName = ?");
    $stmt->bind_param('s', $staff);
    $stmt->execute();
    $result = $stmt->get_result();
    $housekeeper = $result->fetch_assoc();
    $stmt->close();
    
    if (!$housekeeper) {
        echo json_encode(['error' => 'Housekeeper not found.']);
        exit;
    }
    
    $housekeeperId = $housekeeper['HousekeeperID'];

    // Get total rooms cleaned from completed assignments
    $sqlCleaned = "SELECT COUNT(*) as total_cleaned
                   FROM assignments a
                   WHERE a.HousekeeperID = ? AND a.Status = 'Completed'";
    $stmt = $conn->prepare($sqlCleaned);
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cleanedStats = $result->fetch_assoc();
    $totalCleaned = $cleanedStats['total_cleaned'] ?? 0;
    $stmt->close();

    // Get average cleaning time per room (TimeCompleted is in MM:SS format)
    $sqlAvgTime = "SELECT AVG(
                        CAST(SUBSTRING_INDEX(a.TimeCompleted, ':', 1) AS UNSIGNED) * 60 +
                        CAST(SUBSTRING_INDEX(a.TimeCompleted, ':', -1) AS UNSIGNED)
                    ) as avg_time
                   FROM assignments a
                   WHERE a.HousekeeperID = ? AND a.Status = 'Completed' AND a.TimeCompleted IS NOT NULL";
    $stmt = $conn->prepare($sqlAvgTime);
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $avgTimeRow = $result->fetch_assoc();
    $avgTimeSec = $avgTimeRow['avg_time'] ?? 0;
    $stmt->close();

    // Get room types cleaned
    $sqlRoomTypes = "SELECT r.room_type as RoomType, COUNT(*) as count
                     FROM webdb.assignments a
                     JOIN roomslunera_hotel.tasks t ON a.TaskID = t.TaskID
                     JOIN roomslunera_hotel.rooms r ON t.RoomID = r.id
                     WHERE a.HousekeeperID = ? AND a.Status = 'Completed'
                     GROUP BY r.room_type
                     ORDER BY count DESC";
    $stmt = $conn->prepare($sqlRoomTypes);
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $roomTypes = [];
    while ($row = $result->fetch_assoc()) {
        $roomTypes[] = ($row['RoomType'] ?? 'Standard') . ': ' . $row['count'];
    }
    $stmt->close();

    // Maintenance issues reported by this housekeeper
    $sql2 = "SELECT r.room_number as RoomNumber, m.Description, m.ReportedDate, m.RequestID
             FROM webdb.maintenancerequests m 
             JOIN roomslunera_hotel.rooms r ON m.RoomID = r.id 
             JOIN roomslunera_hotel.tasks t ON t.RoomID = r.id 
             JOIN webdb.assignments a ON a.TaskID = t.TaskID
             WHERE a.HousekeeperID = ? AND m.Status IN ('Open', 'In Progress', 'Resolved')
             GROUP BY m.RequestID, r.room_number, m.Description, m.ReportedDate
             ORDER BY m.ReportedDate DESC
             LIMIT 5";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param('i', $housekeeperId);
    $stmt2->execute();
    $issues = $stmt2->get_result();
    $issueList = [];
    $issueCount = 0;
    while ($issue = $issues->fetch_assoc()) {
        $issueList[] = 'Room ' . $issue['RoomNumber'] . ' (' . $issue['ReportedDate'] . '): ' . $issue['Description'];
        $issueCount++;
    }
    $stmt2->close();

    // Get total maintenance count
    $sqlMaintenanceCount = "SELECT COUNT(DISTINCT m.RequestID) as total
                            FROM webdb.maintenancerequests m 
                            JOIN roomslunera_hotel.rooms r ON m.RoomID = r.id 
                            JOIN roomslunera_hotel.tasks t ON t.RoomID = r.id 
                            JOIN webdb.assignments a ON a.TaskID = t.TaskID
                            WHERE a.HousekeeperID = ?";
    $stmt = $conn->prepare($sqlMaintenanceCount);
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $maintenanceRow = $result->fetch_assoc();
    $totalMaintenance = $maintenanceRow['total'] ?? 0;
    $stmt->close();

    // Build comprehensive report
    $report = "Performance Report for $staff:\n\n";
    
    // Rooms cleaned metrics
    $report .= "ROOMS CLEANED:\n";
    $report .= "- Total Completed Tasks: " . $totalCleaned . " room(s)\n\n";
    
    // Average cleaning time
    $report .= "EFFICIENCY:\n";
    if ($avgTimeSec > 0) {
        $min = floor($avgTimeSec / 60);
        $sec = round($avgTimeSec % 60);
        $timeStr = ($min > 0 ? $min . ' min ' : '') . $sec . ' sec';
        $report .= "- Average Cleaning Time: " . $timeStr . " per room\n\n";
    } else {
        $report .= "- Average Cleaning Time: N/A\n\n";
    }
    
    // Room types cleaned
    $report .= "ROOM TYPES CLEANED:\n";
    if (!empty($roomTypes)) {
        foreach ($roomTypes as $type) {
            $report .= "- " . $type . "\n";
        }
    } else {
        $report .= "- No data available\n";
    }
    $report .= "\n";
    
    // Maintenance reports
    $report .= "MAINTENANCE REPORTED:\n";
    $report .= "- Total Issues Reported: " . $totalMaintenance . "\n";
    if (!empty($issueList)) {
        $report .= "- Recent Issues (Last 5):\n";
        foreach ($issueList as $issue) {
            $report .= "  • " . $issue . "\n";
        }
    } else {
        $report .= "- No maintenance issues reported\n";
    }

    echo json_encode([
        'name' => $staff,
        'report' => $report,
        'stats' => [
            'total_cleaned' => $totalCleaned,
            'avg_time_sec' => $avgTimeSec,
            'maintenance_total' => $totalMaintenance
        ]
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}