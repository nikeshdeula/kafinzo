<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div><h4><i class="bi bi-plus-circle-fill text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
    <p>Add a new bank or cash account.</p></div>
    <a href="/banking/accounts" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:620px;"><div class="card-body p-4">
<form action="/banking/create" method="POST">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-600">Account Type</label>
            <select name="account_type" class="form-select" id="accType">
                <option value="bank">Bank Account</option>
                <option value="cash">Cash Account</option>
            </select></div>
        <div class="col-md-6"><label class="form-label fw-600">Account Name <span class="text-danger">*</span></label>
            <input type="text" name="account_name" class="form-control" required placeholder="e.g. NMB Bank - Main"></div>
        <div id="bankFields" class="col-12 row g-3">
            <div class="col-md-6"><label class="form-label fw-600">Bank Name</label>
                <input type="text" name="bank_name" class="form-control" placeholder="e.g. NMB Bank"></div>
            <div class="col-md-6"><label class="form-label fw-600">Account Number</label>
                <input type="text" name="account_number" class="form-control"></div>
            <div class="col-md-6"><label class="form-label fw-600">Branch</label>
                <input type="text" name="branch" class="form-control"></div>
        </div>
        <div class="col-md-6"><label class="form-label fw-600">Opening Balance (NPR)</label>
            <input type="number" name="opening_balance" step="0.01" class="form-control" value="0"></div>
        <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Account</button>
            <a href="/banking/accounts" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
</div></div>

<script>
document.getElementById('accType').addEventListener('change', function() {
    document.getElementById('bankFields').style.display = this.value === 'cash' ? 'none' : '';
});
</script>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
