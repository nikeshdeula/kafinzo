<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div><h4><i class="bi bi-check2-square text-primary me-2"></i>Bank Reconciliation</h4>
    <p>Compare your system bank balance against your actual bank statement.</p></div>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card p-4">
            <h6 class="fw-700 text-muted mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.08em;">Select Account</h6>
            <?php foreach ($accounts as $acc): ?>
            <a href="/banking/transactions?account_id=<?= $acc['id'] ?>" class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2 text-decoration-none" style="background:#f8f9fa;color:inherit;">
                <i class="bi <?= $acc['account_type']==='cash' ? 'bi-cash-coin text-success' : 'bi-bank2 text-primary' ?> fs-5"></i>
                <div>
                    <div class="fw-600"><?= htmlspecialchars($acc['account_name']) ?></div>
                    <div class="text-muted small">NPR <?= number_format($acc['current_balance'],2) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card p-4 text-center">
            <i class="bi bi-check2-square text-primary mb-3" style="font-size:3rem;opacity:.4;"></i>
            <h5 class="fw-700">Reconciliation Module</h5>
            <p class="text-muted">Select an account from the left and import your bank statement to begin reconciliation.</p>
            <p class="text-muted small">Full reconciliation features will be available in Phase 6.</p>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
