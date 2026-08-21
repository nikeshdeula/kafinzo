<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div><h4><i class="bi bi-tag text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
    <p><?= isset($category) ? 'Update category details.' : 'Create a new product category.' ?></p></div>
    <a href="/inventory/categories" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:640px;"><div class="card-body p-4">
<form action="<?= isset($category) ? '/inventory/categories/edit?id=' . $category['id'] : '/inventory/categories/create' ?>" method="POST">
    <div class="row g-3">
        <div class="col-12"><label class="form-label fw-600">Category Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($category['name'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label fw-600">Description</label>
            <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($category['description'] ?? '') ?></textarea></div>
        <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> <?= isset($category) ? 'Update Category' : 'Save Category' ?></button>
            <a href="/inventory/categories" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
</div></div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
