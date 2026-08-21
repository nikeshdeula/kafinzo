<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-percent text-primary me-2"></i>Tax Settings</h4>
        <p>Configure default VAT and PAN formats.</p>
    </div>
    <button type="submit" form="taxForm" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Changes</button>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:720px;">
<div class="card-body p-4">
<form action="/settings/tax" method="POST" id="taxForm">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-600">Default Tax Name</label>
            <input type="text" name="tax_name" class="form-control" value="<?= htmlspecialchars($tax_name ?? 'VAT') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">Default Tax Rate (%)</label>
            <input type="number" step="0.01" name="tax_rate" class="form-control" value="<?= htmlspecialchars($tax_rate ?? '13') ?>">
            <div class="form-text">This rate is applied globally to all transactions.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">PAN Number Format</label>
            <input type="text" name="pan_format" class="form-control" value="<?= htmlspecialchars($pan_format ?? 'XXXXXXXXX') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">VAT Number Format</label>
            <input type="text" name="vat_format" class="form-control" value="<?= htmlspecialchars($vat_format ?? 'XXXXXXXXX') ?>">
        </div>
    </div>
</form>
</div></div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
