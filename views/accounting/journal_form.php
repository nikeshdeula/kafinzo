<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-<?= isset($journal) ? 'pencil-square' : 'plus-circle' ?>-fill text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
        <p><?= isset($journal) ? 'Edit the journal entry details.' : 'Record a new manual journal entry. Debits must equal credits.' ?></p>
    </div>
    <a href="/accounting/journal-entries" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card" style="max-width: 1000px;">
    <div class="card-body p-4">
        <form action="<?= isset($journal) ? '/accounting/journal-entries/edit?id='.$journal['id'] : '/accounting/journal-entries/create' ?>" method="POST">
            <?php if (isset($journal)): ?>
            <input type="hidden" name="id" value="<?= $journal['id'] ?>">
            <?php endif; ?>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-600">Journal #</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($journalNumber) ?>" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-600">Date <span class="text-danger">*</span></label>
                    <?= nepali_date_picker('entry_date', $journal['entry_date'] ?? date('Y-m-d'), 'Entry Date', ['required' => true]) ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-600">Reference</label>
                    <input type="text" name="reference" class="form-control" placeholder="e.g. INV-001" value="<?= htmlspecialchars($journal['reference'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-600">Description <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control" placeholder="Brief description of the journal entry" required value="<?= htmlspecialchars($journal['description'] ?? '') ?>">
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-700 mb-0">Debit / Credit Lines</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addRowBtn">
                    <i class="bi bi-plus-lg me-1"></i> Add Row
                </button>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-hover mb-0" id="linesTable">
                    <thead>
                        <tr>
                            <th style="width:35%">Account</th>
                            <th style="width:30%">Description</th>
                            <th class="text-end" style="width:15%">Debit (NPR)</th>
                            <th class="text-end" style="width:15%">Credit (NPR)</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="linesBody">
                        <?php
                        $rows = isset($journal) ? $ledgerRows : [];
                        if (empty($rows)) {
                            $rows = [
                                ['account_id' => '', 'description' => '', 'debit' => '', 'credit' => ''],
                            ];
                        }
                        foreach ($rows as $i => $row):
                        ?>
                        <tr>
                            <td>
                                <select name="account_id[]" class="form-select form-select-sm" required>
                                    <option value="">Select account...</option>
                                    <?php foreach ($accounts as $acc): ?>
                                    <option value="<?= $acc['id'] ?>" <?= ($row['account_id'] ?? '') == $acc['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($acc['code'] . ' — ' . $acc['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="line_description[]" class="form-control form-control-sm" placeholder="Line description" value="<?= htmlspecialchars($row['description'] ?? '') ?>">
                            </td>
                            <td>
                                <input type="number" name="debit[]" class="form-control form-control-sm text-end debit-input" step="0.01" min="0" placeholder="0.00" value="<?= htmlspecialchars($row['debit'] ?? '') ?>" oninput="toggleCredit(this)">
                            </td>
                            <td>
                                <input type="number" name="credit[]" class="form-control form-control-sm text-end credit-input" step="0.01" min="0" placeholder="0.00" value="<?= htmlspecialchars($row['credit'] ?? '') ?>" oninput="toggleDebit(this)">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" onclick="removeRow(this)">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row g-3">
                <div class="col-md-4 offset-md-8">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-600">Total Debit:</span>
                                <span class="fw-700 text-end" id="totalDebit">NPR 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fw-600">Total Credit:</span>
                                <span class="fw-700 text-end" id="totalCredit">NPR 0.00</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-700">Difference:</span>
                                <span class="fw-700 text-end" id="diffAmount">NPR 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Journal Entry</button>
                    <a href="/accounting/journal-entries" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCredit(el) {
    const row = el.closest('tr');
    row.querySelector('.credit-input').value = '';
    updateTotals();
}

function toggleDebit(el) {
    const row = el.closest('tr');
    row.querySelector('.debit-input').value = '';
    updateTotals();
}

function removeRow(btn) {
    const tbody = document.getElementById('linesBody');
    if (tbody.rows.length > 1) {
        btn.closest('tr').remove();
        updateTotals();
    }
}

document.getElementById('addRowBtn').addEventListener('click', function() {
    const tbody = document.getElementById('linesBody');
    const lastRow = tbody.rows[tbody.rows.length - 1];
    const newRow = lastRow.cloneNode(true);
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    tbody.appendChild(newRow);
});

function updateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;
    document.querySelectorAll('#linesBody tr').forEach(row => {
        const d = parseFloat(row.querySelector('.debit-input').value) || 0;
        const c = parseFloat(row.querySelector('.credit-input').value) || 0;
        totalDebit += d;
        totalCredit += c;
    });
    document.getElementById('totalDebit').textContent = 'NPR ' + totalDebit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('totalCredit').textContent = 'NPR ' + totalCredit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    const diff = Math.abs(totalDebit - totalCredit);
    const diffEl = document.getElementById('diffAmount');
    diffEl.textContent = 'NPR ' + diff.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    diffEl.className = 'fw-700 text-end ' + (diff < 0.01 ? 'text-success' : 'text-danger');
}

document.addEventListener('DOMContentLoaded', updateTotals);
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . 'views/layouts/app.php';
?>
