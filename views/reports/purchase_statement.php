<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-cart-fill text-primary me-2"></i><?= htmlspecialchars($pageTitle) ?></h4>
        <p><?= htmlspecialchars($pageDesc) ?></p>
    </div>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="/reports/purchase-statement" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-600 small text-muted">From Date</label>
                <?= nepali_date_picker('from', $from ?? '', 'From', ['class' => 'form-control form-control-sm', 'placeholder' => 'YYYY-MM-DD']) ?>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600 small text-muted">To Date</label>
                <?= nepali_date_picker('to', $to ?? '', 'To', ['class' => 'form-control form-control-sm', 'placeholder' => 'YYYY-MM-DD']) ?>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600 small text-muted">Supplier</label>
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($supplier_id ?? '') == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
            <div class="col-md-2">
                <a href="/reports/purchase-statement" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
            <div class="col-md-2">
                <a href="/reports/purchase-statement/export?from=<?= urlencode($from ?? '') ?>&to=<?= urlencode($to ?? '') ?>&supplier_id=<?= urlencode($supplier_id ?? '') ?>" class="btn btn-outline-success btn-sm w-100"><i class="bi bi-file-earmark-excel me-1"></i> Export Excel</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-600 mb-1">Total Purchases</div>
                <div class="h4 mb-0 text-primary">NPR <?= number_format($summary['total_purchases'], 2) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-600 mb-1">Total Payments</div>
                <div class="h4 mb-0 text-success">NPR <?= number_format($summary['total_payments'], 2) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-600 mb-1">Outstanding</div>
                <div class="h4 mb-0 text-warning">NPR <?= number_format($summary['total_outstanding'], 2) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($rows)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-cart-fill fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No transactions found for the selected filters.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Date</th><th>Type</th><th>Ref #</th><th>Supplier</th>
                <th class="text-end">Subtotal</th><th class="text-end">Tax</th><th class="text-end">Discount</th>
                <th class="text-end">Total</th><th class="text-end">Paid</th><th>Status</th>
            </tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
            <?php
                $typeBadge = match($r['type']) {
                    'bill' => '<span class="badge bg-primary-subtle text-primary">Bill</span>',
                    'order' => '<span class="badge bg-info-subtle text-info">Order</span>',
                    'payment' => '<span class="badge bg-success-subtle text-success">Payment</span>',
                    default => '<span class="badge bg-light text-dark border">'.$r['type'].'</span>'
                };
                $statusBadge = match($r['status']) {
                    'paid' => '<span class="badge bg-success-subtle text-success">Paid</span>',
                    'partial' => '<span class="badge bg-info-subtle text-info">Partial</span>',
                    'unpaid' => '<span class="badge bg-warning-subtle text-warning">Unpaid</span>',
                    'overdue' => '<span class="badge bg-danger-subtle text-danger">Overdue</span>',
                    'completed' => '<span class="badge bg-success-subtle text-success">Completed</span>',
                    default => '<span class="badge bg-light text-dark border">'.ucfirst($r['status']).'</span>'
                };
            ?>
            <tr>
                <td><?= nepali_date('d M Y', $r['date']) ?></td>
                <td><?= $typeBadge ?></td>
                <td class="fw-600"><?= htmlspecialchars($r['ref_number']) ?></td>
                <td><?= htmlspecialchars($r['party_name'] ?? '—') ?></td>
                <td class="text-end">NPR <?= number_format($r['subtotal'], 2) ?></td>
                <td class="text-end">NPR <?= number_format($r['tax_amount'], 2) ?></td>
                <td class="text-end">NPR <?= number_format($r['discount_amount'], 2) ?></td>
                <td class="text-end fw-600">NPR <?= number_format($r['total_amount'], 2) ?></td>
                <td class="text-end">NPR <?= number_format($r['paid_amount'], 2) ?></td>
                <td><?= $statusBadge ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
