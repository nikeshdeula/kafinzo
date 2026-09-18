<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-cart-fill text-primary me-2"></i><?= htmlspecialchars($pageTitle) ?></h4>
        <p><?= htmlspecialchars($pageDesc) ?></p>
    </div>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="/reports/purchase-statement" id="purchaseFilterForm" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-600 small text-muted">Nepali Month</label>
                <select class="form-select form-select-sm" id="nepaliMonthSelect" onchange="applyNepaliMonth(this.value)">
                    <option value="">-- Select Month --</option>
                    <option value="1">Baisakh</option>
                    <option value="2">Jestha</option>
                    <option value="3">Ashadh</option>
                    <option value="4">Shrawan</option>
                    <option value="5">Bhadra</option>
                    <option value="6">Ashwin</option>
                    <option value="7">Kartik</option>
                    <option value="8">Mangsir</option>
                    <option value="9">Poush</option>
                    <option value="10">Magh</option>
                    <option value="11">Falgun</option>
                    <option value="12">Chaitra</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label fw-600 small text-muted">Year</label>
                <select class="form-select form-select-sm" id="nepaliYearSelect" onchange="applyNepaliMonth(document.getElementById('nepaliMonthSelect').value)">
                    <?php for ($y = 2083; $y >= 2075; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == 2083 ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-600 small text-muted">From Date</label>
                <?= nepali_date_picker('from', $from ?? '', 'From', ['class' => 'form-control form-control-sm', 'placeholder' => 'YYYY-MM-DD']) ?>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-600 small text-muted">To Date</label>
                <?= nepali_date_picker('to', $to ?? '', 'To', ['class' => 'form-control form-control-sm', 'placeholder' => 'YYYY-MM-DD']) ?>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-600 small text-muted">Supplier</label>
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($supplier_id ?? '') == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?><?= !empty($s['branch']) ? ' — ' . htmlspecialchars($s['branch']) : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
            <div class="col-md-1">
                <a href="/reports/purchase-statement" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
            <div class="col-md-1">
                <a href="/reports/purchase-statement/export?from=<?= urlencode($from ?? '') ?>&to=<?= urlencode($to ?? '') ?>&supplier_id=<?= urlencode($supplier_id ?? '') ?>" class="btn btn-outline-success btn-sm w-100"><i class="bi bi-file-earmark-excel me-1"></i> Export</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-600 mb-1">Total Purchases</div>
                <div class="h4 mb-0 text-primary">NPR <?= number_format($summary['total_purchases'], 2) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-600 mb-1">Total Payments</div>
                <div class="h4 mb-0 text-success">NPR <?= number_format($summary['total_payments'], 2) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-600 mb-1">Outstanding</div>
                <div class="h4 mb-0 text-warning">NPR <?= number_format($summary['total_outstanding'], 2) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($rows)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-cart-fill fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No transactions found for the selected filters.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Date</th><th>Type</th><th>Ref #</th><th>Supplier</th><th>Branch</th><th>Address</th><th>VAT No</th>
                <th class="text-end">Subtotal</th><th class="text-end">Tax</th><th class="text-end">Discount</th>
                <th class="text-end">Total</th><th class="text-end">Paid</th><th>Status</th>
            </tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
            <?php
                $typeBadge = match($r['type']) {
                    'bill' => '<span class="badge bg-primary-subtle text-primary">Bill</span>',
                    'order' => '<span class="badge bg-info-subtle text-info">Order</span>',
                    'payment' => '<span class="badge bg-success-subtle text-success">Payment</span>',
                    default => '<span class="badge bg-light text-dark border">'.$r['type'].'</span>'
                };
                $statusBadge = match($r['status']) {
                    'paid' => '<span class="badge bg-success-subtle text-success">Paid</span>',
                    'partial' => '<span class="badge bg-info-subtle text-info">Partial</span>',
                    'unpaid' => '<span class="badge bg-warning-subtle text-warning">Unpaid</span>',
                    'overdue' => '<span class="badge bg-danger-subtle text-danger">Overdue</span>',
                    'completed' => '<span class="badge bg-success-subtle text-success">Completed</span>',
                    default => '<span class="badge bg-light text-dark border">'.ucfirst($r['status']).'</span>'
                };
            ?>
            <tr>
                <td><?= nepali_date('d M Y', $r['date']) ?></td>
                <td><?= $typeBadge ?></td>
                <td class="fw-600"><?= htmlspecialchars($r['ref_number']) ?></td>
                <td><?= htmlspecialchars($r['party_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($r['party_branch'] ?? '—') ?></td>
                <td><?= htmlspecialchars($r['party_address'] ?? '—') ?></td>
                <td><?= htmlspecialchars($r['party_vat_number'] ?? '—') ?></td>
                <td class="text-end">NPR <?= number_format($r['subtotal'], 2) ?></td>
                <td class="text-end">NPR <?= number_format($r['tax_amount'], 2) ?></td>
                <td class="text-end">NPR <?= number_format($r['discount_amount'], 2) ?></td>
                <td class="text-end fw-600">NPR <?= number_format($r['total_amount'], 2) ?></td>
                <td class="text-end">NPR <?= number_format($r['paid_amount'], 2) ?></td>
                <td><?= $statusBadge ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function applyNepaliMonth(month) {
    if (!month) return;
    var year = parseInt(document.getElementById('nepaliYearSelect').value);
    month = parseInt(month);
    var daysInMonth = 30;
    if (window.npMonthsData && window.npMonthsData[year] && window.npMonthsData[year][month]) {
        daysInMonth = window.npMonthsData[year][month];
    }
    var fromAd = bsToAd(year, month, 1);
    var toAd = bsToAd(year, month, daysInMonth);
    if (fromAd && toAd) {
        document.querySelector('[name="from"]').value = fromAd;
        var fromPicker = document.getElementById(document.querySelector('[name="from"]').closest('.nepali-date-picker').querySelector('.form-control').id);
        if (fromPicker) fromPicker.value = year + '-' + String(month).padStart(2,'0') + '-01';
        document.querySelector('[name="to"]').value = toAd;
        var toPicker = document.getElementById(document.querySelector('[name="to"]').closest('.nepali-date-picker').querySelector('.form-control').id);
        if (toPicker) toPicker.value = year + '-' + String(month).padStart(2,'0') + '-' + String(daysInMonth).padStart(2,'0');
    }
}
</script>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
