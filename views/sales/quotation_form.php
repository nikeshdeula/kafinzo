<?php ob_start(); ?>

<style>
.transaction-form-card { max-width: 1200px !important; margin: 0 auto; border: 0; background: transparent; }
.transaction-form-card .card-body { padding: 0 !important; }
.transaction-info { background: #f8f9fa; padding: 12px 15px; border-left: 3px solid #0d6efd; border-radius: 6px; margin-bottom: 12px; }
.transaction-info .form-label, .totals-grid .form-label { font-size: .8rem; font-weight: 600; color: #333; }
.transaction-info .form-control, .transaction-info .form-select, .totals-grid .form-control { height: 32px; padding: 6px 8px !important; font-size: .875rem; }
.items-title { color: #0d6efd; text-transform: uppercase; font-size: .75rem; letter-spacing: .5px; font-weight: 600; margin: 0; }
.transaction-workspace { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 12px; align-items: start; }
.items-column { min-width: 0; }
.items-panel { background: #fff; border: 1px solid #dee2e6; border-radius: 6px; padding: 10px; }
.items-table-wrap { max-height: 350px; overflow-y: auto; }
.items-panel .table { font-size: .85rem; margin-bottom: 0; }
.items-panel .table thead th { padding: 6px 4px; font-size: .75rem; background: #f1f3f5; }
.items-panel .table tbody td { padding: 4px; vertical-align: middle; }
.items-panel .table input { height: 28px; padding: 3px 4px !important; font-size: .8rem; }
.items-panel .table-hover > tbody > tr:hover > * { --bs-table-bg-state: transparent !important; background-color: transparent !important; box-shadow: none !important; }
.totals-grid { margin-top: 0; padding: 12px 15px; background: #fff; border: 1px solid #dbe2ea; border-top: 3px solid #0d6efd; border-radius: 8px; box-shadow: 0 2px 8px rgba(31,45,61,.08); display: grid; grid-template-columns: 1fr; gap: 0 !important; align-content: start; }
.totals-grid::before { content: 'Bill Summary'; display: block; margin-bottom: 12px; color: #1f2937; font-size: .9rem; font-weight: 700; }
.totals-grid > .col-md-4 { width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 7px 0; margin: 0 !important; min-height: 0; border-bottom: 1px solid #edf0f3; }
.totals-grid > .col-md-4 .form-label { margin: 0; color: #687384; font-size: .85rem; white-space: nowrap; }
.totals-grid > .col-md-4 .form-control { width: 125px; border: 0; background: transparent; padding: 0 !important; text-align: right; font-weight: 600; color: #1f2937; }
.totals-grid > .col-md-4:nth-of-type(4) { border-top: 2px solid #0d6efd; border-bottom: 0; padding-top: 8px; margin-top: 5px; }
.totals-grid > .col-md-4:nth-of-type(4) .form-label, .totals-grid > .col-md-4:nth-of-type(4) .form-control { color: #0d6efd; font-size: 1rem; font-weight: 700; }
.totals-grid > .col-12 { width: 100%; }
.invoice-footer { margin-top: 12px; }
.totals-grid .form-control[readonly] { background: transparent; }
#totalAmount { color: #0d6efd; font-weight: 700; }
</style>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-file-earmark-text text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
        <p>Fill in the quotation details and line items.</p>
    </div>
    <a href="/sales/quotations" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card transaction-form-card"><div class="card-body">
<?php $action = isset($quotation) ? '/sales/quotations/edit?id='.$quotation['id'] : '/sales/quotations/create'; ?>
<form action="<?= $action ?>" method="POST" id="quotationForm">
    <div class="row g-3 mb-3 transaction-info">
        <div class="col-md-4">
            <label class="form-label fw-600">Quotation Number <span class="text-danger">*</span></label>
            <input type="text" name="quotation_number" class="form-control" required value="<?= htmlspecialchars($quotation['quotation_number'] ?? $quotationNumber) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Customer <span class="text-danger">*</span></label>
            <select name="customer_id" class="form-select" required id="customerSelect">
                <option value="">— Select Customer —</option>
                <?php foreach ($customers as $c): ?>
                <option value="<?= $c['id'] ?>" data-address="<?= htmlspecialchars($c['address'] ?? '') ?>" data-company="<?= htmlspecialchars($c['company_name'] ?? '') ?>" data-phone="<?= htmlspecialchars($c['phone'] ?? '') ?>" data-email="<?= htmlspecialchars($c['email'] ?? '') ?>" data-branch="<?= htmlspecialchars($c['branch'] ?? '') ?>" <?= (isset($quotation) && $quotation['customer_id']==$c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?><?= !empty($c['branch']) ? ' — ' . htmlspecialchars($c['branch']) : '' ?></option>
                <?php endforeach; ?>
            </select>
            <div id="customerInfo" class="mt-1 small text-muted" style="display:none;"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Status</label>
            <select name="status" class="form-select">
                <option value="draft" <?= (isset($quotation) && $quotation['status']==='draft') ? 'selected' : '' ?>>Draft</option>
                <option value="sent" <?= (!isset($quotation) || $quotation['status']==='sent') ? 'selected' : '' ?>>Sent</option>
                <option value="accepted" <?= (isset($quotation) && $quotation['status']==='accepted') ? 'selected' : '' ?>>Accepted</option>
                <option value="rejected" <?= (isset($quotation) && $quotation['status']==='rejected') ? 'selected' : '' ?>>Rejected</option>
                <option value="expired" <?= (isset($quotation) && $quotation['status']==='expired') ? 'selected' : '' ?>>Expired</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Quotation Date</label>
            <?= nepali_date_picker('quotation_date', $quotation['quotation_date'] ?? '', 'Quotation Date') ?>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Valid Until</label>
            <?= nepali_date_picker('valid_until', $quotation['valid_until'] ?? '', 'Valid Until') ?>
        </div>
    </div>

    <div class="transaction-workspace">
    <div class="items-column">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h6 class="items-title">Line Items</h6>
        <button type="button" class="btn btn-primary btn-sm" id="addItemBtn"><i class="bi bi-plus-lg"></i></button>
    </div>
    <div class="items-panel">
    <div class="table-responsive items-table-wrap mb-0">
        <table class="table table-bordered mb-0" id="itemsTable">
            <thead><tr>
                <th style="width:40%">Description</th>
                <th style="width:15%">Qty</th>
                <th style="width:15%">Unit Price (NPR)</th>
                <th style="width:10%">Disc %</th>
                <th style="width:10%">Tax %</th>
                <th style="width:10%">Amount (NPR)</th>
                <th style="width:40px"></th>
            </tr></thead>
            <tbody id="itemsBody">
                <?php
                $items = isset($quotation['items']) && !empty($quotation['items']) ? $quotation['items'] : [['product_id'=>'','description'=>'','quantity'=>1,'unit_price'=>0,'discount_pct'=>0,'tax_rate'=>0,'amount'=>0]];
                foreach ($items as $idx => $it):
                ?>
                <tr>
                    <td><input type="text" name="items[<?= $idx ?>][product_id]" class="form-control item-desc" value="<?= htmlspecialchars($it['product_id'] ?? '') ?>"></td>
                    <td><input type="number" name="items[<?= $idx ?>][quantity]" class="form-control item-qty" value="<?= htmlspecialchars($it['quantity']) ?>" step="0.01" min="0"></td>
                    <td><input type="number" name="items[<?= $idx ?>][unit_price]" class="form-control item-price" value="<?= htmlspecialchars($it['unit_price']) ?>" step="0.01" min="0"></td>
                    <td><input type="number" name="items[<?= $idx ?>][discount_pct]" class="form-control item-disc" value="<?= htmlspecialchars($it['discount_pct']) ?>" step="0.01" min="0" max="100"></td>
                    <td><input type="number" name="items[<?= $idx ?>][tax_rate]" class="form-control item-tax" value="<?= htmlspecialchars($it['tax_rate'] ?: ($taxRate ?? 13)) ?>" step="0.01" min="0" readonly></td>
                    <td><input type="number" name="items[<?= $idx ?>][amount]" class="form-control item-amount" value="<?= htmlspecialchars($it['amount']) ?>" step="0.01" readonly></td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>

    </div>
    <div class="row g-3 totals-grid">
        <div class="col-md-4">
            <label class="form-label fw-600">Subtotal</label>
            <input type="number" name="subtotal" id="subtotal" class="form-control" value="0" step="0.01" readonly>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Tax</label>
            <input type="number" name="tax_amount" id="taxAmount" class="form-control" value="0" step="0.01" readonly>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Discount</label>
            <input type="number" name="discount_amount" id="discountAmount" class="form-control" value="0" step="0.01" min="0">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">TOTAL</label>
            <input type="number" name="total_amount" id="totalAmount" class="form-control" value="0" step="0.01" readonly>
        </div>
    </div>
    </div>
    <div class="invoice-footer">
        <div class="col-12">
            <label class="form-label fw-600">Notes</label>
            <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($quotation['notes'] ?? '') ?></textarea>
        </div>
        <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Quotation</button>
            <a href="/sales/quotations" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
</div></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = <?= count($items) ?>;
    const tbody = document.getElementById('itemsBody');
    const addBtn = document.getElementById('addItemBtn');
    const defaultTax = <?= json_encode($taxRate ?? 13) ?>;

    function calculateRow(row) {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const disc = parseFloat(row.querySelector('.item-disc').value) || 0;
        const tax = parseFloat(row.querySelector('.item-tax').value) || 0;
        const base = qty * price;
        const afterDisc = base * (1 - disc / 100);
        const amount = afterDisc * (1 + tax / 100);
        row.querySelector('.item-amount').value = amount.toFixed(2);
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;
        let totalDiscount = 0;
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const disc = parseFloat(row.querySelector('.item-disc').value) || 0;
            const tax = parseFloat(row.querySelector('.item-tax').value) || 0;
            const base = qty * price;
            const discountAmt = base * (disc / 100);
            const afterDisc = base - discountAmt;
            const taxAmt = afterDisc * (tax / 100);
            subtotal += afterDisc;
            totalTax += taxAmt;
            totalDiscount += discountAmt;
        });
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('taxAmount').value = totalTax.toFixed(2);
        const discountInput = parseFloat(document.getElementById('discountAmount').value) || 0;
        document.getElementById('totalAmount').value = (subtotal + totalTax - discountInput).toFixed(2);
    }

    addBtn.addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td><input type="text" name="items['+itemIndex+'][product_id]" class="form-control item-desc" value=""></td>' +
            '<td><input type="number" name="items['+itemIndex+'][quantity]" class="form-control item-qty" value="1" step="0.01" min="0"></td>' +
            '<td><input type="number" name="items['+itemIndex+'][unit_price]" class="form-control item-price" value="0" step="0.01" min="0"></td>' +
            '<td><input type="number" name="items['+itemIndex+'][discount_pct]" class="form-control item-disc" value="0" step="0.01" min="0" max="100"></td>' +
            '<td><input type="number" name="items['+itemIndex+'][tax_rate]" class="form-control item-tax" value="'+defaultTax+'" step="0.01" min="0" readonly></td>' +
            '<td><input type="number" name="items['+itemIndex+'][amount]" class="form-control item-amount" value="0" step="0.01" readonly></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>';
        tbody.appendChild(tr);
        itemIndex++;
        calculateRow(tr);
    });

    tbody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            const row = e.target.closest('tr');
            if (tbody.querySelectorAll('tr').length > 1) {
                row.remove();
                calculateTotals();
            } else {
                row.querySelectorAll('input').forEach(inp => { if (!inp.classList.contains('item-qty')) inp.value = inp.defaultValue; });
                row.querySelector('.item-qty').value = 1;
                calculateRow(row);
            }
        }
    });

    tbody.addEventListener('input', function(e) {
        if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price') || e.target.classList.contains('item-disc') || e.target.classList.contains('item-tax')) {
            calculateRow(e.target.closest('tr'));
        }
    });

    document.getElementById('discountAmount').addEventListener('input', calculateTotals);

    calculateTotals();
});
</script>
<script>
function showCustomerInfo() {
    const sel = document.getElementById('customerSelect');
    const info = document.getElementById('customerInfo');
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) { info.style.display = 'none'; return; }
    const parts = [];
    if (opt.dataset.company) parts.push(opt.dataset.company);
    if (opt.dataset.branch) parts.push(opt.dataset.branch);
    if (opt.dataset.address) parts.push(opt.dataset.address);
    if (opt.dataset.phone) parts.push('Ph: ' + opt.dataset.phone);
    if (opt.dataset.email) parts.push(opt.dataset.email);
    if (parts.length) { info.innerHTML = '<i class="bi bi-geo-alt me-1"></i>' + parts.join(' | '); info.style.display = 'block'; } else { info.style.display = 'none'; }
}
document.getElementById('customerSelect').addEventListener('change', showCustomerInfo);
showCustomerInfo();
</script>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
