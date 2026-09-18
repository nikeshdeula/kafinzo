<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-cart-fill text-primary me-2"></i>Purchase Orders</h4>
        <p>Create and manage purchase orders to suppliers.</p>
    </div>
    <a href="/purchases/orders/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New Order</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="/purchases/orders" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-600 small text-muted">Nepali Month</label>
                <select class="form-select form-select-sm" id="nepaliMonthSelect" onchange="applyNepaliMonth(this.value)">
                    <option value="">-- Select Month --</option>
                    <?php foreach ($nepaliMonths as $i => $name): ?>
                    <option value="<?= $i + 1 ?>"><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label fw-600 small text-muted">Year</label>
                <select class="form-select form-select-sm" id="nepaliYearSelect" onchange="applyNepaliMonth(document.getElementById('nepaliMonthSelect').value)">
                    <?php for ($y = $currentYear; $y >= $currentYear - 2; $y--): ?>
                    <option value="<?= $y ?>"><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-600 small text-muted">From Date</label>
                <?= nepali_date_picker('from', $from ?? '', 'From', ['class' => 'form-control form-control-sm']) ?>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-600 small text-muted">To Date</label>
                <?= nepali_date_picker('to', $to ?? '', 'To', ['class' => 'form-control form-control-sm']) ?>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-600 small text-muted">Supplier</label>
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $sup): ?>
                    <option value="<?= $sup['id'] ?>" <?= ($supplier_id ?? '') == $sup['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sup['name']) ?><?= !empty($sup['branch']) ? ' — ' . htmlspecialchars($sup['branch']) : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label fw-600 small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach (['draft'=>'Draft','pending'=>'Pending','ordered'=>'Ordered','received'=>'Received','cancelled'=>'Cancelled'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= ($status ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
            <div class="col-md-1">
                <a href="/purchases/orders" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
        </form>
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

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($orders)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-cart fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No purchase orders found. <a href="/purchases/orders/create">Create your first order.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Order #</th><th>Date</th><th>Supplier</th><th>Branch</th><th>Address</th><th class="text-end">Total</th>
                <th>Status</th><th style="width:160px">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
            <?php
                $statusBadge = match($o['status']) {
                    'draft' => '<span class="badge bg-secondary-subtle text-secondary">Draft</span>',
                    'pending' => '<span class="badge bg-warning-subtle text-warning">Pending</span>',
                    'ordered' => '<span class="badge bg-info-subtle text-info">Ordered</span>',
                    'received' => '<span class="badge bg-success-subtle text-success">Received</span>',
                    'cancelled' => '<span class="badge bg-dark-subtle text-dark">Cancelled</span>',
                    default => '<span class="badge bg-light text-dark border">'.$o['status'].'</span>'
                };
            ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($o['order_number']) ?></td>
                <td><?= nepali_date('d M Y', $o['order_date']) ?></td>
                <td><?= htmlspecialchars($o['supplier_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($o['supplier_branch'] ?? '—') ?></td>
                <td><?= htmlspecialchars($o['supplier_address'] ?? '—') ?></td>
                <td class="text-end">NPR <?= number_format($o['total_amount'], 2) ?></td>
                <td><?= $statusBadge ?></td>
                <td>
                    <a href="/purchases/orders/edit?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                    <a href="/purchases/orders/convert?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-success me-1" title="Convert to Bill" onclick="return confirm('Convert this order to a bill?')"><i class="bi bi-file-earmark-minus"></i></a>
                    <form method="POST" action="/purchases/orders/delete" class="d-inline" onsubmit="return confirm('Delete this order?')">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="id" value="<?= $o['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
