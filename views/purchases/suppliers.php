<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-truck text-primary me-2"></i>Suppliers</h4>
        <p>Manage your supplier accounts and purchase history.</p>
    </div>
    <a href="/purchases/suppliers/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Supplier</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($suppliers)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-truck fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No suppliers yet. <a href="/purchases/suppliers/create">Add your first supplier.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Name</th><th>Company</th><th>PAN</th><th>Phone</th><th>Email</th>
                <th class="text-end">Opening Bal.</th><th>Status</th><th style="width:100px">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($suppliers as $s): ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($s['name']) ?></td>
                <td><?= htmlspecialchars($s['company_name'] ?? '—') ?></td>
                <td><code><?= htmlspecialchars($s['pan'] ?? '—') ?></code></td>
                <td><?= htmlspecialchars($s['phone'] ?? '—') ?></td>
                <td><?= htmlspecialchars($s['email'] ?? '—') ?></td>
                <td class="text-end">NPR <?= number_format($s['opening_balance'], 2) ?></td>
                <td><?= $s['status']==='active' ? '<span class="badge bg-success-subtle text-success">Active</span>' : '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>' ?></td>
                <td><a href="/purchases/suppliers/edit?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
