<?php ob_start(); ?>

<style>
.transaction-form-card { max-width: 1200px !important; margin: 0 auto; border: 0; background: transparent; }
.transaction-form-card .card-body { padding: 0 !important; }
.transaction-info { background: #f8f9fa; padding: 12px 15px; border-left: 3px solid #0d6efd; border-radius: 6px; }
.transaction-info .form-label { font-size: .8rem; font-weight: 600; color: #333; }
.transaction-info .form-control, .transaction-info .form-select { height: 32px; padding: 6px 8px !important; font-size: .875rem; }
.items-title { color: #0d6efd; text-transform: uppercase; font-size: .75rem; letter-spacing: .5px; font-weight: 600; }
.items-panel { background: #fff; border: 1px solid #dee2e6; border-radius: 6px; padding: 10px; }
.items-table-wrap { max-height: 350px; overflow-y: auto; }
.items-panel .table { font-size: .85rem; margin-bottom: 0; }
.items-panel .table thead th { padding: 6px 4px; font-size: .75rem; background: #f1f3f5; }
.items-panel .table tbody td { padding: 4px; vertical-align: middle; }
.items-panel .table input, .items-panel .table select { height: 28px; padding: 3px 4px !important; font-size: .8rem; }
.items-panel .table-hover > tbody > tr:hover > * { --bs-table-bg-state: transparent !important; background-color: transparent !important; box-shadow: none !important; }
.order-summary { margin-top: 12px; margin-left: auto; max-width: 300px; padding: 12px 15px; background: #fff; border: 1px solid #dbe2ea; border-top: 3px solid #0d6efd; border-radius: 8px; box-shadow: 0 2px 8px rgba(31,45,61,.08); }
.order-summary .summary-line { display: flex; justify-content: space-between; gap: 12px; padding: 7px 0; border-bottom: 1px solid #edf0f3; color: #687384; font-size: .85rem; }
.order-summary .summary-total { color: #0d6efd; font-weight: 700; font-size: 1rem; border-top: 2px solid #0d6efd; border-bottom: 0; padding-top: 8px; margin-top: 5px; }
</style>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-cart-fill text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
        <p><?= isset($order) ? 'Update purchase order details.' : 'Create a new purchase order for a supplier.' ?></p>
    </div>
    <a href="/purchases/orders" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card transaction-form-card"><div class="card-body">
<?php $action = isset($order) ? '/purchases/orders/edit?id='.$order['id'] : '/purchases/orders/create'; ?>
<form action="<?= $action ?>" method="POST" id="orderForm">
    <div class="row g-3 transaction-info mb-3">
        <div class="col-md-3">
            <label class="form-label fw-600">Order Number <span class="text-danger">*</span></label>
            <input type="text" name="order_number" class="form-control" required value="<?= htmlspecialchars($order['order_number'] ?? $order_number) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-600">Supplier <span class="text-danger">*</span></label>
            <select name="supplier_id" class="form-select" required>
                <option value="">— Select Supplier —</option>
                <?php foreach ($suppliers as $sup): ?>
                <option value="<?= $sup['id'] ?>" <?= (isset($order) && $order['supplier_id'] == $sup['id']) ? 'selected' : '' ?>><?= htmlspecialchars($sup['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-600">Order Date <span class="text-danger">*</span></label>
            <?= nepali_date_picker('order_date', $order['order_date'] ?? '', 'Order Date', ['required' => true]) ?>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-600">Expected Delivery</label>
            <?= nepali_date_picker('expected_delivery', $order['expected_delivery'] ?? '', 'Expected Delivery') ?>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Status</label>
            <select name="status" class="form-select">
                <?php foreach (['draft','pending','ordered','received','cancelled'] as $st): ?>
                <option value="<?= $st ?>" <?= (isset($order) && $order['status'] === $st) ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label fw-600">Notes</label>
            <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($order['notes'] ?? '') ?></textarea>
        </div>
    </div>

    <hr class="my-3">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0 items-title">Items</h5>
        <button type="button" class="btn btn-primary btn-sm" onclick="addItemRow()"><i class="bi bi-plus-lg"></i></button>
    </div>

    <div class="items-panel">
    <div class="table-responsive items-table-wrap">
        <table class="table table-bordered mb-0" id="itemsTable">
            <thead class="table-light"><tr>
                <th style="width:30%">Product / Description</th>
                <th style="width:15%">Quantity</th>
                <th style="width:15%">Unit Price (NPR)</th>
                <th style="width:10%">Discount (%)</th>
                <th style="width:10%">Tax (%)</th>
                <th style="width:15%" class="text-end">Amount</th>
                <th style="width:5%"></th>
            </tr></thead>
            <tbody>
            <?php if (isset($order['items']) && !empty($order['items'])): ?>
                <?php foreach ($order['items'] as $idx => $item): ?>
                <tr>
                            <td>
                                <select name="items[<?= $idx ?>][product_id]" class="form-select form-select-sm">
                                    <option value="">— Select Product —</option>
                                    <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= ($item['product_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                    <td><input type="number" name="items[<?= $idx ?>][quantity]" class="form-control form-control-sm item-qty" value="<?= htmlspecialchars($item['quantity'] ?? 1) ?>" min="0" step="0.01"></td>
                    <td><input type="number" name="items[<?= $idx ?>][unit_price]" class="form-control form-control-sm item-price" value="<?= htmlspecialchars($item['unit_price'] ?? 0) ?>" min="0" step="0.01"></td>
                    <td><input type="number" name="items[<?= $idx ?>][discount_pct]" class="form-control form-control-sm item-discount" value="<?= htmlspecialchars($item['discount_pct'] ?? 0) ?>" min="0" step="0.01"></td>
                     <td><input type="number" name="items[<?= $idx ?>][tax_rate]" class="form-control form-control-sm item-tax" value="<?= htmlspecialchars($item['tax_rate'] ?: $taxRate) ?>" min="0" step="0.01" readonly></td>
                    <td class="text-end fw-600 item-amount">NPR <?= number_format($item['amount'] ?? 0, 2) ?></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItemRow(this)"><i class="bi bi-x"></i></button></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                            <td>
                                <select name="items[0][product_id]" class="form-select form-select-sm">
                                    <option value="">— Select Product —</option>
                                    <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                    <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm item-qty" value="1" min="0" step="0.01"></td>
                    <td><input type="number" name="items[0][unit_price]" class="form-control form-control-sm item-price" value="0" min="0" step="0.01"></td>
                    <td><input type="number" name="items[0][discount_pct]" class="form-control form-control-sm item-discount" value="0" min="0" step="0.01"></td>
                     <td><input type="number" name="items[0][tax_rate]" class="form-control form-control-sm item-tax" value="<?= htmlspecialchars($taxRate) ?>" min="0" step="0.01" readonly></td>
                    <td class="text-end fw-600 item-amount">NPR 0.00</td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItemRow(this)"><i class="bi bi-x"></i></button></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>

    <div class="order-summary">
            <div class="summary-line"><span>Subtotal</span><span id="subtotalDisplay">NPR 0.00</span></div>
            <div class="summary-line"><span>Discount</span><span id="discountDisplay">NPR 0.00</span></div>
            <div class="summary-line"><span>Tax</span><span id="taxDisplay">NPR 0.00</span></div>
            <div class="summary-line summary-total"><span>Total</span><span id="totalDisplay">NPR 0.00</span></div>
    </div>

    <div class="col-12 d-flex gap-2 mt-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Order</button>
        <a href="/purchases/orders" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
</div></div>

<script>
let itemIndex = <?= isset($order['items']) ? count($order['items']) : 1 ?>;
const defaultTax = <?= json_encode($taxRate) ?>;
function addItemRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const row = document.createElement('tr');
    row.innerHTML = `<td>
        <select name="items[${itemIndex}][product_id]" class="form-select form-select-sm">
            <option value="">— Select Product —</option>
            <?php foreach ($products as $p): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </td>
    <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm item-qty" value="1" min="0" step="0.01"></td>
    <td><input type="number" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm item-price" value="0" min="0" step="0.01"></td>
    <td><input type="number" name="items[${itemIndex}][discount_pct]" class="form-control form-control-sm item-discount" value="0" min="0" step="0.01"></td>
    <td><input type="number" name="items[${itemIndex}][tax_rate]" class="form-control form-control-sm item-tax" value="${defaultTax}" min="0" step="0.01" readonly></td>
    <td class="text-end fw-600 item-amount">NPR 0.00</td>
    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItemRow(this)"><i class="bi bi-x"></i></button></td>`;
    tbody.appendChild(row);
    itemIndex++;
    calculateTotals();
    attachRowListeners(row);
}
function removeItemRow(btn) {
    const row = btn.closest('tr');
    if (document.querySelectorAll('#itemsTable tbody tr').length > 1) {
        row.remove();
        calculateTotals();
    }
}
function attachRowListeners(row) {
    row.querySelectorAll('.item-qty, .item-price, .item-discount, .item-tax').forEach(el => {
        el.addEventListener('input', calculateTotals);
    });
}
document.querySelectorAll('#itemsTable tbody tr').forEach(attachRowListeners);
function calculateTotals() {
    let subtotal = 0, discount = 0, tax = 0, total = 0;
    document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const disc = parseFloat(row.querySelector('.item-discount').value) || 0;
        const taxRate = parseFloat(row.querySelector('.item-tax').value) || 0;
        const lineSub = qty * price;
        const lineDisc = lineSub * (disc / 100);
        const lineTax = (lineSub - lineDisc) * (taxRate / 100);
        const lineTotal = lineSub - lineDisc + lineTax;
        subtotal += lineSub;
        discount += lineDisc;
        tax += lineTax;
        total += lineTotal;
        row.querySelector('.item-amount').textContent = 'NPR ' + lineTotal.toFixed(2);
    });
    document.getElementById('subtotalDisplay').textContent = 'NPR ' + subtotal.toFixed(2);
    document.getElementById('discountDisplay').textContent = 'NPR ' + discount.toFixed(2);
    document.getElementById('taxDisplay').textContent = 'NPR ' + tax.toFixed(2);
    document.getElementById('totalDisplay').textContent = 'NPR ' + total.toFixed(2);
}
document.querySelectorAll('.item-qty, .item-price, .item-discount, .item-tax').forEach(el => {
    el.addEventListener('input', calculateTotals);
});
calculateTotals();
</script>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
