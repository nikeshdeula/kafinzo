<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-clipboard-data-fill text-primary me-2"></i>Balance Sheet</h4>
        <p>View your financial position — Assets, Liabilities, and Equity.</p>
    </div>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<?php
$typeLabels = [
    'asset'     => ['label' => 'Assets',      'icon' => 'bi-bank',              'color' => '#4361ee', 'bg' => '#eef0fd'],
    'liability' => ['label' => 'Liabilities', 'icon' => 'bi-exclamation-circle','color' => '#ef233c', 'bg' => '#fdedf0'],
    'equity'    => ['label' => 'Equity',       'icon' => 'bi-person-fill',       'color' => '#7209b7', 'bg' => '#f3e8fd'],
];
?>

<!-- KPI Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#4361ee">
            <h5>Total Assets</h5>
            <h2>NPR <?= number_format($data['totalAssets'], 2) ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#ef233c">
            <h5>Total Liabilities</h5>
            <h2>NPR <?= number_format($data['totalLiabilities'], 2) ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#7209b7">
            <h5>Total Equity</h5>
            <h2>NPR <?= number_format($data['totalEquity'], 2) ?></h2>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart -->
    <div class="col-md-5">
        <div class="card h-100 p-4">
            <h5 class="fw-bold mb-3">Assets vs Liabilities + Equity</h5>
            <canvas id="bsChart" height="200"></canvas>
        </div>
    </div>
    <!-- Balance Check -->
    <div class="col-md-7">
        <div class="card h-100 p-4">
            <h5 class="fw-bold mb-3">Balance Check</h5>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Total Assets</span>
                <span class="fw-600">NPR <?= number_format($data['totalAssets'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Total Liabilities</span>
                <span class="fw-600">NPR <?= number_format($data['totalLiabilities'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Total Equity</span>
                <span class="fw-600">NPR <?= number_format($data['totalEquity'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="fw-600">Liabilities + Equity</span>
                <span class="fw-600">NPR <?= number_format($data['totalLiabilitiesEquity'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between py-3 mt-2 rounded" style="background:<?= $data['isBalanced'] ? '#e6fdf7' : '#fdedf0' ?>;padding:1rem;">
                <span class="fw-700">Status</span>
                <span class="badge <?= $data['isBalanced'] ? 'bg-success' : 'bg-danger' ?>">
                    <?= $data['isBalanced'] ? 'Balanced' : 'Not Balanced' ?>
                </span>
            </div>
        </div>
    </div>
</div>

<?php foreach (['asset', 'liability', 'equity'] as $typeKey): 
    $groupKey = $typeKey === 'asset' ? 'assets' : ($typeKey === 'liability' ? 'liabilities' : 'equity');
    $group = $data[$groupKey] ?? ['accounts' => [], 'total' => 0];
    if (empty($group['accounts'])) continue;
    $meta = $typeLabels[$typeKey];
?>
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2 py-3" style="background:<?= $meta['bg'] ?>;border-bottom:2px solid <?= $meta['color'] ?>22;">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:<?= $meta['color'] ?>22;">
            <i class="bi <?= $meta['icon'] ?>" style="color:<?= $meta['color'] ?>;font-size:1.1rem;"></i>
        </div>
        <span class="fw-700" style="color:<?= $meta['color'] ?>;font-weight:700;font-size:1rem;"><?= $meta['label'] ?></span>
        <span class="badge ms-auto" style="background:<?= $meta['color'] ?>"><?= count($group['accounts']) ?> accounts</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:100px">Code</th>
                    <th>Account Name</th>
                    <th>Sub Type</th>
                    <th class="text-end">Opening Balance</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Credit</th>
                    <th class="text-end">Current Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($group['accounts'] as $acc): 
                    $debit = (float)$acc['total_debit'];
                    $credit = (float)$acc['total_credit'];
                    $opening = (float)$acc['opening_balance'];
                    $balance = $typeKey === 'asset' ? $opening + $debit - $credit : $credit - $debit - $opening;
                ?>
                <tr>
                    <td><code class="text-primary fw-bold"><?= htmlspecialchars($acc['code']) ?></code></td>
                    <td><?= htmlspecialchars($acc['name']) ?></td>
                    <td><span class="text-muted small"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $acc['sub_type'] ?? ''))) ?></span></td>
                    <td class="text-end">NPR <?= number_format($opening, 2) ?></td>
                    <td class="text-end">NPR <?= number_format($debit, 2) ?></td>
                    <td class="text-end">NPR <?= number_format($credit, 2) ?></td>
                    <td class="text-end fw-600">NPR <?= number_format($balance, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="fw-700 bg-light">
                    <td colspan="6" class="text-end">Total <?= $meta['label'] ?>:</td>
                    <td class="text-end" style="color:<?= $meta['color'] ?>">NPR <?= number_format($group['total'], 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php endforeach; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('bsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Assets', 'Liabilities', 'Equity', 'L + E'],
            datasets: [{
                label: 'Amount (NPR)',
                data: [
                    <?= $data['totalAssets'] ?>,
                    <?= $data['totalLiabilities'] ?>,
                    <?= $data['totalEquity'] ?>,
                    <?= $data['totalLiabilitiesEquity'] ?>
                ],
                backgroundColor: [
                    '#4361ee',
                    '#ef233c',
                    '#7209b7',
                    '#fb8500'
                ],
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return 'NPR ' + value.toLocaleString(); }
                    }
                }
            }
        }
    });
});
</script>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . 'views/layouts/app.php'; 
?>
