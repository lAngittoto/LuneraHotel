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
        $summary = [
            "total_rooms" => $this->pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn(),
            "available"   => $this->pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Available'")->fetchColumn(),
            "booked"      => $this->pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Booked'")->fetchColumn(),
            "deactivated" => $this->pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Deactivated'")->fetchColumn(),
            "total_bookings" => $this->pdo->query("SELECT COUNT(*) FROM bookings WHERE YEAR(booking_date) = $year")->fetchColumn()
        ];

        $stmt = $this->pdo->prepare("
            SELECT b.*, r.room_number, r.room_type 
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            WHERE YEAR(b.booking_date) = ?
            ORDER BY b.booking_date DESC
        ");
        $stmt->execute([$year]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt2 = $this->pdo->prepare("
            SELECT * FROM notifications
            WHERE YEAR(completed_at) = ?
            ORDER BY completed_at DESC
        ");
        $stmt2->execute([$year]);
        $notifications = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return [
            "summary" => $summary,
            "bookings" => $bookings,
            "notifications" => $notifications
        ];
    }
}
