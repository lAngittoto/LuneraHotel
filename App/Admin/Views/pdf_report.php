<?php
require_once __DIR__ . '/../../../vendor/tecnickcom/tcpdf/tcpdf.php';

$months = [
    "January","February","March","April","May","June",
    "July","August","September","October","November","December"
];

// Create PDF
$pdf = new TCPDF();
$pdf->SetCreator('Lunera Hotel');
$pdf->SetAuthor('Lunera Hotel');
$pdf->SetTitle("Annual Report $year");
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

// Title
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 10, "Annual Report - $year", 0, 1, 'C');
$pdf->Ln(5);

// Summary
$pdf->SetFont('helvetica', '', 12);
foreach (['Total Rooms' => $summary['total_rooms'], 
          'Available' => $summary['available'], 
          'Booked' => $summary['booked'], 
          'Deactivated' => $summary['deactivated'], 
          'Top Room Type' => $topRoomType ?? 'N/A'] as $label => $value) {
    $pdf->Cell(60, 8, "$label:", 0, 0);
    $pdf->Cell(30, 8, $value, 0, 1);
}
$pdf->Ln(8);

// Monthly Bookings Table
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, "Monthly Bookings", 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(60, 8, 'Month', 1, 0, 'C', 1);
$pdf->Cell(40, 8, 'Bookings', 1, 1, 'C', 1);

foreach ($months as $i => $month) {
    $pdf->Cell(60, 8, $month, 1);
    $pdf->Cell(40, 8, $monthlyReport[$i + 1] ?? 0, 1, 1, 'C');
}
$pdf->Ln(5);

// Room Type Breakdown
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, "Room Type Breakdown", 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(70, 8, 'Room Type', 1, 0, 'C', 1);
$pdf->Cell(40, 8, 'Total Rooms', 1, 0, 'C', 1);
$pdf->Cell(40, 8, 'Bookings', 1, 1, 'C', 1);

foreach ($roomTypeBreakdown as $type) {
    $pdf->Cell(70, 8, $type['type_name'], 1);
    $pdf->Cell(40, 8, $type['total_rooms'], 1, 0, 'C');
    $pdf->Cell(40, 8, $type['bookings'], 1, 1, 'C');
}

// Output
$pdf->Output("Annual_Report_$year.pdf", 'D');
exit;
