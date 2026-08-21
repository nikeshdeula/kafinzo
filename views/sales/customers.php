<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-people-fill text-primary me-2"></i>Customers</h4>
        <p>Manage your customer accounts and track outstanding balances.</p>
    </div>
    <a href="/sales/customers/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Customer</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($customers)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-people fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No customers yet. <a href="/sales/customers/create">Add your first customer.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Name</th><th>Company</th><th>PAN</th><th>Phone</th><th>Email</th>
                <th class="text-end">Opening Bal.</th><th>Status</th><th style="width:100px">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($customers as $c): ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['company_name'] ?? '—') ?></td>
                <td><code><?= htmlspecialchars($c['pan'] ?? '—') ?></code></td>
                <td><?= htmlspecialchars($c['phone'] ?? '—') ?></td>
                <td><?= htmlspecialchars($c['email'] ?? '—') ?></td>
                <td class="text-end">NPR <?= number_format($c['opening_balance'], 2) ?></td>
                <td>
                    <?php if ($c['status']==='active'): ?>
                    <span class="badge bg-success-subtle text-success">Active</span>
                    <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/sales/customers/edit?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
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
