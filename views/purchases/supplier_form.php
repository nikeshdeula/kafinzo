<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-truck text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
        <p><?= isset($supplier) ? 'Update supplier information.' : 'Add a new supplier.' ?></p>
    </div>
    <a href="/purchases/suppliers" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:720px;"><div class="card-body p-4">
<?php $action = isset($supplier) ? '/purchases/suppliers/edit?id='.$supplier['id'] : '/purchases/suppliers/create'; ?>
<form action="<?= $action ?>" method="POST">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-600">Supplier Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($supplier['name'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label fw-600">Company Name</label>
            <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($supplier['company_name'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label fw-600">PAN Number</label>
            <input type="text" name="pan" class="form-control" value="<?= htmlspecialchars($supplier['pan'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label fw-600">VAT Number</label>
            <input type="text" name="vat_number" class="form-control" value="<?= htmlspecialchars($supplier['vat_number'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label fw-600">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($supplier['phone'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label fw-600">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($supplier['email'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label fw-600">Address</label>
            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($supplier['address'] ?? '') ?></textarea></div>
        <div class="col-md-6"><label class="form-label fw-600">Opening Balance (NPR)</label>
            <input type="number" name="opening_balance" step="0.01" class="form-control" value="<?= htmlspecialchars($supplier['opening_balance'] ?? '0') ?>"></div>
        <div class="col-md-6"><label class="form-label fw-600">Payment Terms (days)</label>
            <input type="number" name="payment_terms" class="form-control" value="<?= htmlspecialchars($supplier['payment_terms'] ?? '0') ?>"></div>
        <div class="col-md-4"><label class="form-label fw-600">Status</label>
            <select name="status" class="form-select">
                <option value="active" <?= ($supplier['status'] ?? 'active')==='active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($supplier['status'] ?? '')==='inactive' ? 'selected' : '' ?>>Inactive</option>
            </select></div>
        <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Supplier</button>
            <a href="/purchases/suppliers" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
</div></div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
