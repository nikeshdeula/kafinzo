<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-arrow-left-right text-primary me-2"></i>Stock Movements</h4>
        <p>Track incoming and outgoing stock across warehouses.</p>
    </div>
    <a href="/inventory/stock-movement/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Record Movement</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<form method="GET" action="/inventory/stock-movement" class="card mb-3">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-600 small">Product</label>
                <select name="product_id" class="form-select">
                    <option value="">All Products</option>
                    <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($filters['product_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600 small">Reference Type</label>
                <select name="reference_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="Purchase" <?= ($filters['reference_type'] ?? '') === 'Purchase' ? 'selected' : '' ?>>Purchase</option>
                    <option value="Sale" <?= ($filters['reference_type'] ?? '') === 'Sale' ? 'selected' : '' ?>>Sale</option>
                    <option value="Adjustment" <?= ($filters['reference_type'] ?? '') === 'Adjustment' ? 'selected' : '' ?>>Adjustment</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-600 small">From</label>
                <?= nepali_date_picker('from_date', $filters['from_date'] ?? '', 'From') ?>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-600 small">To</label>
                <?= nepali_date_picker('to_date', $filters['to_date'] ?? '', 'To') ?>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($movements)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-arrow-left-right fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No stock movements found.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Date</th><th>Reference</th><th>Product</th><th>Warehouse</th>
                <th class="text-end">Qty Change</th><th>Notes</th>
            </tr></thead>
            <tbody>
            <?php foreach ($movements as $m): ?>
            <tr>
                <td class="small text-muted"><?= nepali_date('d M Y H:i', $m['created_at']) ?></td>
                <td>
                    <?php
                        $badge = match($m['reference_type']) {
                            'Purchase'   => 'bg-success-subtle text-success',
                            'Sale'       => 'bg-danger-subtle text-danger',
                            'Adjustment' => 'bg-warning-subtle text-warning',
                            default      => 'bg-secondary-subtle text-secondary',
                        };
                    ?>
                    <span class="badge <?= $badge ?>"><?= htmlspecialchars($m['reference_type']) ?></span>
                    <?php if ($m['reference_id']): ?><small class="text-muted">#<?= $m['reference_id'] ?></small><?php endif; ?>
                </td>
                <td class="fw-600"><?= htmlspecialchars($m['product_name'] ?? 'Deleted Product') ?></td>
                <td><?= htmlspecialchars($m['warehouse_name'] ?? '—') ?></td>
                <td class="text-end fw-600 <?= $m['quantity_change'] > 0 ? 'text-success' : ($m['quantity_change'] < 0 ? 'text-danger' : '') ?>">
                    <?= ($m['quantity_change'] > 0 ? '+' : '') . number_format($m['quantity_change'], 2) ?>
                </td>
                <td class="small text-muted"><?= htmlspecialchars($m['notes'] ?? '—') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
