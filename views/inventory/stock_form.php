<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div><h4><i class="bi bi-arrow-left-right text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
    <p>Record a stock movement for a product.</p></div>
    <a href="/inventory/stock-movement" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:720px;"><div class="card-body p-4">
<form action="/inventory/stock-movement/create" method="POST">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-600">Product <span class="text-danger">*</span></label>
            <select name="product_id" class="form-select" required>
                <option value="">— Select Product —</option>
                <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">Warehouse</label>
            <select name="warehouse_id" class="form-select">
                <option value="">— Select Warehouse —</option>
                <?php foreach ($warehouses as $w): ?>
                <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Reference Type <span class="text-danger">*</span></label>
            <select name="reference_type" class="form-select" required>
                <option value="Purchase">Purchase</option>
                <option value="Sale">Sale</option>
                <option value="Adjustment">Adjustment</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Reference ID</label>
            <input type="number" name="reference_id" class="form-control" placeholder="Optional">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Quantity Change <span class="text-danger">*</span></label>
            <input type="number" name="quantity_change" step="0.01" class="form-control" required placeholder="e.g. +50 or -20">
        </div>
        <div class="col-12">
            <label class="form-label fw-600">Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Reason or remarks..."></textarea>
        </div>
        <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Movement</button>
            <a href="/inventory/stock-movement" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
</div></div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
