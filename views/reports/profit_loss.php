<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-graph-up-arrow text-primary me-2"></i>Profit & Loss</h4>
        <p>View your income statement — Revenue, Expenses, and Net Profit.</p>
    </div>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<!-- KPI Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#06d6a0">
            <h5>Total Income</h5>
            <h2>NPR <?= number_format($data['totalIncome'], 2) ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#ef233c">
            <h5>Total Expenses</h5>
            <h2>NPR <?= number_format($data['totalExpenses'], 2) ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:<?= $data['netProfit'] >= 0 ? '#06d6a0' : '#ef233c' ?>">
            <h5>Net Profit</h5>
            <h2>NPR <?= number_format($data['netProfit'], 2) ?></h2>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart -->
    <div class="col-md-5">
        <div class="card h-100 p-4">
            <h5 class="fw-bold mb-3">Income vs Expenses</h5>
            <canvas id="plChart" height="200"></canvas>
        </div>
    </div>
    <!-- Summary -->
    <div class="col-md-7">
        <div class="card h-100 p-4">
            <h5 class="fw-bold mb-3">Summary</h5>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Total Revenue</span>
                <span class="fw-600">NPR <?= number_format($data['totalIncome'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Total Expenses</span>
                <span class="fw-600 text-danger">NPR <?= number_format($data['totalExpenses'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="fw-700">Net Profit / (Loss)</span>
                <span class="fw-700" style="color:<?= $data['netProfit'] >= 0 ? '#06d6a0' : '#ef233c' ?>">NPR <?= number_format($data['netProfit'], 2) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Accounts -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2 py-3" style="background:#e6fdf7;border-bottom:2px solid #06d6a022;">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#06d6a022;">
            <i class="bi bi-graph-up-arrow" style="color:#06d6a0;font-size:1.1rem;"></i>
        </div>
        <span class="fw-700" style="color:#06d6a0;font-weight:700;font-size:1rem;">Revenue</span>
        <span class="badge ms-auto" style="background:#06d6a0">Income</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($data['revenueAccounts'])): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No revenue accounts found.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th style="width:100px">Code</th><th>Account Name</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead>
            <tbody>
            <?php foreach ($data['revenueAccounts'] as $acc): 
                $balance = max(0, (float)$acc['total_credit'] - (float)$acc['total_debit']);
            ?>
            <tr>
                <td><code class="text-primary fw-bold"><?= htmlspecialchars($acc['code']) ?></code></td>
                <td><?= htmlspecialchars($acc['name']) ?></td>
                <td class="text-end">NPR <?= number_format($acc['total_debit'], 2) ?></td>
                <td class="text-end">NPR <?= number_format($acc['total_credit'], 2) ?></td>
                <td class="text-end fw-600">NPR <?= number_format($balance, 2) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="fw-700 bg-light">
                    <td colspan="4" class="text-end">Total Revenue:</td>
                    <td class="text-end" style="color:#06d6a0">NPR <?= number_format($data['totalIncome'], 2) ?></td>
                </tr>
            </tfoot>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Expense Accounts -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2 py-3" style="background:#fff3e6;border-bottom:2px solid #fb850022;">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#fb850022;">
            <i class="bi bi-wallet2" style="color:#fb8500;font-size:1.1rem;"></i>
        </div>
        <span class="fw-700" style="color:#fb8500;font-weight:700;font-size:1rem;">Expenses</span>
        <span class="badge ms-auto" style="background:#fb8500">Costs</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($data['expenseAccounts'])): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No expense accounts found.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th style="width:100px">Code</th><th>Account Name</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead>
            <tbody>
            <?php foreach ($data['expenseAccounts'] as $acc): 
                $balance = max(0, (float)$acc['total_debit'] - (float)$acc['total_credit']);
            ?>
            <tr>
                <td><code class="text-primary fw-bold"><?= htmlspecialchars($acc['code']) ?></code></td>
                <td><?= htmlspecialchars($acc['name']) ?></td>
                <td class="text-end">NPR <?= number_format($acc['total_debit'], 2) ?></td>
                <td class="text-end">NPR <?= number_format($acc['total_credit'], 2) ?></td>
                <td class="text-end fw-600">NPR <?= number_format($balance, 2) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="fw-700 bg-light">
                    <td colspan="4" class="text-end">Total Expenses:</td>
                    <td class="text-end" style="color:#ef233c">NPR <?= number_format($data['totalExpenses'], 2) ?></td>
                </tr>
            </tfoot>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Net Result -->
<div class="card">
    <div class="card-body <?= $data['netProfit'] >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-uppercase mb-1 opacity-75">Net <?= $data['netProfit'] >= 0 ? 'Profit' : 'Loss' ?></h6>
                <h3 class="fw-700 mb-0">NPR <?= number_format(abs($data['netProfit']), 2) ?></h3>
            </div>
            <i class="bi bi-<?= $data['netProfit'] >= 0 ? 'arrow-up-circle-fill' : 'arrow-down-circle-fill' ?>" style="font-size:3rem;opacity:.3"></i>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('plChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Income', 'Expenses', 'Net Profit'],
            datasets: [{
                label: 'Amount (NPR)',
                data: [<?= $data['totalIncome'] ?>, <?= $data['totalExpenses'] ?>, <?= $data['netProfit'] ?>],
                    backgroundColor: [
                        '#06d6a0',
                        '#ef233c',
                        "<?= $data['netProfit'] >= 0 ? '#4361ee' : '#fb8500' ?>"
                    ],
                borderRadius: 8,
                barThickness: 50
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
