<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div><h4><i class="bi bi-tags-fill text-primary me-2"></i>Expense Categories</h4>
    <p>View all expense categories configured in the system.</p></div>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:600px;">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Category Name</th></tr></thead>
            <tbody>
            <?php foreach ($categories as $i => $cat): ?>
            <tr>
                <td class="text-muted small"><?= $i+1 ?></td>
                <td class="fw-600"><?= htmlspecialchars($cat['name']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
