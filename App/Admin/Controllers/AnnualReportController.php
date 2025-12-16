<?php
// ======================= CONTROLLER =======================
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/AnnualReportModel.php';

class AnnualReportController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new AnnualReportModel($pdo);
    }

    public function index()
    {
        $year = $_GET['year'] ?? date('Y');
        $data = $this->model->generateReport($year);

        $summary = $data['summary'] ?? [];
        $monthlyReport = $data['monthlyReport'] ?? array_fill(1,12,0);
        $roomTypeBreakdown = $data['roomTypeBreakdown'] ?? [];
        $popularRoomType = $data['popularRoomType'] ?? 'N/A';
        $itemUsageReport = $data['itemUsageReport'] ?? [];

        require __DIR__ . '/../Views/annualreport.php';
    }
}
?>
