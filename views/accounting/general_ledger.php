<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-book-fill text-primary me-2"></i>General Ledger</h4>
        <p>View all ledger entries filtered by account and date range.</p>
    </div>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card mb-4">
    <div class="card-body">
        <form action="/accounting/general-ledger" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-600">Account</label>
                <select name="account_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Select an account...</option>
                    <?php foreach ($accounts as $acc): ?>
                    <option value="<?= $acc['id'] ?>" <?= $selectedAccountId == $acc['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($acc['code'] . ' — ' . $acc['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600">From</label>
                <?= nepali_date_picker('from', $from ?? '', 'From') ?>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600">To</label>
                <?= nepali_date_picker('to', $to ?? '', 'To') ?>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white border-0 pt-3 px-4">
        <strong>
            <?php if ($selectedAccountId > 0): ?>
                Ledger: <?= htmlspecialchars($accountName) ?>
                <?php if ($from && $to): ?>
                    <span class="text-muted fw-normal">(<?= nepali_date('d M Y', $from) ?> — <?= nepali_date('d M Y', $to) ?>)</span>
                <?php endif; ?>
            <?php else: ?>
                Select an account to view ledger entries.
            <?php endif; ?>
        </strong>
    </div>
    <div class="card-body p-0">
        <?php if ($selectedAccountId > 0): ?>
            <?php if (empty($entries)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 mb-3 d-block" style="opacity:.3"></i>
                <p class="fw-500">No ledger entries found for this selection.</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Journal #</th>
                        <th>Description</th>
                        <th>Reference</th>
                        <th class="text-end">Debit (NPR)</th>
                        <th class="text-end">Credit (NPR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $runningDebit  = 0;
                    $runningCredit = 0;
                    foreach ($entries as $entry):
                        $runningDebit  += (float)$entry['debit'];
                        $runningCredit += (float)$entry['credit'];
                    ?>
                    <tr>
                        <td><?= nepali_date('d M Y', $entry['entry_date']) ?></td>
                        <td><code class="text-primary fw-bold"><?= htmlspecialchars($entry['reference'] ?? '') ?></code></td>
                        <td><?= htmlspecialchars($entry['journal_description'] ?? $entry['description'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($entry['reference'] ?? '—') ?></td>
                        <td class="text-end fw-500"><?= $entry['debit'] > 0 ? 'NPR ' . number_format($entry['debit'], 2) : '—' ?></td>
                        <td class="text-end fw-500"><?= $entry['credit'] > 0 ? 'NPR ' . number_format($entry['credit'], 2) : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-700 bg-light">
                        <td colspan="4" class="text-end">Total:</td>
                        <td class="text-end">NPR <?= number_format($runningDebit, 2) ?></td>
                        <td class="text-end">NPR <?= number_format($runningCredit, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
            </div>
            <?php endif; ?>
        <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-book fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">Please select an account to view its ledger.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . 'views/layouts/app.php';
?>
