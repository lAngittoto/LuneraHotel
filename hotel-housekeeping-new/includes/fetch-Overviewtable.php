<?php
require("database.php");

$filterStatus = $_GET['status'] ?? "All";
$searchRoom   = $_GET['search'] ?? "";

$sql = "SELECT r.id as RoomID, r.room_number as RoomNumber, r.status as Status, r.room_type as RoomType, r.floor as Floor, MAX(h.FullName) as FullName, MAX(a.Status) as AssignmentStatus 
        FROM roomslunera_hotel.rooms r
        LEFT JOIN roomslunera_hotel.tasks t ON r.id = t.RoomID
        LEFT JOIN webdb.assignments a ON t.TaskID = a.TaskID AND a.Status IN ('Pending', 'In Progress')
        LEFT JOIN webdb.housekeepers h ON a.HousekeeperID = h.HousekeeperID 
        WHERE 1=1";
$params = [];
$types  = "";

if ($filterStatus !== "All") {
    $sql .= " AND r.Status = ?";
    $params[] = $filterStatus;
    $types   .= "s";
}

if (!empty($searchRoom)) {
    $sql .= " AND r.RoomNumber LIKE ?";
    $params[] = "%$searchRoom%";
    $types   .= "s";
}

$sql .= " GROUP BY r.id";

$stmt = $connTasks->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

echo "<table>
        <thead>
            <tr>
                <td class='label'>Room</td>
                <td class='label'>Status</td>
                <td class='label'>Assigned Staff</td>
                <td class='label'>Maintenance Issues</td>
            </tr>
        </thead>";

while ($row = $result->fetch_assoc()) {
    // Get maintenance request count for this room
    $maintStmt = $conn->prepare("SELECT COUNT(*) as issue_count FROM maintenancerequests WHERE RoomID = ? AND Status IN ('Open', 'In Progress')");
    $maintStmt->bind_param('i', $row['RoomID']);
    $maintStmt->execute();
    $maintResult = $maintStmt->get_result();
    $maintRow = $maintResult->fetch_assoc();
    $issueCount = $maintRow['issue_count'] ?? 0;
    $maintStmt->close();
    
    $issueDisplay = $issueCount > 0 ? $issueCount . ' open issue(s)' : '---';
    
    echo "<tr>
            <td>" . htmlspecialchars($row['RoomNumber']) . "</td>
            <td>" . htmlspecialchars($row['Status']) . "</td>
            <td>" . htmlspecialchars($row['FullName'] ?: 'Unassigned') . "</td>
            <td>" . $issueDisplay . "</td>
        </tr>";
}

echo "</table>";
?>