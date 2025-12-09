<?php
require("database.php");

// Fetch housekeepers with assigned room counts from assignments
$staffQuery  = "
    SELECT h.*, 
           COUNT(CASE WHEN a.Status IN ('Pending', 'In Progress') THEN 1 END) AS RoomCount
    FROM housekeepers h
    LEFT JOIN assignments a ON a.HousekeeperID = h.HousekeeperID
    GROUP BY h.HousekeeperID
    ORDER BY h.AssignedFloor, h.FullName
";
$staffResult = $conn->query($staffQuery);

echo '<table>
        <thead>
            <tr>
                <th style="width: 200px;">Staff</th>
                <th style="width: 150px;">Availability</th>
                <th style="width: 200px;">Reason</th>
                <th style="width: 150px;">Assigned Floor</th>
                <th style="width: 150px;">Assigned Rooms</th>
                <th>Schedule</th>
            </tr>
        </thead>
        <tbody>';

while ($row = $staffResult->fetch_assoc()) {
    $housekeeperId = (int) $row['HousekeeperID'];
    $assignedFloor = (int) ($row['AssignedFloor'] ?? 0);
    $roomCount     = (int) ($row['RoomCount'] ?? 0);
    $availability  = $row['Availability'] ?? 'Available';
    $reason        = $row['reason'] ?? '';

    // Availability badge colors
    $availabilityColors = [
        'Available' => 'background:#d1fae5;color:#065f46',
        'On Break' => 'background:#fef3c7;color:#92400e',
        'Absent' => 'background:#fee2e2;color:#991b1b',
        'On Leave' => 'background:#dbeafe;color:#1e40af',
        'Unavailable' => 'background:#e5e7eb;color:#374151'
    ];
    $availStyle = $availabilityColors[$availability] ?? 'background:#e5e7eb;color:#374151';

    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['FullName']) . '</td>';
    echo '<td><span style="font-size:0.85rem;padding:4px 8px;border-radius:4px;font-weight:600;display:inline-block;' . $availStyle . '">' . htmlspecialchars($availability) . '</span></td>';
    
    // Display reason if status requires it
    $requiresReason = ['Absent', 'On Leave', 'Unavailable'];
    if (in_array($availability, $requiresReason) && !empty($reason)) {
        echo '<td style="font-size:0.9rem;color:#555;">' . htmlspecialchars($reason) . '</td>';
    } else {
        echo '<td style="color:#999;">—</td>';
    }

    // dropdown for floors
    echo '<td><select style="background-color: white; cursor: pointer; border: 1.5px solid var(--border-color); border-radius: 4px; padding: 10px;" class="floorSelect" data-id="' . $housekeeperId . '">';
    echo '<option value="">Not Assigned</option>';
    
    // Dynamically get floors from rooms table
    $floorsQuery = "SELECT DISTINCT floor FROM roomslunera_hotel.rooms ORDER BY floor";
    $floorsResult = $connTasks->query($floorsQuery);
    while ($floorRow = $floorsResult->fetch_assoc()) {
        $floorNum = (int) $floorRow['floor'];
        $selected = ($assignedFloor === $floorNum) ? ' selected' : '';
        echo '<option value="' . $floorNum . '"' . $selected . '>Floor ' . $floorNum . '</option>';
    }
    echo '</select></td>';

    // assigned rooms with badge styling
    echo '<td><span style="background:#e0e7ff;color:#3730a3;padding:4px 12px;border-radius:12px;font-weight:600;font-size:0.9rem;">' . $roomCount . '</span></td>';

    // Fetch current shift assignment for this housekeeper
    $currentShiftQuery = "SELECT ShiftID FROM housekeepershifts WHERE HousekeeperID = ? LIMIT 1";
    $shiftStmt = $conn->prepare($currentShiftQuery);
    $shiftStmt->bind_param('i', $housekeeperId);
    $shiftStmt->execute();
    $shiftResult = $shiftStmt->get_result();
    $currentShift = $shiftResult->fetch_assoc();
    $currentShiftId = $currentShift['ShiftID'] ?? null;
    $shiftStmt->close();
    
    // Fetch all available shifts
    $shiftsQuery = "SELECT ShiftID, StartDay, EndDay, StartTime, EndTime FROM shifts ORDER BY 
                    FIELD(StartDay, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
                    StartTime";
    $shiftsResult = $conn->query($shiftsQuery);
    
    echo '<td>';
    echo '<select style="background-color: white; cursor: pointer; border: 1.5px solid var(--border-color); border-radius: 4px; padding: 10px; width: 100%;" class="shiftSelect" data-id="' . $housekeeperId . '">';
    echo '<option value="">-- Select Shift --</option>';
    
    while ($shift = $shiftsResult->fetch_assoc()) {
        $shiftId = $shift['ShiftID'];
        $startDay = $shift['StartDay'];
        $endDay = $shift['EndDay'];
        $startTime = $shift['StartTime'];
        $endTime = $shift['EndTime'];
        
        // Format display: "Monday - Friday, 08:00 AM - 05:00 PM"
        $dayRange = ($startDay === $endDay) ? $startDay : "$startDay - $endDay";
        $shiftLabel = "$dayRange, $startTime - $endTime";
        
        $selected = ($currentShiftId == $shiftId) ? ' selected' : '';
        echo '<option value="' . $shiftId . '"' . $selected . '>' . htmlspecialchars($shiftLabel) . '</option>';
    }
    
    echo '</select>';
    echo '</td>';

    echo '</tr>';
}

echo '</tbody></table>';
?>