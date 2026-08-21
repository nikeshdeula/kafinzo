<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-water text-primary me-2"></i>Cash Flow</h4>
        <p>View your cash movement across Operating, Investing, and Financing activities.</p>
    </div>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<!-- KPI Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="kpi-card" style="border-left-color:#06d6a0">
            <h5>Operating</h5>
            <h2 class="<?= $data['netOperating'] >= 0 ? 'text-success' : 'text-danger' ?>">
                NPR <?= number_format($data['netOperating'], 2) ?>
            </h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card" style="border-left-color:#fb8500">
            <h5>Investing</h5>
            <h2 class="<?= $data['netInvesting'] >= 0 ? 'text-success' : 'text-danger' ?>">
                NPR <?= number_format($data['netInvesting'], 2) ?>
            </h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card" style="border-left-color:#7209b7">
            <h5>Financing</h5>
            <h2 class="<?= $data['netFinancing'] >= 0 ? 'text-success' : 'text-danger' ?>">
                NPR <?= number_format($data['netFinancing'], 2) ?>
            </h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card" style="border-left-color:#4361ee">
            <h5>Net Change</h5>
            <h2 class="<?= $data['netChange'] >= 0 ? 'text-success' : 'text-danger' ?>">
                NPR <?= number_format($data['netChange'], 2) ?>
            </h2>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart -->
    <div class="col-md-5">
        <div class="card h-100 p-4">
            <h5 class="fw-bold mb-3">Cash Flow Overview</h5>
            <canvas id="cfChart" height="200"></canvas>
        </div>
    </div>
    <!-- Bank Summary -->
    <div class="col-md-7">
        <div class="card h-100 p-4">
            <h5 class="fw-bold mb-3">Bank Transactions Summary</h5>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Total Deposits</span>
                <span class="fw-600 text-success">+NPR <?= number_format($data['totalDeposits'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Total Withdrawals</span>
                <span class="fw-600 text-danger">-NPR <?= number_format($data['totalWithdrawals'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="fw-700">Net Change in Cash</span>
                <span class="fw-700" style="color:<?= $data['netChange'] >= 0 ? '#06d6a0' : '#ef233c' ?>">
                    NPR <?= number_format($data['netChange'], 2) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Operating Activities -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2 py-3" style="background:#e6fdf7;border-bottom:2px solid #06d6a022;">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#06d6a022;">
            <i class="bi bi-arrow-left-right" style="color:#06d6a0;font-size:1.1rem;"></i>
        </div>
        <span class="fw-700" style="color:#06d6a0;font-weight:700;font-size:1rem;">Operating Activities</span>
        <span class="badge ms-auto" style="background:#06d6a0">Day-to-day</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Description</th><th class="text-end">Inflow</th><th class="text-end">Outflow</th></tr></thead>
            <tbody>
                <tr>
                    <td>Cash received from customers (paid invoices)</td>
                    <td class="text-end text-success fw-600">NPR <?= number_format($data['operatingInflow'], 2) ?></td>
                    <td class="text-end">—</td>
                </tr>
                <tr>
                    <td>Cash paid for operating expenses</td>
                    <td class="text-end">—</td>
                    <td class="text-end text-danger fw-600">NPR <?= number_format($data['operatingOutflow'], 2) ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="fw-700 bg-light">
                    <td class="text-end">Net Cash from Operating Activities</td>
                    <td colspan="2" class="text-end" style="color:<?= $data['netOperating'] >= 0 ? '#06d6a0' : '#ef233c' ?>">
                        NPR <?= number_format($data['netOperating'], 2) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Investing Activities -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2 py-3" style="background:#fff3e6;border-bottom:2px solid #fb850022;">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#fb850022;">
            <i class="bi bi-box-seam" style="color:#fb8500;font-size:1.1rem;"></i>
        </div>
        <span class="fw-700" style="color:#fb8500;font-weight:700;font-size:1rem;">Investing Activities</span>
        <span class="badge ms-auto" style="background:#fb8500">Long-term</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Description</th><th class="text-end">Inflow</th><th class="text-end">Outflow</th></tr></thead>
            <tbody>
                <tr>
                    <td class="text-muted">Purchase of assets / investments</td>
                    <td class="text-end">—</td>
                    <td class="text-end text-muted">—</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="fw-700 bg-light">
                    <td class="text-end">Net Cash from Investing Activities</td>
                    <td colspan="2" class="text-end text-muted">NPR 0.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Financing Activities -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2 py-3" style="background:#f3e8fd;border-bottom:2px solid #7209b722;">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#7209b722;">
            <i class="bi bi-bank2" style="color:#7209b7;font-size:1.1rem;"></i>
        </div>
        <span class="fw-700" style="color:#7209b7;font-weight:700;font-size:1rem;">Financing Activities</span>
        <span class="badge ms-auto" style="background:#7209b7">Capital</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Description</th><th class="text-end">Inflow</th><th class="text-end">Outflow</th></tr></thead>
            <tbody>
                <tr>
                    <td>Deposits beyond invoice income (loans / equity)</td>
                    <td class="text-end text-success fw-600">NPR <?= number_format($data['financingInflow'], 2) ?></td>
                    <td class="text-end">—</td>
                </tr>
                <tr>
                    <td>Withdrawals beyond operating expenses</td>
                    <td class="text-end">—</td>
                    <td class="text-end text-danger fw-600">NPR <?= number_format($data['financingOutflow'], 2) ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="fw-700 bg-light">
                    <td class="text-end">Net Cash from Financing Activities</td>
                    <td colspan="2" class="text-end" style="color:<?= $data['netFinancing'] >= 0 ? '#06d6a0' : '#ef233c' ?>">
                        NPR <?= number_format($data['netFinancing'], 2) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Net Change -->
<div class="card">
    <div class="card-body <?= $data['netChange'] >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
        <div class="row g-3">
            <div class="col-md-4">
                <h6 class="text-uppercase mb-1 opacity-75">Operating</h6>
                <h4 class="fw-700 mb-0">NPR <?= number_format($data['netOperating'], 2) ?></h4>
            </div>
            <div class="col-md-4">
                <h6 class="text-uppercase mb-1 opacity-75">Investing</h6>
                <h4 class="fw-700 mb-0">NPR <?= number_format($data['netInvesting'], 2) ?></h4>
            </div>
            <div class="col-md-4 text-md-end">
                <h6 class="text-uppercase mb-1 opacity-75">Net Change in Cash</h6>
                <h4 class="fw-700 mb-0">NPR <?= number_format($data['netChange'], 2) ?></h4>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('cfChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Operating', 'Investing', 'Financing', 'Net Change'],
            datasets: [{
                label: 'Amount (NPR)',
                data: [
                    <?= $data['netOperating'] ?>,
                    <?= $data['netInvesting'] ?>,
                    <?= $data['netFinancing'] ?>,
                    <?= $data['netChange'] ?>
                ],
                backgroundColor: [
                    '#06d6a0',
                    '#fb8500',
                    '#7209b7',
                    '#4361ee'
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
