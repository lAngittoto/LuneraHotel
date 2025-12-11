<?php 
$title = "Rooms";
ob_start();
require_once 'header.php';

if (!isset($_SESSION['user'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}
?>

<?php $year = $year ?? date('Y'); ?>
<h1>Annual Report - <?= htmlspecialchars($year) ?></h1>

<h2>Summary</h2>
<ul>
    <li>Total Rooms: <?= $summary['total_rooms'] ?? 0 ?></li>
    <li>Available: <?= $summary['available'] ?? 0 ?></li>
    <li>Booked: <?= $summary['booked'] ?? 0 ?></li>
    <li>Deactivated: <?= $summary['deactivated'] ?? 0 ?></li>
    <li>Total Bookings: <?= $summary['total_bookings'] ?? 0 ?></li>
</ul>

<a href="index.php?page=annualreport" class="btn">Refresh</a>
<a href="index.php?page=exportpdf&year=<?= urlencode($year) ?>" 
   target="_blank" 
   class="btn"
   style="padding:10px 20px;background:#800000;color:white;border-radius:8px;">
   Export to PDF
</a>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../App/layout.php";
?>
