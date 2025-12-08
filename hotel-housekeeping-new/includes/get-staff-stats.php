<?php
session_start();
require_once 'database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['accType'] !== 'staff') {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$housekeeperId = $_SESSION['housekeeperID'] ?? 0;

try {
    // Get total completed tasks
    $completedQuery = "SELECT COUNT(*) as total FROM webdb.assignments WHERE HousekeeperID = ? AND Status = 'Completed'";
    $stmt = $conn->prepare($completedQuery);
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $completedData = $result->fetch_assoc();
    $totalCompleted = $completedData['total'] ?? 0;
    $stmt->close();
    
    // Get pending tasks
    $pendingQuery = "SELECT COUNT(*) as total FROM webdb.assignments WHERE HousekeeperID = ? AND Status IN ('Pending', 'In Progress')";
    $stmt = $conn->prepare($pendingQuery);
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $pendingData = $result->fetch_assoc();
    $totalPending = $pendingData['total'] ?? 0;
    $stmt->close();
    
    // Get average cleaning time (in minutes) - TimeCompleted is in MM:SS format
    $avgTimeQuery = "SELECT AVG(
                        CAST(SUBSTRING_INDEX(TimeCompleted, ':', 1) AS UNSIGNED) * 60 +
                        CAST(SUBSTRING_INDEX(TimeCompleted, ':', -1) AS UNSIGNED)
                     ) as avgTime 
                     FROM webdb.assignments 
                     WHERE HousekeeperID = ? AND Status = 'Completed' AND TimeCompleted IS NOT NULL";
    $stmt = $conn->prepare($avgTimeQuery);
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $avgTimeData = $result->fetch_assoc();
    $avgCleaningTime = round($avgTimeData['avgTime'] ?? 0, 1);
    $stmt->close();
    
    // Get completed tasks this month
    $monthQuery = "SELECT COUNT(*) as total FROM webdb.assignments WHERE HousekeeperID = ? AND Status = 'Completed' AND MONTH(AssignedDate) = MONTH(CURDATE()) AND YEAR(AssignedDate) = YEAR(CURDATE())";
    $stmt = $conn->prepare($monthQuery);
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $monthData = $result->fetch_assoc();
    $completedThisMonth = $monthData['total'] ?? 0;
    $stmt->close();
    
    // Get completed tasks this week
    $weekQuery = "SELECT COUNT(*) as total FROM webdb.assignments WHERE HousekeeperID = ? AND Status = 'Completed' AND YEARWEEK(AssignedDate, 1) = YEARWEEK(CURDATE(), 1)";
    $stmt = $conn->prepare($weekQuery);
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $weekData = $result->fetch_assoc();
    $completedThisWeek = $weekData['total'] ?? 0;
    $stmt->close();
    
    // Get completed tasks today
    $todayQuery = "SELECT COUNT(*) as total FROM webdb.assignments WHERE HousekeeperID = ? AND Status = 'Completed' AND DATE(AssignedDate) = CURDATE()";
    $stmt = $conn->prepare($todayQuery);
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $todayData = $result->fetch_assoc();
    $completedToday = $todayData['total'] ?? 0;
    $stmt->close();
    
    // Get recent completed assignments with details
    $recentQuery = "SELECT a.RoomNumber, a.TaskDescription, a.TimeCompleted, a.AssignedDate 
                    FROM webdb.assignments a
                    WHERE a.HousekeeperID = ? AND a.Status = 'Completed'
                    ORDER BY a.AssignedDate DESC
                    LIMIT 5";
    $stmt = $conn->prepare($recentQuery);
    $stmt->bind_param('i', $housekeeperId);
    $stmt->execute();
    $result = $stmt->get_result();
    $recentTasks = [];
    while ($row = $result->fetch_assoc()) {
        // Convert MM:SS to total seconds for display
        $timeCompleted = $row['TimeCompleted'];
        $cleaningTimeMinutes = 0;
        if ($timeCompleted) {
            list($minutes, $seconds) = explode(':', $timeCompleted);
            $cleaningTimeMinutes = round(($minutes * 60 + $seconds) / 60, 1);
        }
        
        $recentTasks[] = [
            'roomNumber' => $row['RoomNumber'],
            'taskDescription' => $row['TaskDescription'],
            'cleaningTime' => $cleaningTimeMinutes,
            'completedAt' => $row['AssignedDate']
        ];
    }
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'totalCompleted' => $totalCompleted,
            'totalPending' => $totalPending,
            'avgCleaningTime' => $avgCleaningTime,
            'completedThisMonth' => $completedThisMonth,
            'completedThisWeek' => $completedThisWeek,
            'completedToday' => $completedToday,
            'recentTasks' => $recentTasks
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
