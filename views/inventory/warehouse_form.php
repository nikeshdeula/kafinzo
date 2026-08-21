<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div><h4><i class="bi bi-building text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
    <p><?= isset($warehouse) ? 'Update warehouse details.' : 'Add a new warehouse location.' ?></p></div>
    <a href="/inventory/warehouses" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:640px;"><div class="card-body p-4">
<form action="<?= isset($warehouse) ? '/inventory/warehouses/edit?id=' . $warehouse['id'] : '/inventory/warehouses/create' ?>" method="POST">
    <div class="row g-3">
        <div class="col-12"><label class="form-label fw-600">Warehouse Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($warehouse['name'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label fw-600">Location</label>
            <input type="text" name="location" class="form-control" placeholder="e.g. Kathmandu, Nepal" value="<?= htmlspecialchars($warehouse['location'] ?? '') ?>"></div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault" <?= (!empty($warehouse['is_default']) ? 'checked' : '') ?>>
                <label class="form-check-label" for="isDefault">Set as default warehouse</label>
            </div>
        </div>
        <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> <?= isset($warehouse) ? 'Update Warehouse' : 'Save Warehouse' ?></button>
            <a href="/inventory/warehouses" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
</div></div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
