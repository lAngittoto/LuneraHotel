<?php

ob_start();
require_once 'header.php';
require_once __DIR__ . '/../../config/Helpers/amenityicon.php';

if (!isset($_SESSION['user'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}
?>

<div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-4xl font-extrabold text-gray-800 mb-6">Inventory</h1>

    <?php foreach (['Bedsheets'=>$bedsheets,'Toiletries'=>$toiletries] as $category=>$itemsList): ?>
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><?= $category ?></h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($itemsList as $item):
                    $used = $item['used_count'];
                    $maxUse = $item['max_use'];
                    $stock = $maxUse - $used;
                    $isRestock = $used >= 29;
                    $status = $isRestock ? 'need to restock' : 'good';
                ?>
                    <div class="bg-white rounded-2xl shadow p-6 border hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-gray-800 mb-2"><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="text-sm text-gray-500 mb-4">Category: <?= $category ?></p>

                        <div class="text-center mb-4">
                            <span class="text-3xl font-extrabold <?= $stock <=10 ? 'text-red-600':'text-green-600' ?>">
                                <?= $used ?>/<?= $maxUse ?>
                            </span>
                            <p class="text-xs mt-1 <?= $stock <=10 ? 'text-red-500':'text-gray-500' ?>">
                                <?= $stock <=10 ? 'Low Stock':'Available' ?>
                            </p>
                        </div>

                        <span class="inline-block px-3 py-1 mb-2 text-xs font-bold rounded-full
                            <?= $isRestock ? 'bg-red-100 text-red-700':'bg-green-100 text-green-700' ?>">
                            <?= strtoupper($status) ?>
                        </span>

                        <form method="POST">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button <?= !$isRestock ? 'disabled' : '' ?>
                                class="w-full py-2 rounded-lg font-semibold transition
                                <?= $isRestock ? 'bg-red-600 text-white hover:bg-red-700':'bg-gray-200 text-gray-400 cursor-not-allowed' ?>">
                                Restock
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Summary Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">Item Name</th>
                    <th class="px-6 py-4">Category</th>
                    <th class="px-6 py-4 text-center">Stock</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($summary as $s): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($s['name']) ?></td>
                        <td class="px-6 py-4"><?= $s['location'] ?></td>
                        <td class="px-6 py-4 text-center"><?= $s['stock'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
