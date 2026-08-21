<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div><h4><i class="bi bi-box-seam-fill text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
    <p><?= isset($product) ? 'Update product details.' : 'Add a new product or service to your catalogue.' ?></p></div>
    <a href="/inventory/products" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:760px;"><div class="card-body p-4">
<?php $action = isset($product) ? '/inventory/products/edit?id='.$product['id'] : '/inventory/products/create'; ?>
<form action="<?= $action ?>" method="POST">
    <div class="row g-3">
        <div class="col-md-8"><label class="form-label fw-600">Product / Service Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($product['name'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label fw-600">Type</label>
            <select name="type" class="form-select">
                <option value="product" <?= (isset($product) && $product['type']==='product') ? 'selected' : '' ?>>Product</option>
                <option value="service" <?= (isset($product) && $product['type']==='service') ? 'selected' : '' ?>>Service</option>
            </select></div>
        <div class="col-md-4"><label class="form-label fw-600">SKU</label>
            <input type="text" name="sku" class="form-control" placeholder="Optional" value="<?= htmlspecialchars($product['sku'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label fw-600">Category</label>
            <select name="category_id" class="form-select">
                <option value="">— Select —</option>
                <?php foreach ($categories as $cat): ?><option value="<?= $cat['id'] ?>" <?= (isset($product) && $product['category_id']==$cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option><?php endforeach; ?>
            </select></div>
        <div class="col-md-4"><label class="form-label fw-600">Unit</label>
            <select name="unit_id" class="form-select">
                <option value="">— Select —</option>
                <?php foreach ($units as $u): ?><option value="<?= $u['id'] ?>" <?= (isset($product) && $product['unit_id']==$u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['abbreviation']) ?>)</option><?php endforeach; ?>
            </select></div>
        <div class="col-md-4"><label class="form-label fw-600">Purchase Price (NPR)</label>
            <input type="number" name="purchase_price" step="0.01" class="form-control" value="<?= htmlspecialchars($product['purchase_price'] ?? 0) ?>"></div>
        <div class="col-md-4"><label class="form-label fw-600">Selling Price (NPR)</label>
            <input type="number" name="selling_price" step="0.01" class="form-control" value="<?= htmlspecialchars($product['selling_price'] ?? 0) ?>"></div>
        <div class="col-md-4"><label class="form-label fw-600">Tax Rate (%)</label>
            <input type="number" name="tax_rate" step="0.01" class="form-control" value="<?= htmlspecialchars($product['tax_rate'] ?? \tax_rate()) ?>" placeholder="Global tax rate"></div>
        <div id="stockFields" class="col-12 row g-3">
            <div class="col-md-4"><label class="form-label fw-600">Opening Stock</label>
                <input type="number" name="opening_stock" step="0.01" class="form-control" value="<?= htmlspecialchars($product['opening_stock'] ?? 0) ?>"></div>
            <div class="col-md-4"><label class="form-label fw-600">Current Stock</label>
                <input type="number" name="current_stock" step="0.01" class="form-control" value="<?= htmlspecialchars($product['current_stock'] ?? $product['opening_stock'] ?? 0) ?>"></div>
            <div class="col-md-4"><label class="form-label fw-600">Minimum Stock</label>
                <input type="number" name="minimum_stock" step="0.01" class="form-control" value="<?= htmlspecialchars($product['minimum_stock'] ?? 0) ?>"></div>
        </div>
        <div class="col-12"><label class="form-label fw-600">Description</label>
            <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($product['description'] ?? '') ?></textarea></div>
        <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Product</button>
            <a href="/inventory/products" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
</div></div>

<script>
document.querySelector('[name="type"]').addEventListener('change', function() {
    document.getElementById('stockFields').style.display = this.value === 'service' ? 'none' : '';
});
</script>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
