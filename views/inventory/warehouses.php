<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-building text-primary me-2"></i>Warehouses</h4>
        <p>Manage warehouse locations for stock tracking.</p>
    </div>
    <a href="/inventory/warehouses/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Warehouse</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($warehouses)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-building fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No warehouses yet. <a href="/inventory/warehouses/create">Add your first warehouse.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>#</th><th>Warehouse Name</th><th>Location</th><th>Default</th><th class="text-end">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($warehouses as $i => $wh): ?>
            <tr>
                <td class="text-muted small"><?= $i + 1 ?></td>
                <td class="fw-600"><?= htmlspecialchars($wh['name']) ?></td>
                <td><?= htmlspecialchars($wh['location'] ?? '—') ?></td>
                <td><?= $wh['is_default'] ? '<span class="badge bg-success-subtle text-success">Default</span>' : '—' ?></td>
                <td class="text-end">
                    <a href="/inventory/warehouses/edit?id=<?= $wh['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="/inventory/warehouses/delete" style="display:inline" onsubmit="return confirm('Delete this warehouse?')">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="id" value="<?= $wh['id'] ?>">
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
