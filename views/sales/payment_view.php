<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-cash-coin text-primary me-2"></i>Payment Details</h4>
        <p>View payment information.</p>
    </div>
    <a href="/sales/payments" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width:720px;"><div class="card-body p-4">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-600 text-muted">Invoice</label>
            <div class="fw-600"><?= htmlspecialchars($payment['invoice_number'] ?? '—') ?></div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600 text-muted">Customer</label>
            <div class="fw-600"><?= htmlspecialchars($payment['customer_name']) ?></div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600 text-muted">Payment Date</label>
            <div><?= htmlspecialchars($payment['payment_date']) ?></div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600 text-muted">Payment Method</label>
            <div>
                <?php
                $method = $payment['payment_method'];
                $badgeClass = 'bg-secondary-subtle text-secondary';
                if ($method === 'cash') $badgeClass = 'bg-success-subtle text-success';
                elseif ($method === 'bank') $badgeClass = 'bg-info-subtle text-info';
                elseif ($method === 'cheque') $badgeClass = 'bg-warning-subtle text-warning';
                ?>
                <span class="badge <?= $badgeClass ?>"><?= ucfirst($method) ?></span>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600 text-muted">Reference Number</label>
            <div><code><?= htmlspecialchars($payment['reference_number'] ?? '—') ?></code></div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600 text-muted">Amount</label>
            <div class="h5 text-success mb-0">NPR <?= number_format($payment['amount'], 2) ?></div>
        </div>
        <?php if (!empty($payment['notes'])): ?>
        <div class="col-12">
            <label class="form-label fw-600 text-muted">Notes</label>
            <div><?= nl2br(htmlspecialchars($payment['notes'])) ?></div>
        </div>
        <?php endif; ?>
    </div>
</div></div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
