<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-credit-card text-primary me-2"></i>Supplier Payments</h4>
        <p>Record and track payments made to suppliers.</p>
    </div>
    <a href="/purchases/payments/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Record Payment</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#4361ee">
            <h5>Total Payments</h5>
            <h2>NPR <?= number_format(array_sum(array_column($payments, 'amount')), 2) ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#ef233c">
            <h5>This Month</h5>
            <h2>NPR <?= number_format(array_sum(array_map(function($p) {
                $bs = ad_to_bs($p['payment_date']);
                return ($bs && $bs['year'] == current_nepali_date('Y') && $bs['month'] == current_nepali_date('n')) ? $p['amount'] : 0;
            }, $payments)), 2) ?></h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($payments)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-credit-card fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No payments recorded yet. <a href="/purchases/payments/create">Record your first payment.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Payment #</th><th>Date</th><th>Supplier</th><th>Branch</th><th>Address</th><th>Bill / Order</th>
                <th class="text-end">Amount</th><th>Method</th><th>Reference</th>
            </tr></thead>
            <tbody>
            <?php foreach ($payments as $p): ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($p['payment_number']) ?></td>
                <td><?= nepali_date('d M Y', $p['payment_date']) ?></td>
                <td><?= htmlspecialchars($p['supplier_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($p['supplier_branch'] ?? '—') ?></td>
                <td><?= htmlspecialchars($p['supplier_address'] ?? '—') ?></td>
                <td>
                    <?php if ($p['bill_id']): ?>
                        <a href="/purchases/bills/edit?id=<?= $p['bill_id'] ?>">Bill #<?= $p['bill_id'] ?></a>
                    <?php elseif ($p['order_id']): ?>
                        <a href="/purchases/orders/edit?id=<?= $p['order_id'] ?>">Order #<?= $p['order_id'] ?></a>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td class="text-end fw-600">NPR <?= number_format($p['amount'], 2) ?></td>
                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars(ucfirst($p['payment_method'])) ?></span></td>
                <td><code><?= htmlspecialchars($p['reference'] ?? '—') ?></code></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
