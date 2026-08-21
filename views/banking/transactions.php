<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div><h4><i class="bi bi-list-ul text-primary me-2"></i>Transactions — <?= htmlspecialchars($selectedAccount['account_name'] ?? 'Select Account') ?></h4>
    <p>View recent bank or cash transactions.</p></div>
</div>

<div class="mb-3 d-flex align-items-center gap-2">
    <label class="fw-600 me-2">Account:</label>
    <?php foreach ($accounts as $acc): ?>
    <a href="/banking/transactions?account_id=<?= $acc['id'] ?>" class="btn btn-sm <?= ($selectedAccount['id'] ?? 0) == $acc['id'] ? 'btn-primary' : 'btn-outline-secondary' ?>">
        <?= htmlspecialchars($acc['account_name']) ?>
    </a>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($transactions)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-receipt fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p>No transactions found for this account.</p>
        </div>
        <?php else: ?>
        <table class="table table-hover mb-0">
            <thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Reference</th>
            <th class="text-end">Amount</th><th>Reconciled</th></tr></thead>
            <tbody>
            <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?= nepali_date('d M Y', $t['transaction_date']) ?></td>
                <td><?php
                    $typeMap = ['deposit'=>['success','bi-arrow-down-circle'],'withdrawal'=>['danger','bi-arrow-up-circle'],'transfer'=>['info','bi-arrow-left-right'],'charge'=>['warning','bi-dash-circle']];
                    [$cls,$icon] = $typeMap[$t['transaction_type']] ?? ['secondary','bi-circle'];
                ?><span class="badge bg-<?= $cls ?>-subtle text-<?= $cls ?>"><i class="bi <?= $icon ?> me-1"></i><?= ucfirst($t['transaction_type']) ?></span></td>
                <td><?= htmlspecialchars($t['description'] ?? '—') ?></td>
                <td><code><?= htmlspecialchars($t['reference'] ?? '—') ?></code></td>
                <td class="text-end fw-600">NPR <?= number_format($t['amount'], 2) ?></td>
                <td><?= $t['is_reconciled'] ? '<span class="badge bg-success-subtle text-success">Yes</span>' : '<span class="badge bg-light text-muted border">No</span>' ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
