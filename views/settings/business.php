<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-building text-primary me-2"></i>Business Settings</h4>
        <p>Manage your business information and settings.</p>
    </div>
    <button type="submit" form="businessForm" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Changes</button>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:720px;">
<div class="card-body p-4">
<form action="/settings/business" method="POST" id="businessForm">
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label fw-600">Business Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($business['name'] ?? '') ?>">
        </div>
        <div class="col-12">
            <label class="form-label fw-600">Address</label>
            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($business['address'] ?? '') ?></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($business['phone'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($business['email'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">PAN Number</label>
            <input type="text" name="pan" class="form-control" value="<?= htmlspecialchars($business['pan_number'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">VAT Number</label>
            <input type="text" name="vat" class="form-control" value="<?= htmlspecialchars($business['vat_number'] ?? '') ?>">
        </div>
    </div>
</form>
</div></div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
