<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Good, <?= htmlspecialchars($userName ?? 'User') ?></h4>
    <a href="/sales/bills/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New Sale Bill</a>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="kpi-card border-left-primary">
            <h5>Total Sales</h5>
            <h2>NPR <?= number_format($totalSales, 2) ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card border-left-danger" style="border-left-color: var(--bs-danger) !important;">
            <h5>Total Expenses</h5>
            <h2>NPR <?= number_format($totalExpenses, 2) ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card border-left-success" style="border-left-color: var(--bs-success) !important;">
            <h5>Net Profit</h5>
            <h2 style="color:<?= $netProfit >= 0 ? 'inherit' : 'var(--bs-danger)' ?>">NPR <?= number_format($netProfit, 2) ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card border-left-info" style="border-left-color: var(--bs-info) !important;">
            <h5>Bank Balance</h5>
            <h2>NPR <?= number_format($bankBalance, 2) ?></h2>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart Area -->
    <div class="col-md-8">
        <div class="card h-100 p-4">
            <h5 class="fw-bold mb-4">Sales & Expenses Overview</h5>
            <canvas id="mainChart" height="100"></canvas>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-md-4">
        <div class="card h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Recent Transactions</h5>
                <a href="/sales/bills" class="text-decoration-none small">View All</a>
            </div>

            <?php if (empty($recent)): ?>
            <div class="text-center text-muted my-5">
                <i class="bi bi-inbox fs-1 mb-2"></i>
                <p>No recent transactions</p>
            </div>
            <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($recent as $t): ?>
                <div class="list-group-item px-0 d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:34px;height:34px;background:<?= $t['kind'] === 'sale' ? 'rgba(67,97,238,.12)' : 'rgba(239,35,60,.12)' ?>;">
                        <i class="bi <?= $t['kind'] === 'sale' ? 'bi-cart3 text-primary' : 'bi-receipt text-danger' ?>"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-600 text-truncate" style="font-size:.9rem;"><?= htmlspecialchars($t['party']) ?></div>
                        <div class="text-muted" style="font-size:.75rem;">
                            <?= $t['kind'] === 'sale' ? 'Bill' : 'Expense' ?> #<?= htmlspecialchars($t['ref']) ?> &middot; <?= nepali_date('Y-m-d', $t['txn_date']) ?>
                        </div>
                    </div>
                    <div class="fw-700 flex-shrink-0" style="font-size:.85rem;color:<?= $t['kind'] === 'sale' ? '#06d6a0' : '#ef233c' ?>">
                        <?= $t['kind'] === 'sale' ? '+' : '-' ?><?= number_format($t['amount'], 2) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('mainChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Sales',
                data: <?= json_encode($chartSales) ?>,
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Expenses',
                data: <?= json_encode($chartExpenses) ?>,
                borderColor: '#ef233c',
                backgroundColor: 'transparent',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
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
