<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Good, <?= htmlspecialchars($userName ?? 'User') ?></h4>
    <button class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Create Invoice</button>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="kpi-card border-left-primary">
            <h5>Total Sales</h5>
            <h2>NPR 0.00</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card border-left-danger" style="border-left-color: var(--bs-danger) !important;">
            <h5>Total Expenses</h5>
            <h2>NPR 0.00</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card border-left-success" style="border-left-color: var(--bs-success) !important;">
            <h5>Net Profit</h5>
            <h2>NPR 0.00</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card border-left-info" style="border-left-color: var(--bs-info) !important;">
            <h5>Bank Balance</h5>
            <h2>NPR 0.00</h2>
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
                <a href="#" class="text-decoration-none small">View All</a>
            </div>
            
            <div class="text-center text-muted my-5">
                <i class="bi bi-inbox fs-1 mb-2"></i>
                <p>No recent transactions</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('mainChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales',
                data: [0, 0, 0, 0, 0, 0],
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Expenses',
                data: [0, 0, 0, 0, 0, 0],
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
