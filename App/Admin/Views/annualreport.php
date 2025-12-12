<?php 
$title = "Annual Report";
ob_start();
require_once 'header.php';

if (!isset($_SESSION['user'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}

// Set the selected year or default to current year
$year = $_GET['year'] ?? date('Y');

// Example: allow last 5 years in dropdown
$years = range(date('Y'), date('Y') - 5);

$months = [
    "January","February","March","April","May","June",
    "July","August","September","October","November","December"
];

$summary = $summary ?? [
    'total_rooms' => 0,
    'available' => 0,
    'booked' => 0,
    'deactivated' => 0
];
$monthlyReport = $monthlyReport ?? array_fill(1,12,0);
$roomTypeBreakdown = $roomTypeBreakdown ?? [];
$popularRoomType = $popularRoomType ?? 'N/A';
?>
<div class="p-6 flex justify-center">
    <div class="w-full max-w-6xl">

        <!-- Top Controls: Refresh + Year Selector + Print + Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center  gap-4 mb-25">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <!-- Refresh Button -->
                <a href="index.php?page=annualreport" 
                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-medium shadow hover:shadow-lg transition-all">
                    Refresh
                </a>

                <!-- Year Selector -->
                <form method="get" action="index.php" class="flex items-center gap-2">
                    <input type="hidden" name="page" value="annualreport">
                    <select name="year" onchange="this.form.submit()" 
                        class="px-4 py-2 border rounded-xl shadow-sm text-sm">
                        <?php foreach($years as $y): ?>
                            <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-gray-600 text-sm">Select Year</span>
                </form>

                <!-- Print Dropdown -->
                <div class="relative ml-4 mt-2 sm:mt-0 ">
                    <button id="printDropdownBtn" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium shadow hover:shadow-lg transition-all">
                        Print
                    </button>
                    <div id="printDropdown" class="hidden absolute right-0 mt-2 w-52 bg-white border rounded-2xl shadow-lg z-50">
                        <a href="#" data-print="all" class="block px-4 py-2 hover:bg-gray-100">All</a>
                        <a href="#" data-print="monthly" class="block px-4 py-2 hover:bg-gray-100">Monthly Bookings</a>
                        <a href="#" data-print="breakdown" class="block px-4 py-2 hover:bg-gray-100">Room Type Breakdown</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add some top space so dropdown doesn't overlap content -->
        <div class="h-6"></div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-6 mb-8 mt-4">
            <?php
            $cards = [
                ["Total Rooms", $summary['total_rooms'], "bg-white", "text-gray-800"],
                ["Available", $summary['available'], "bg-green-50", "text-green-700"],
                ["Currently Booked", $summary['booked'], "bg-yellow-50", "text-yellow-700"],
                ["Out of Order", $summary['deactivated'], "bg-red-50", "text-red-700"],
                ["Popular Room Type", $popularRoomType, "bg-blue-50", "text-blue-700"]
            ];
            foreach ($cards as $c):
            ?>
            <div class="<?= $c[2] ?> shadow-lg rounded-2xl p-6 text-center hover:scale-105 transition-transform duration-300">
                <h3 class="<?= $c[3] ?> text-sm font-medium mb-1"><?= $c[0] ?></h3>
                <p class="text-3xl font-bold"><?= htmlspecialchars($c[1]) ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Occupancy Rate -->
        <div class="bg-purple-50 shadow-lg rounded-2xl p-6 mb-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-center sm:text-left">
                <h3 class="text-purple-700 text-sm font-medium mb-1">Overall Occupancy Rate</h3>
                <?php 
                    $occupancy = $summary['total_rooms'] > 0 ? ($summary['booked'] / $summary['total_rooms']) * 100 : 0;
                ?>
                <p class="text-3xl font-extrabold text-purple-700"><?= number_format($occupancy, 1) ?>%</p>
            </div>
            <div class="w-full sm:w-2/3 bg-purple-200 rounded-full h-5">
                <div class="bg-purple-700 h-5 rounded-full transition-all duration-500" style="width: <?= $occupancy ?>%;"></div>
            </div>
        </div>

        <!-- Monthly Breakdown Chart -->
        <div class="mb-8 bg-white shadow-lg rounded-2xl p-6" id="monthlySection">
            <h2 class="text-xl font-semibold text-gray-800 mb-3">Monthly Bookings</h2>
            <canvas id="monthlyChart"></canvas>
        </div>

        <!-- Room Type Breakdown -->
        <div class="mb-8 bg-white shadow-lg rounded-2xl p-6 overflow-x-auto" id="breakdownSection">
            <h2 class="text-xl font-semibold text-gray-800 mb-3">Room Type Breakdown</h2>
            <table class="min-w-full text-sm border divide-y divide-gray-200">
                <thead class="bg-gray-100 text-gray-600 border-b">
                    <tr>
                        <th class="px-4 py-2 text-left">Room Type</th>
                        <th class="px-4 py-2 text-left">Total Rooms</th>
                        <th class="px-4 py-2 text-left">Bookings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($roomTypeBreakdown)): ?>
                    <?php foreach ($roomTypeBreakdown as $type): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2"><?= htmlspecialchars($type['type_name'] ?? '') ?></td>
                        <td class="px-4 py-2 font-semibold"><?= $type['total_rooms'] ?? 0 ?></td>
                        <td class="px-4 py-2 font-semibold"><?= $type['bookings'] ?? 0 ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr><td colspan="3" class="px-4 py-2 text-center">No data available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Bookings',
            data: <?= json_encode(array_values($monthlyReport)) ?>,
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, stepSize: 1 } }
    }
});

// Print Dropdown Logic
const dropdownBtn = document.getElementById('printDropdownBtn');
const dropdown = document.getElementById('printDropdown');

dropdownBtn.addEventListener('click', () => {
    dropdown.classList.toggle('hidden');
});

dropdown.querySelectorAll('a').forEach(item => {
    item.addEventListener('click', e => {
        e.preventDefault();
        const type = item.dataset.print;

        // Auto-scroll
        let target = document.getElementById('monthlySection');
        if(type === 'breakdown') target = document.getElementById('breakdownSection');

        target.scrollIntoView({ behavior: 'smooth', block: 'start' });

        if(type === 'all') {
            document.getElementById('monthlySection').style.display = 'block';
            document.getElementById('breakdownSection').style.display = 'block';
        } else if(type === 'monthly') {
            document.getElementById('monthlySection').style.display = 'block';
            document.getElementById('breakdownSection').style.display = 'none';
        } else if(type === 'breakdown') {
            document.getElementById('monthlySection').style.display = 'none';
            document.getElementById('breakdownSection').style.display = 'block';
        }

        window.print();

        setTimeout(() => {
            document.getElementById('monthlySection').style.display = 'block';
            document.getElementById('breakdownSection').style.display = 'block';
        }, 500);
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../App/layout.php";
?>
