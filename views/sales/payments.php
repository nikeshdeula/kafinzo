<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-cash-coin text-primary me-2"></i>Payments Received</h4>
        <p>Track payments received from customers against invoices.</p>
    </div>
    <a href="/sales/payments/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Record Payment</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<?php
$totalReceived = 0;
foreach ($payments as $p) { $totalReceived += $p['amount']; }
?>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-600 mb-1">Total Received</div>
                <div class="h4 mb-0 text-success">NPR <?= number_format($totalReceived, 2) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-600 mb-1">Transactions</div>
                <div class="h4 mb-0"><?= count($payments) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($payments)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-cash-coin fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No payments recorded yet. <a href="/sales/payments/create">Record your first payment.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>#</th><th>Invoice #</th><th>Customer</th><th>Date</th>
                <th>Method</th><th>Reference</th><th class="text-end">Amount (NPR)</th><th style="width:100px">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($payments as $idx => $p): ?>
            <tr>
                <td><?= $idx + 1 ?></td>
                <td class="fw-600"><?= htmlspecialchars($p['invoice_number'] ?? '—') ?></td>
                <td><?= htmlspecialchars($p['customer_name']) ?></td>
                <td><?= htmlspecialchars($p['payment_date']) ?></td>
                <td>
                    <?php
                    $method = $p['payment_method'];
                    $badgeClass = 'bg-secondary-subtle text-secondary';
                    if ($method === 'cash') $badgeClass = 'bg-success-subtle text-success';
                    elseif ($method === 'bank') $badgeClass = 'bg-info-subtle text-info';
                    elseif ($method === 'cheque') $badgeClass = 'bg-warning-subtle text-warning';
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= ucfirst($method) ?></span>
                </td>
                <td><code><?= htmlspecialchars($p['reference_number'] ?? '—') ?></code></td>
                <td class="text-end fw-600 text-success">NPR <?= number_format($p['amount'], 2) ?></td>
                <td>
                    <a href="/sales/payments/view?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
