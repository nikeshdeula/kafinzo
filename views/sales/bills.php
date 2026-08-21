<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-receipt text-primary me-2"></i>Sales Bill</h4>
        <p>Record and manage sales bills for customers.</p>
    </div>
    <a href="/sales/bills/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New Bill</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="/sales/bills" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-600 small text-muted">Customer</label>
                <select name="customer_id" class="form-select form-select-sm">
                    <option value="">All Customers</option>
                    <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($customer_id ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600 small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="unpaid" <?= ($status ?? '') === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    <option value="partial" <?= ($status ?? '') === 'partial' ? 'selected' : '' ?>>Partial</option>
                    <option value="paid" <?= ($status ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="overdue" <?= ($status ?? '') === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                    <option value="cancelled" <?= ($status ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
            <div class="col-md-2">
                <a href="/sales/bills" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($bills)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-receipt fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No sales bills found. <a href="/sales/bills/create">Create your first bill.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Bill #</th><th>Date</th><th>Customer</th><th class="text-end">Total</th>
                <th class="text-end">TDS (1.5%)</th><th class="text-end">Grand Total</th>
                <th class="text-end">Paid</th><th class="text-end">Balance</th><th>Status</th><th style="width:110px">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($bills as $b): ?>
            <?php
                $tds = $b['total_amount'] * 0.015;
                $grandTotal = $b['total_amount'] - $tds;
                $balance = $grandTotal - $b['paid_amount'];
                $statusBadge = match($b['status']) {
                    'draft' => '<span class="badge bg-secondary-subtle text-secondary">Draft</span>',
                    'unpaid' => '<span class="badge bg-warning-subtle text-warning">Unpaid</span>',
                    'partial' => '<span class="badge bg-info-subtle text-info">Partial</span>',
                    'paid' => '<span class="badge bg-success-subtle text-success">Paid</span>',
                    'overdue' => '<span class="badge bg-danger-subtle text-danger">Overdue</span>',
                    'cancelled' => '<span class="badge bg-dark-subtle text-dark">Cancelled</span>',
                    default => '<span class="badge bg-light text-dark border">'.$b['status'].'</span>'
                };
            ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($b['bill_number']) ?></td>
                <td><?= nepali_date('d M Y', $b['bill_date']) ?></td>
                <td><?= htmlspecialchars($b['customer_name'] ?? '—') ?></td>
                <td class="text-end">NPR <?= number_format($b['total_amount'], 2) ?></td>
                <td class="text-end text-danger">NPR <?= number_format($tds, 2) ?></td>
                <td class="text-end fw-600 text-success">NPR <?= number_format($grandTotal, 2) ?></td>
                <td class="text-end">NPR <?= number_format($b['paid_amount'], 2) ?></td>
                <td class="text-end fw-600">NPR <?= number_format($balance, 2) ?></td>
                <td><?= $statusBadge ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/sales/bills/edit?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="/sales/bills/delete" class="d-inline" onsubmit="return confirm('Delete this bill?')">
                            <input type="hidden" name="id" value="<?= $b['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
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
