<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-cash-coin text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
        <p>Record a new payment received from a customer.</p>
    </div>
    <a href="/sales/payments" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:720px;"><div class="card-body p-4">
<form action="/sales/payments/create" method="POST" id="paymentForm">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-600">Invoice <span class="text-danger">*</span></label>
            <select name="invoice_id" class="form-select" id="invoiceSelect" required>
                <option value="">— Select Invoice —</option>
                <?php foreach ($invoices as $inv): ?>
                <option value="<?= $inv['id'] ?>" data-customer="<?= $inv['customer_id'] ?>" data-total="<?= $inv['total_amount'] ?>" data-paid="<?= $inv['paid_amount'] ?>">
                    <?= htmlspecialchars($inv['invoice_number']) ?> — <?= htmlspecialchars($inv['customer_name']) ?> (NPR <?= number_format($inv['total_amount'], 2) ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">Customer <span class="text-danger">*</span></label>
            <select name="customer_id" class="form-select" id="customerSelect" required>
                <option value="">— Select Customer —</option>
                <?php foreach ($customers as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Payment Date</label>
            <?= nepali_date_picker('payment_date', date('Y-m-d'), 'Payment Date') ?>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Payment Method</label>
            <select name="payment_method" class="form-select">
                <option value="cash">Cash</option>
                <option value="bank">Bank</option>
                <option value="cheque">Cheque</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Reference Number</label>
            <input type="text" name="reference_number" class="form-control" placeholder="e.g. Cheque No. / Txn ID">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Amount (NPR) <span class="text-danger">*</span></label>
            <input type="number" name="amount" id="paymentAmount" class="form-control" step="0.01" min="0.01" required>
        </div>
        <div class="col-12">
            <label class="form-label fw-600">Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
        </div>
        <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Payment</button>
            <a href="/sales/payments" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
</div></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const invoiceSelect = document.getElementById('invoiceSelect');
    const customerSelect = document.getElementById('customerSelect');
    const paymentAmount = document.getElementById('paymentAmount');

    invoiceSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            customerSelect.value = option.dataset.customer;
            const total = parseFloat(option.dataset.total) || 0;
            const paid = parseFloat(option.dataset.paid) || 0;
            const remaining = total - paid;
            paymentAmount.value = remaining > 0 ? remaining.toFixed(2) : '0.01';
            paymentAmount.max = remaining > 0 ? remaining.toFixed(2) : '0.01';
        }
    });
});
</script>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
