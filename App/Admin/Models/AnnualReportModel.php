<?php
// ======================= MODEL =======================
class AnnualReportModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function generateReport($year)
    {
        $summary = [
            "total_rooms" => (int)$this->pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn(),
            "available" => (int)$this->pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Available'")->fetchColumn(),
            "booked" => (int)$this->pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Booked'")->fetchColumn(),
            "deactivated" => (int)$this->pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Deactivated'")->fetchColumn(),
            "total_bookings" => (int)$this->pdo->query("SELECT COUNT(*) FROM bookings WHERE YEAR(booking_date) = $year")->fetchColumn()
        ];

        // MONTHLY
        $stmt2 = $this->pdo->prepare("
            SELECT MONTH(booking_date) AS month, COUNT(*) AS total
            FROM bookings
            WHERE YEAR(booking_date) = ?
            GROUP BY MONTH(booking_date)
        ");
        $stmt2->execute([$year]);
        $monthlyRaw = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $monthlyReport = array_fill(1,12,0);
        foreach ($monthlyRaw as $row) {
            $monthlyReport[(int)$row['month']] = (int)$row['total'];
        }

        // ROOM TYPE BREAKDOWN
        $stmt3 = $this->pdo->query("
            SELECT r.room_type AS type_name, COUNT(*) AS total_rooms
            FROM rooms r
            GROUP BY r.room_type
        ");
        $roomTypes = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        foreach ($roomTypes as &$type) {
            $stmt4 = $this->pdo->prepare("
                SELECT COUNT(*) FROM bookings b
                JOIN rooms r ON b.room_id = r.id
                WHERE YEAR(b.booking_date)=? AND r.room_type=?
            ");
            $stmt4->execute([$year, $type['type_name']]);
            $type['bookings'] = (int)$stmt4->fetchColumn();
        }
        unset($type);

        usort($roomTypes, fn($a,$b) => $b['bookings'] - $a['bookings']);
        $popularRoomType = $roomTypes[0]['type_name'] ?? 'N/A';

        // ITEM USAGE ANNUAL REPORT
        $stmt5 = $this->pdo->query("
            SELECT 
                i.name AS item_name,
                i.location,
                SUM(riu.used_count) AS total_used,
                SUM(riu.max_use) AS total_capacity,
                SUM(CASE WHEN riu.condition_status='need to restock' THEN 1 ELSE 0 END) AS restock_count
            FROM room_item_usage riu
            JOIN items i ON riu.item_id = i.id
            GROUP BY i.id, i.name, i.location
            ORDER BY i.location, i.name
        ");
        $itemUsageReport = $stmt5->fetchAll(PDO::FETCH_ASSOC);

        return [
            "summary" => $summary,
            "monthlyReport" => $monthlyReport,
            "roomTypeBreakdown" => $roomTypes,
            "popularRoomType" => $popularRoomType,
            "itemUsageReport" => $itemUsageReport
        ];
    }
}
?>
