<?php
class AnnualReportModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function generateReport($year)
    {
        // SUMMARY
        $summary = [
            "total_rooms" => (int)$this->pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn(),
            "available" => (int)$this->pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Available'")->fetchColumn(),
            "booked" => (int)$this->pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Booked'")->fetchColumn(),
            "deactivated" => (int)$this->pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Deactivated'")->fetchColumn(),
            "total_bookings" => (int)$this->pdo->query("SELECT COUNT(*) FROM bookings WHERE YEAR(booking_date) = $year")->fetchColumn()
        ];

        // BOOKINGS
        $stmt = $this->pdo->prepare("
            SELECT b.*, r.room_number, r.room_type
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            WHERE YEAR(b.booking_date) = ?
            ORDER BY b.booking_date DESC
        ");
        $stmt->execute([$year]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // MONTHLY BREAKDOWN
        $stmt2 = $this->pdo->prepare("
            SELECT MONTH(booking_date) AS month, COUNT(*) AS total
            FROM bookings
            WHERE YEAR(booking_date) = ?
            GROUP BY MONTH(booking_date)
            ORDER BY MONTH(booking_date)
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
            $type['occupancy_rate'] = $type['total_rooms'] > 0 ? ($type['bookings'] / $type['total_rooms']) * 100 : 0;
        }
        unset($type);

        // Top Room Type → Popular Room Type
        usort($roomTypes, fn($a,$b) => $b['bookings'] - $a['bookings']);
        $popularRoomType = $roomTypes[0]['type_name'] ?? 'N/A';

        return [
            "summary" => $summary,
            "bookings" => $bookings,
            "monthlyReport" => $monthlyReport,
            "roomTypeBreakdown" => $roomTypes,
            "popularRoomType" => $popularRoomType
        ];
    }
}
?>
