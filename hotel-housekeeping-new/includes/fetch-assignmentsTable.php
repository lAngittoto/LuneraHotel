<?php
require("database.php");
$taskQuery = "SELECT a.AssignmentID, a.Status as AssignmentStatus, t.TaskID, t.Description, r.room_number as RoomNumber, r.floor as Floor, r.status as RoomStatus, h.FullName
              FROM webdb.assignments a
              JOIN roomslunera_hotel.tasks t ON a.TaskID = t.TaskID
              JOIN roomslunera_hotel.rooms r ON t.RoomID = r.id
              LEFT JOIN webdb.housekeepers h ON a.HousekeeperID = h.HousekeeperID
              WHERE a.Status != 'Completed'
              ORDER BY r.floor, r.room_number";
$taskResult = $conn->query($taskQuery);

$currentFloor = null;

while ($row = $taskResult->fetch_assoc()):
    if ($currentFloor !== $row['Floor']) {
        if ($currentFloor !== null) echo "</table>";
        $currentFloor = $row['Floor'];
        echo "<h3 style='color: maroon; margin-bottom:10px; margin-top:10px;'>Floor {$currentFloor}</h3>";
        echo "<table>
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Room Status</th>
                        <th>Task</th>
                        <th>Assigned Staff</th>
                        <th>Assignment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>";
    }
?>
<tr>
    <td><?= htmlspecialchars($row['RoomNumber']); ?></td>
    <?php
    $stat = strtolower($row['RoomStatus']);
    $badgeClass = '';
    if ($stat === 'dirty') {
        $badgeClass = 'badge-dirty';
    } elseif ($stat === 'clean') {
        $badgeClass = 'badge-clean';
    } elseif ($stat === 'in progress') {
        $badgeClass = 'badge-inprogress';
    }
    ?>
    <td><span class="status-badge <?= $badgeClass; ?>"><?= htmlspecialchars($row['RoomStatus']); ?></span></td>
    <td><?= htmlspecialchars($row['Description']); ?></td>
    <td><?= htmlspecialchars($row['FullName'] ?: 'Unassigned'); ?></td>
    <?php
    $assignStat = $row['AssignmentStatus'];
    $assignBadgeClass = '';
    if ($assignStat === 'Pending') {
        $assignBadgeClass = 'badge-dirty';
    } elseif ($assignStat === 'In Progress') {
        $assignBadgeClass = 'badge-inprogress';
    } elseif ($assignStat === 'Completed') {
        $assignBadgeClass = 'badge-clean';
    }
    ?>
    <td><span class="status-badge <?= $assignBadgeClass; ?>"><?= htmlspecialchars($assignStat); ?></span></td>
    <td>
        <button class="assign-btn" data-task="<?= $row['TaskID']; ?>" data-room="<?= $row['RoomNumber']; ?>" data-floor="<?= $row['Floor']; ?>"
            style="width:100px; height:35px; border-radius:6px; <?= !empty($row['FullName']) ? 'border:1px solid var(--border-color); background:white;' : 'border:1px solid maroon; background:maroon; color:white;' ?>">
            <?= !empty($row['FullName']) ? 'Reassign' : 'Assign' ?>
        </button>
    </td>
</tr>
<?php endwhile;
if ($currentFloor !== null) echo "</table>";
?>
