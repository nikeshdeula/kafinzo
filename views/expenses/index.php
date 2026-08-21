<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div><h4><i class="bi bi-wallet-fill text-primary me-2"></i>Expenses</h4>
    <p>Record and track all business expenses.</p></div>
    <a href="/expenses/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Record Expense</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#ef233c">
            <h5>This Month's Expenses</h5>
            <h2>NPR <?= number_format($total, 2) ?></h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center gap-2 bg-white border-0 pt-3 px-4">
        <strong>All Expenses</strong>
        <div class="ms-auto d-flex gap-2">
            <select class="form-select form-select-sm" style="width:160px" id="catFilter">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($expenses)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-wallet fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No expenses recorded yet. <a href="/expenses/create">Record an expense.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0" id="expTable">
            <thead><tr>
                <th>Date</th><th>Category</th><th>Vendor</th><th>Description</th>
                <th class="text-end">Tax</th><th class="text-end">Amount</th><th>Account</th>
            </tr></thead>
            <tbody>
            <?php foreach ($expenses as $e): ?>
            <tr data-cat="<?= htmlspecialchars($e['category_name'] ?? '') ?>">
                <td><?= nepali_date('d M Y', $e['expense_date']) ?></td>
                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($e['category_name'] ?? '—') ?></span></td>
                <td><?= htmlspecialchars($e['vendor'] ?? '—') ?></td>
                <td><?= htmlspecialchars($e['description'] ?? '—') ?></td>
                <td class="text-end">NPR <?= number_format($e['tax_amount'], 2) ?></td>
                <td class="text-end fw-600">NPR <?= number_format($e['amount'], 2) ?></td>
                <td><?= htmlspecialchars($e['payment_account'] ?? '—') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('catFilter')?.addEventListener('change', function() {
    const val = this.value.toLowerCase();
    document.querySelectorAll('#expTable tbody tr').forEach(row => {
        row.style.display = (!val || row.dataset.cat.toLowerCase() === val) ? '' : 'none';
    });
});
</script>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
