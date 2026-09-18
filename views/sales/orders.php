<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-cart-fill text-primary me-2"></i>Sales Orders</h4>
        <p>Create and manage sales orders for customers.</p>
    </div>
    <a href="/sales/orders/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New Sales Order</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="/sales/orders" class="row g-2 align-items-end">
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
                <label class="form-label fw-600 small text-muted">Customer</label>
                <select name="customer_id" class="form-select form-select-sm">
                    <option value="">All Customers</option>
                    <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($customer_id ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?><?= !empty($c['branch']) ? ' — ' . htmlspecialchars($c['branch']) : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label fw-600 small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach (['draft'=>'Draft','pending'=>'Pending','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= ($status ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
            <div class="col-md-1">
                <a href="/sales/orders" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
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
            <i class="bi bi-cart-fill fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No sales orders found. <a href="/sales/orders/create">Create your first order.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Order #</th><th>Date</th><th>Customer</th><th>Branch</th><th>Address</th><th class="text-end">Total</th>
                <th>Status</th><th style="width:110px">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
            <?php
                $statusBadge = match($o['status']) {
                    'draft' => '<span class="badge bg-secondary-subtle text-secondary">Draft</span>',
                    'pending' => '<span class="badge bg-warning-subtle text-warning">Pending</span>',
                    'processing' => '<span class="badge bg-info-subtle text-info">Processing</span>',
                    'shipped' => '<span class="badge bg-primary-subtle text-primary">Shipped</span>',
                    'delivered' => '<span class="badge bg-success-subtle text-success">Delivered</span>',
                    'cancelled' => '<span class="badge bg-dark-subtle text-dark">Cancelled</span>',
                    default => '<span class="badge bg-light text-dark border">'.$o['status'].'</span>'
                };
            ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($o['order_number']) ?></td>
                <td><?= nepali_date('d M Y', $o['order_date']) ?></td>
                <td><?= htmlspecialchars($o['customer_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($o['customer_branch'] ?? '—') ?></td>
                <td><?= htmlspecialchars($o['customer_address'] ?? '—') ?></td>
                <td class="text-end">NPR <?= number_format($o['total_amount'], 2) ?></td>
                <td><?= $statusBadge ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/sales/orders/edit?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="/sales/orders/delete" class="d-inline" onsubmit="return confirm('Delete this order?')">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="id" value="<?= $o['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
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
