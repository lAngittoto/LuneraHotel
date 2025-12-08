<?php
require("database.php");

$sql = "SELECT AssignedFloor, FullName, HousekeeperID, Availability FROM housekeepers WHERE AssignedFloor IS NOT NULL ORDER BY AssignedFloor, FullName";
$result = $conn->query($sql);

$floors = [];
while ($row = $result->fetch_assoc()) {
    $floors[$row['AssignedFloor']][] = $row;
}

$html = '';
foreach ($floors as $floorNum => $staffList) {
    $html .= "<div class='floor-card'>";
    $html .= "<h3>Floor " . htmlspecialchars($floorNum) . "</h3>";

    foreach ($staffList as $staff) {
        // Fetch schedule for this housekeeper
        $schedule = 'No schedule set';
        $scheduleQuery = "SELECT CONCAT(
                            CASE WHEN s.StartDay = s.EndDay THEN s.StartDay 
                            ELSE CONCAT(s.StartDay, ' - ', s.EndDay) END,
                            ', ', s.StartTime, ' - ', s.EndTime
                          ) as schedule
                          FROM housekeepershifts hs
                          JOIN shifts s ON hs.ShiftID = s.ShiftID
                          WHERE hs.HousekeeperID = ?
                          LIMIT 1";
        
        try {
            $schedStmt = $conn->prepare($scheduleQuery);
            $schedStmt->bind_param('i', $staff['HousekeeperID']);
            $schedStmt->execute();
            $schedResult = $schedStmt->get_result();
            $schedRow = $schedResult->fetch_assoc();
            $schedule = $schedRow['schedule'] ?? 'No schedule set';
            $schedStmt->close();
        } catch (Exception $e) {
            $schedule = 'No schedule set';
        }
        
        // Availability badge colors
        $availability = $staff['Availability'] ?? 'Available';
        $availabilityColors = [
            'Available' => 'background:#d1fae5;color:#065f46',
            'On Break' => 'background:#fef3c7;color:#92400e',
            'Absent' => 'background:#fee2e2;color:#991b1b',
            'On Leave' => 'background:#dbeafe;color:#1e40af',
            'Unavailable' => 'background:#e5e7eb;color:#374151'
        ];
        $availStyle = $availabilityColors[$availability] ?? 'background:#e5e7eb;color:#374151';
        
        $html .= "<div class='staff-member'>";
        $html .= "<div>";
        $html .= "<div class='name'>" . htmlspecialchars($staff['FullName']) . "</div>";
        $html .= "<div class='schedule'>" . htmlspecialchars($schedule) . "</div>";
        $html .= "<div style='margin-top:4px;'><span style='font-size:0.75rem;padding:2px 6px;border-radius:4px;font-weight:600;{$availStyle}'>" . htmlspecialchars($availability) . "</span></div>";
        $html .= "</div>";
        $html .= "</div>";
    }

    $html .= "</div>";
}

echo $html;
?>
