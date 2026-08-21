<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div><h4><i class="bi bi-plus-circle-fill text-primary me-2"></i>Record Expense</h4>
    <p>Add a new expense record to your books.</p></div>
    <a href="/expenses" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:680px;"><div class="card-body p-4">
<form action="/expenses/create" method="POST">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-600">Expense Date <span class="text-danger">*</span></label>
            <?= nepali_date_picker('expense_date', date('Y-m-d'), 'Expense Date', ['required' => true]) ?>
        </div>
        <div class="col-md-6"><label class="form-label fw-600">Category</label>
            <select name="category_id" class="form-select">
                <option value="">— Select Category —</option>
                <?php foreach ($categories as $cat): ?><option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option><?php endforeach; ?>
            </select></div>
        <div class="col-md-6"><label class="form-label fw-600">Vendor</label>
            <input type="text" name="vendor" class="form-control" placeholder="Vendor or payee name"></div>
        <div class="col-md-6"><label class="form-label fw-600">Reference</label>
            <input type="text" name="reference" class="form-control" placeholder="Receipt / voucher no."></div>
        <div class="col-md-6"><label class="form-label fw-600">Amount (NPR) <span class="text-danger">*</span></label>
            <input type="number" name="amount" step="0.01" class="form-control" value="0" required></div>
        <div class="col-md-6"><label class="form-label fw-600">Tax Amount (NPR)</label>
            <input type="number" name="tax_amount" step="0.01" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label fw-600">Payment Account</label>
            <input type="text" name="payment_account" class="form-control" placeholder="e.g. Cash, Bank, Petty Cash"></div>
        <div class="col-12"><label class="form-label fw-600">Description</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Brief description of the expense..."></textarea></div>
        <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Record Expense</button>
            <a href="/expenses" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
</div></div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
