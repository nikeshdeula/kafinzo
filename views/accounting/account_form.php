<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-plus-circle-fill text-primary me-2"></i>Add New Account</h4>
        <p>Create a custom account in your chart of accounts.</p>
    </div>
    <a href="/accounting/chart-of-accounts" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="card" style="max-width:680px;">
    <div class="card-body p-4">
        <form action="/accounting/chart-of-accounts/create" method="POST">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-600">Account Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. 6001" required value="<?= htmlspecialchars($_POST['code'] ?? '') ?>">
                    <div class="form-text">Must be unique.</div>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-600">Account Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Consulting Revenue" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-600">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="">Select type...</option>
                        <option value="asset" <?= ($_POST['type'] ?? '') === 'asset' ? 'selected' : '' ?>>Asset</option>
                        <option value="liability" <?= ($_POST['type'] ?? '') === 'liability' ? 'selected' : '' ?>>Liability</option>
                        <option value="equity" <?= ($_POST['type'] ?? '') === 'equity' ? 'selected' : '' ?>>Equity</option>
                        <option value="revenue" <?= ($_POST['type'] ?? '') === 'revenue' ? 'selected' : '' ?>>Revenue</option>
                        <option value="expense" <?= ($_POST['type'] ?? '') === 'expense' ? 'selected' : '' ?>>Expense</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-600">Sub Type</label>
                    <input type="text" name="sub_type" class="form-control" placeholder="e.g. operating" value="<?= htmlspecialchars($_POST['sub_type'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-600">Opening Balance (NPR)</label>
                    <input type="number" name="opening_balance" class="form-control" step="0.01" value="<?= htmlspecialchars($_POST['opening_balance'] ?? '0') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-600">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Optional description..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Account</button>
                    <a href="/accounting/chart-of-accounts" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . 'views/layouts/app.php';
?>
