<?php
// Using built-in Windows printing or simple HTML download
header('Content-Type: application/vnd.ms-word');
header("Content-Disposition: attachment; filename=Annual_Report_{$year}.doc");
header("Pragma: no-cache");
header("Expires: 0");
?>

<h1>Annual Report - <?= htmlspecialchars($year) ?></h1>

<h2>Summary</h2>
<p>Total Rooms: <?= $summary['total_rooms'] ?? 0 ?></p>
<p>Available: <?= $summary['available'] ?? 0 ?></p>
<p>Booked: <?= $summary['booked'] ?? 0 ?></p>
<p>Deactivated: <?= $summary['deactivated'] ?? 0 ?></p>
<p>Total Bookings: <?= $summary['total_bookings'] ?? 0 ?></p>

<h2>Bookings</h2>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr>
        <th>ID</th>
        <th>Room</th>
        <th>Email</th>
        <th>Status</th>
        <th>Date</th>
    </tr>

    <?php foreach ($bookings as $b): ?>
    <tr>
        <td><?= htmlspecialchars($b['id']) ?></td>
        <td><?= htmlspecialchars($b['room_number']) ?></td>
        <td><?= htmlspecialchars($b['user_email']) ?></td>
        <td><?= htmlspecialchars($b['status']) ?></td>
        <td><?= htmlspecialchars($b['booking_date']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
