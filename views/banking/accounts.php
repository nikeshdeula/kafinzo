<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div><h4><i class="bi bi-bank2 text-primary me-2"></i>Bank & Cash Accounts</h4>
    <p>Manage your bank and cash accounts.</p></div>
    <a href="/banking/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Account</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#06d6a0">
            <h5>Total Balance</h5>
            <h2>NPR <?= number_format($totalBalance, 2) ?></h2>
        </div>
    </div>
</div>

<div class="row g-3">
<?php foreach ($accounts as $acc): ?>
<div class="col-md-4">
    <div class="card p-3">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:<?= $acc['account_type']==='cash' ? '#e6fdf7' : '#eef0fd' ?>">
                <i class="bi <?= $acc['account_type']==='cash' ? 'bi-cash-coin text-success' : 'bi-bank2 text-primary' ?>" style="font-size:1.3rem;"></i>
            </div>
            <div>
                <div class="fw-700"><?= htmlspecialchars($acc['account_name']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($acc['bank_name'] ?? ucfirst($acc['account_type'])) ?></div>
            </div>
            <?php if ($acc['is_default']): ?><span class="badge bg-primary ms-auto">Default</span><?php endif; ?>
        </div>
        <?php if ($acc['account_number']): ?>
        <div class="text-muted small mb-2">A/C: <code><?= htmlspecialchars($acc['account_number']) ?></code></div>
        <?php endif; ?>
        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
            <span class="text-muted small">Balance</span>
            <span class="fw-700 fs-5">NPR <?= number_format($acc['current_balance'], 2) ?></span>
        </div>
        <a href="/banking/transactions?account_id=<?= $acc['id'] ?>" class="btn btn-outline-primary btn-sm w-100 mt-3">
            <i class="bi bi-list-ul me-1"></i> View Transactions
        </a>
    </div>
</div>
<?php endforeach; ?>
<?php if (empty($accounts)): ?>
<div class="col-12">
<div class="card text-center py-5 text-muted">
    <i class="bi bi-bank2 fs-1 mb-3 d-block" style="opacity:.3"></i>
    <p class="fw-500">No accounts yet. <a href="/banking/create">Add your first account.</a></p>
</div>
</div>
<?php endif; ?>
</div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
