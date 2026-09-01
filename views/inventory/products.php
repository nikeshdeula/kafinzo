<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-box-seam-fill text-primary me-2"></i>Products & Services</h4>
        <p>Manage your product catalogue and track inventory levels.</p>
    </div>
    <a href="/inventory/products/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Product</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<?php if (!empty($lowStock)): ?>
<div class="alert alert-warning d-flex align-items-center mb-3">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong><?= count($lowStock) ?> product(s)</strong>&nbsp;are below minimum stock level.
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($products)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-box-seam fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No products yet. <a href="/inventory/products/create">Add your first product.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Product</th><th>SKU</th><th>Type</th><th>Category</th>
                <th class="text-end">Buy Price</th><th class="text-end">Sell Price</th>
                <th class="text-end">Stock</th><th>Status</th>
            </tr></thead>
            <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($p['name']) ?></td>
                <td><code><?= htmlspecialchars($p['sku'] ?? '—') ?></code></td>
                <td><?= $p['type']==='service' ? '<span class="badge bg-info-subtle text-info">Service</span>' : '<span class="badge bg-primary-subtle text-primary">Product</span>' ?></td>
                <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                <td class="text-end">NPR <?= number_format($p['purchase_price'], 2) ?></td>
                <td class="text-end">NPR <?= number_format($p['selling_price'], 2) ?></td>
                <td class="text-end">
                    <?php if ($p['type']==='product'): ?>
                    <?php $low = $p['current_stock'] <= $p['minimum_stock'] && $p['minimum_stock'] > 0; ?>
                    <span class="fw-600 <?= $low ? 'text-danger' : '' ?>"><?= number_format($p['current_stock'], 2) ?> <?= htmlspecialchars($p['unit_abbr'] ?? '') ?></span>
                    <?php if ($low): ?><i class="bi bi-exclamation-triangle-fill text-danger ms-1" title="Low stock"></i><?php endif; ?>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td><?= $p['status']==='active' ? '<span class="badge bg-success-subtle text-success">Active</span>' : '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>' ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/inventory/products/edit?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="/inventory/products/delete" style="display:inline" onsubmit="return confirm('Delete this product?')">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
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
