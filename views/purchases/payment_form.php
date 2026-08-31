<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-credit-card text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
        <p>Record a payment against a purchase bill or order.</p>
    </div>
    <a href="/purchases/payments" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:760px;"><div class="card-body p-4">
<form action="/purchases/payments/create" method="POST">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-600">Payment Number <span class="text-danger">*</span></label>
            <input type="text" name="payment_number" class="form-control" required value="<?= htmlspecialchars($payment_number) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Payment Date <span class="text-danger">*</span></label>
            <?= nepali_date_picker('payment_date', date('Y-m-d'), 'Payment Date', ['required' => true]) ?>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Supplier <span class="text-danger">*</span></label>
            <select name="supplier_id" class="form-select" required id="supplierSelect">
                <option value="">— Select Supplier —</option>
                <?php foreach ($suppliers as $sup): ?>
                <option value="<?= $sup['id'] ?>" data-address="<?= htmlspecialchars($sup['address'] ?? '') ?>" data-company="<?= htmlspecialchars($sup['company_name'] ?? '') ?>" data-phone="<?= htmlspecialchars($sup['phone'] ?? '') ?>" data-email="<?= htmlspecialchars($sup['email'] ?? '') ?>" data-branch="<?= htmlspecialchars($sup['branch'] ?? '') ?>"><?= htmlspecialchars($sup['name']) ?><?= !empty($sup['branch']) ? ' — ' . htmlspecialchars($sup['branch']) : '' ?></option>
                <?php endforeach; ?>
            </select>
            <div id="supplierInfo" class="mt-1 small text-muted" style="display:none;"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">Against Bill</label>
            <select name="bill_id" class="form-select" id="paymentBills">
                <option value="">— Select Bill —</option>
                <?php foreach ($bills as $b): ?>
                <option value="<?= $b['id'] ?>" data-supplier="<?= $b['supplier_id'] ?>"><?= htmlspecialchars($b['bill_number']) ?> (NPR <?= number_format($b['total_amount'] - $b['paid_amount'], 2) ?> due)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">Against Purchase Order</label>
            <select name="order_id" class="form-select" id="paymentOrders">
                <option value="">— Select Order —</option>
                <?php foreach ($orders as $o): ?>
                <option value="<?= $o['id'] ?>" data-supplier="<?= $o['supplier_id'] ?>"><?= htmlspecialchars($o['order_number']) ?> (NPR <?= number_format($o['total_amount'], 2) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Amount (NPR) <span class="text-danger">*</span></label>
            <input type="number" name="amount" class="form-control" required min="0" step="0.01">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Payment Method</label>
            <select name="payment_method" class="form-select">
                <option value="cash">Cash</option>
                <option value="bank">Bank Transfer</option>
                <option value="cheque">Cheque</option>
                <option value="online">Online</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Reference</label>
            <input type="text" name="reference" class="form-control" placeholder="e.g. TXN-12345">
        </div>
        <div class="col-12">
            <label class="form-label fw-600">Notes</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
        </div>
        <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Record Payment</button>
            <a href="/purchases/payments" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
</div></div>

<script>
document.getElementById('paymentSupplier').addEventListener('change', function() {
    const sid = this.value;
    document.querySelectorAll('#paymentBills option').forEach(opt => {
        opt.style.display = (!sid || opt.dataset.supplier == sid) ? '' : 'none';
        if (opt.value && opt.dataset.supplier != sid) opt.selected = false;
    });
    document.querySelectorAll('#paymentOrders option').forEach(opt => {
        opt.style.display = (!sid || opt.dataset.supplier == sid) ? '' : 'none';
        if (opt.value && opt.dataset.supplier != sid) opt.selected = false;
    });
});
document.getElementById('paymentBills').addEventListener('change', function() {
    if (this.value) document.getElementById('paymentOrders').value = '';
});
document.getElementById('paymentOrders').addEventListener('change', function() {
    if (this.value) document.getElementById('paymentBills').value = '';
});
</script>
<script>
function showSupplierInfo() {
    const sel = document.getElementById('supplierSelect');
    const info = document.getElementById('supplierInfo');
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) { info.style.display = 'none'; return; }
    const parts = [];
    if (opt.dataset.company) parts.push(opt.dataset.company);
    if (opt.dataset.branch) parts.push(opt.dataset.branch);
    if (opt.dataset.address) parts.push(opt.dataset.address);
    if (opt.dataset.phone) parts.push('Ph: ' + opt.dataset.phone);
    if (opt.dataset.email) parts.push(opt.dataset.email);
    if (parts.length) { info.innerHTML = '<i class="bi bi-geo-alt me-1"></i>' + parts.join(' | '); info.style.display = 'block'; } else { info.style.display = 'none'; }
}
document.getElementById('supplierSelect').addEventListener('change', showSupplierInfo);
showSupplierInfo();
</script>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
