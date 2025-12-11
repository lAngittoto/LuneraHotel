<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/AnnualReportModel.php';

class AnnualReportController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new AnnualReportModel($pdo);
    }

    // Show HTML summary
    public function index()
    {
        $year = $_GET['year'] ?? date('Y');

        $data = $this->model->generateReport($year);

        $summary = $data['summary'] ?? [];
        $bookings = $data['bookings'] ?? [];
        $notifications = $data['notifications'] ?? [];

        require __DIR__ . '/../Views/annualreport.php';
    }

    // Export PDF (simple version without composer)
    public function exportPDF()
    {
        $year = $_GET['year'] ?? date('Y');

        $data = $this->model->generateReport($year);

        $summary = $data['summary'] ?? [];
        $bookings = $data['bookings'] ?? [];
        $notifications = $data['notifications'] ?? [];

        require __DIR__ . '/../Views/pdf_report.php';
    }
}
