<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-tags-fill text-primary me-2"></i>Product Categories</h4>
        <p>Organise your products into categories.</p>
    </div>
    <a href="/inventory/categories/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Category</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($categories)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-tags fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No categories yet. <a href="/inventory/categories/create">Add your first category.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>#</th><th>Category Name</th><th>Description</th><th class="text-end">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($categories as $i => $cat): ?>
            <tr>
                <td class="text-muted small"><?= $i + 1 ?></td>
                <td class="fw-600"><?= htmlspecialchars($cat['name']) ?></td>
                <td><?= htmlspecialchars($cat['description'] ?? '—') ?></td>
                <td class="text-end">
                    <a href="/inventory/categories/edit?id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="/inventory/categories/delete" style="display:inline" onsubmit="return confirm('Delete this category?')">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
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
