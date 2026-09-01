<?php ob_start(); ?>

<style>
.bill-form-container {
    max-width: 1200px;
    margin: 0 auto;
}
.form-section {
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 6px;
    margin-bottom: 12px;
    border-left: 3px solid #0d6efd;
}
.form-section h6 {
    color: #0d6efd;
    margin-bottom: 10px;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 10px;
}
.form-group-vertical {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.form-label {
    font-weight: 600;
    font-size: 0.8rem;
    color: #333;
}
.form-label .text-danger {
    margin-left: 2px;
}
.form-control, .form-select {
    font-size: 0.875rem;
    padding: 6px 8px !important;
    height: 32px;
}
.table-section {
    background: white;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 10px;
    border: 1px solid #dee2e6;
}
.items-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.items-header h6 {
    color: #0d6efd;
    margin: 0;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.table-responsive {
    margin-bottom: 0;
    max-height: 350px;
    overflow-y: auto;
}
.table {
    margin-bottom: 0;
    font-size: 0.85rem;
}
.table thead th {
    padding: 6px 4px;
    font-weight: 600;
    font-size: 0.75rem;
    background-color: #f1f3f5;
}
.table tbody td {
    padding: 4px;
    vertical-align: middle;
}
.table-hover > tbody > tr:hover > * {
    --bs-table-bg-state: transparent !important;
    background-color: transparent !important;
    box-shadow: none !important;
}
.table input, .table select {
    font-size: 0.8rem;
    padding: 3px 4px !important;
    height: 28px;
}
.summary-section {
    background: #ffffff;
    padding: 16px 18px;
    border-radius: 8px;
    margin: 0;
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #dbe2ea;
    border-top: 3px solid #0d6efd;
    box-shadow: 0 2px 8px rgba(31, 45, 61, 0.08);
}
.summary-section::before {
    content: 'Bill Summary';
    display: block;
    margin-bottom: 12px;
    color: #1f2937;
    font-size: 0.9rem;
    font-weight: 700;
}
.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 7px 0;
    font-size: 0.85rem;
    border-bottom: 1px solid #edf0f3;
}
.summary-row.total {
    border-top: 2px solid #0d6efd;
    padding-top: 8px;
    margin-top: 5px;
    font-size: 1rem;
    font-weight: 700;
    color: #0d6efd;
}
.summary-label {
    color: #687384;
    font-weight: 500;
    font-size: 0.85rem;
}
.summary-value {
    font-weight: 600;
    color: #1f2937;
    text-align: right;
    font-size: 0.85rem;
    white-space: nowrap;
}
.summary-row.total .summary-value {
    color: #0d6efd;
    font-size: 1.1rem;
}
@media (max-width: 768px) {
    .summary-section {
        margin-top: 0;
    }
}
.action-buttons {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}
.action-buttons .btn {
    padding: 8px 16px;
    font-weight: 500;
    border-radius: 4px;
    font-size: 0.875rem;
}
.quick-create {
    color: #0d6efd;
    font-size: 0.75rem;
    text-decoration: none;
    white-space: nowrap;
}
.quick-create:hover {
    text-decoration: underline;
}
.quick-create-btn {
    border: 0;
    background: transparent;
    padding: 0;
}
.quick-create-modal .modal-dialog {
    max-width: 480px;
}
.quick-create-modal .modal-content {
    border: 0;
    border-radius: 10px;
    overflow: hidden;
}
.quick-create-modal .modal-header {
    border-bottom: 1px solid #edf0f3;
    padding: 12px 16px;
}
.quick-create-modal .modal-body {
    padding: 18px;
    height: auto;
}
.quick-create-modal .form-label {
    font-size: 0.8rem;
}
.quick-create-modal .form-control {
    height: 36px;
}
.quick-create-modal .modal-error {
    display: none;
    margin-bottom: 12px;
}
</style>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-receipt text-primary me-2"></i><?= htmlspecialchars($title) ?></h4>
        <p class="text-muted"><?= isset($bill) ? 'Update sales bill details.' : 'Record a new sales bill for a customer.' ?></p>
        <?php if (!empty($business['address'])): ?>
        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($business['name'] ?? '') ?>, <?= htmlspecialchars($business['address']) ?></p>
        <?php endif; ?>
    </div>
    <a href="/sales/bills" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="bill-form-container">
<?php $action = isset($bill) ? '/sales/bills/edit?id='.$bill['id'] : '/sales/bills/create'; ?>
<form action="<?= $action ?>" method="POST" id="billForm">
    <!-- Bill Information Section -->
    <div class="form-section">
        <h6><i class="bi bi-file-text me-2"></i>Bill Info</h6>
        <div class="form-grid">
            <div class="form-group-vertical">
                <label class="form-label">Bill # <span class="text-danger">*</span></label>
                <input type="text" name="bill_number" class="form-control" required value="<?= htmlspecialchars($bill['bill_number'] ?? $bill_number) ?>">
            </div>
            <div class="form-group-vertical">
                <label class="form-label d-flex justify-content-between align-items-center">
                    <span>Customer <span class="text-danger">*</span></span>
                    <button type="button" class="quick-create quick-create-btn" data-create-url="/sales/customers/create" data-create-title="New Customer" data-bs-toggle="modal" data-bs-target="#quickCreateModal"><i class="bi bi-plus-circle"></i> New</button>
                </label>
                <select name="customer_id" class="form-select" required id="customerSelect">
                    <option value="">— Select —</option>
                    <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>" data-address="<?= htmlspecialchars($c['address'] ?? '') ?>" data-company="<?= htmlspecialchars($c['company_name'] ?? '') ?>" data-phone="<?= htmlspecialchars($c['phone'] ?? '') ?>" data-email="<?= htmlspecialchars($c['email'] ?? '') ?>" data-branch="<?= htmlspecialchars($c['branch'] ?? '') ?>" <?= (isset($bill) && $bill['customer_id'] == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?><?= !empty($c['branch']) ? ' — ' . htmlspecialchars($c['branch']) : '' ?></option>
                    <?php endforeach; ?>
                </select>
                <div id="customerInfo" class="mt-1 small text-muted" style="display:none;"></div>
            </div>
            <div class="form-group-vertical">
                <label class="form-label">Bill Date <span class="text-danger">*</span></label>
                <?= nepali_date_picker('bill_date', $bill['bill_date'] ?? '', 'Bill Date', ['required' => true]) ?>
            </div>
            <div class="form-group-vertical">
                <label class="form-label">Due Date</label>
                <?= nepali_date_picker('due_date', $bill['due_date'] ?? '', 'Due Date') ?>
            </div>
            <div class="form-group-vertical">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['draft','unpaid','partial','paid','overdue','cancelled'] as $st): ?>
                    <option value="<?= $st ?>" <?= (isset($bill) && $bill['status'] === $st) ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group-vertical">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="1" style="resize: none;"><?= htmlspecialchars($bill['notes'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Line Items & Summary Section -->
    <div style="display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 12px; align-items: start;">
        <!-- Table Section -->
        <div class="table-section">
            <div class="items-header">
                <h6><i class="bi bi-basket me-2"></i>Items</h6>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="quick-create quick-create-btn" data-create-url="/inventory/products/create" data-create-title="New Product" data-bs-toggle="modal" data-bs-target="#quickCreateModal"><i class="bi bi-plus-circle"></i> New Product</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addItemRow()" style="padding: 4px 8px; font-size: 0.75rem;"><i class="bi bi-plus-lg"></i></button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0" id="itemsTable">
                    <thead class="table-light"><tr>
                        <th style="width:40%">Product / Description</th>
                        <th style="width:10%" class="text-center">Qty</th>
                        <th style="width:13%" class="text-end">Price</th>
                        <th style="width:8%" class="text-center">Disc %</th>
                        <th style="width:8%" class="text-center">Tax %</th>
                        <th style="width:13%" class="text-end">Amount</th>
                        <th style="width:8%" class="text-center">Action</th>
                    </tr></thead>
                    <tbody>
                    <?php if (isset($bill['items']) && !empty($bill['items'])): ?>
                        <?php foreach ($bill['items'] as $idx => $item): ?>
                        <tr>
                            <td>
                                <select name="items[<?= $idx ?>][product_id]" class="form-select form-select-sm">
                                    <option value="">— Select —</option>
                                    <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= ($item['product_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="text-center"><input type="number" name="items[<?= $idx ?>][quantity]" class="form-control form-control-sm item-qty text-center" value="<?= htmlspecialchars($item['quantity'] ?? 1) ?>" min="0" step="0.01"></td>
                            <td class="text-end"><input type="number" name="items[<?= $idx ?>][unit_price]" class="form-control form-control-sm item-price text-end" value="<?= htmlspecialchars($item['unit_price'] ?? 0) ?>" min="0" step="0.01"></td>
                            <td class="text-center"><input type="number" name="items[<?= $idx ?>][discount_pct]" class="form-control form-control-sm item-discount text-center" value="<?= htmlspecialchars($item['discount_pct'] ?? 0) ?>" min="0" step="0.01"></td>
                            <td class="text-center"><input type="number" name="items[<?= $idx ?>][tax_rate]" class="form-control form-control-sm item-tax text-center" value="<?= htmlspecialchars($item['tax_rate'] ?: $taxRate) ?>" min="0" step="0.01" readonly></td>
                            <td class="text-end fw-600 item-amount">NPR <?= number_format($item['amount'] ?? 0, 2) ?></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItemRow(this)" style="padding: 2px 4px;"><i class="bi bi-trash"></i></button></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td>
                                <select name="items[0][product_id]" class="form-select form-select-sm">
                                    <option value="">— Select —</option>
                                    <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="text-center"><input type="number" name="items[0][quantity]" class="form-control form-control-sm item-qty text-center" value="1" min="0" step="0.01"></td>
                            <td class="text-end"><input type="number" name="items[0][unit_price]" class="form-control form-control-sm item-price text-end" value="0" min="0" step="0.01"></td>
                            <td class="text-center"><input type="number" name="items[0][discount_pct]" class="form-control form-control-sm item-discount text-center" value="0" min="0" step="0.01"></td>
                            <td class="text-center"><input type="number" name="items[0][tax_rate]" class="form-control form-control-sm item-tax text-center" value="<?= htmlspecialchars($taxRate) ?>" min="0" step="0.01" readonly></td>
                            <td class="text-end fw-600 item-amount">NPR 0.00</td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItemRow(this)" style="padding: 2px 4px;"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Section (Right Side) -->
        <div class="summary-section">
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value" id="subtotalDisplay">NPR 0.00</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Discount</span>
                <span class="summary-value" id="discountDisplay">NPR 0.00</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Tax</span>
                <span class="summary-value" id="taxDisplay">NPR 0.00</span>
            </div>
            <div class="summary-row total">
                <span class="summary-label">TOTAL</span>
                <span class="summary-value" id="totalDisplay">NPR 0.00</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">TDS (1.5%)</span>
                <span class="summary-value text-danger" id="tdsDisplay">NPR 0.00</span>
            </div>
            <div class="summary-row" style="border-top: 2px solid #198754; padding-top: 8px; margin-top: 5px; font-size: 1rem; font-weight: 700; color: #198754;">
                <span class="summary-label" style="color: #198754;">GRAND TOTAL</span>
                <span class="summary-value" id="grandTotalDisplay" style="color: #198754; font-size: 1.1rem;">NPR 0.00</span>
            </div>
        </div>
    </div>

    <div class="action-buttons">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Save</button>
        <a href="/sales/bills" class="btn btn-outline-secondary btn-sm">Cancel</a>
    </div>
</form>
</div>

<div class="modal fade quick-create-modal" id="quickCreateModal" tabindex="-1" aria-labelledby="quickCreateTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickCreateTitle">Create</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger modal-error" id="quickCreateError"></div>
                <form id="quickCustomerForm" class="quick-create-form" data-endpoint="/sales/customers/create?modal=1">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company Name</label>
                            <input type="text" name="company_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Branch</label>
                            <input type="text" name="branch" class="form-control" placeholder="e.g. Main Branch">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">PAN Number</label>
                            <input type="text" name="pan" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">VAT Number</label>
                            <input type="text" name="vat_number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <input type="text" name="address" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-save me-1"></i> Save Customer</button>
                </form>
                <form id="quickProductForm" class="quick-create-form" style="display:none" data-endpoint="/inventory/products/create?modal=1">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product / Service Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Purchase Price</label>
                            <input type="number" name="purchase_price" class="form-control" value="0" min="0" step="0.01">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Selling Price</label>
                            <input type="number" name="selling_price" class="form-control" value="0" min="0" step="0.01">
                        </div>
                    </div>
                    <input type="hidden" name="tax_rate" value="<?= htmlspecialchars($taxRate) ?>">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Save Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $productOptions = ''; foreach ($products as $p): $productOptions .= '<option value="' . $p['id'] . '">' . htmlspecialchars($p['name']) . '</option>'; endforeach; ?>

<script>
document.querySelectorAll('[data-create-url]').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('quickCreateTitle').textContent = this.dataset.createTitle;
        document.querySelectorAll('.quick-create-form').forEach(form => form.style.display = 'none');
        const formId = this.dataset.createTitle === 'New Customer' ? 'quickCustomerForm' : 'quickProductForm';
        const targetForm = document.getElementById(formId);
        if (targetForm) targetForm.style.display = 'block';
        document.getElementById('quickCreateError').style.display = 'none';
    });
});
document.querySelectorAll('.quick-create-form').forEach(form => {
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        const error = document.getElementById('quickCreateError');
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        try {
            const response = await fetch(form.dataset.endpoint, { method: 'POST', body: new FormData(form), headers: { 'Accept': 'application/json' } });
            const result = await response.json();
            if (!result.success) throw new Error(result.message || 'Unable to create record.');
            if (form.id === 'quickCustomerForm') {
                const select = document.querySelector('select[name="customer_id"]');
                const option = new Option(result.name, result.id, true, true);
                select.add(option);
            } else if (form.id === 'quickProductForm') {
                const selects = document.querySelectorAll('select[name$="[product_id]"]');
                selects.forEach(target => {
                    const option = new Option(result.name, result.id, false, false);
                    target.add(option);
                });
            }
            bootstrap.Modal.getInstance(document.getElementById('quickCreateModal')).hide();
            form.reset();
        } catch (requestError) {
            error.textContent = requestError.message;
            error.style.display = 'block';
        } finally {
            submitButton.disabled = false;
        }
    });
});
</script>

<script>
let itemIndex = <?= isset($bill['items']) ? count($bill['items']) : 1 ?>;
const defaultTax = <?= json_encode($taxRate) ?>;
function addItemRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const row = document.createElement('tr');
    row.innerHTML = `<td>
        <select name="items[${itemIndex}][product_id]" class="form-select form-select-sm">
            <option value="">— Select Product —</option>
            <?= $productOptions ?>
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
    const tds = total * 0.015;
    const grandTotal = total - tds;
    document.getElementById('subtotalDisplay').textContent = 'NPR ' + subtotal.toFixed(2);
    document.getElementById('discountDisplay').textContent = 'NPR ' + discount.toFixed(2);
    document.getElementById('taxDisplay').textContent = 'NPR ' + tax.toFixed(2);
    document.getElementById('totalDisplay').textContent = 'NPR ' + total.toFixed(2);
    document.getElementById('tdsDisplay').textContent = 'NPR ' + tds.toFixed(2);
    document.getElementById('grandTotalDisplay').textContent = 'NPR ' + grandTotal.toFixed(2);
}
document.querySelectorAll('.item-qty, .item-price, .item-discount, .item-tax').forEach(el => {
    el.addEventListener('input', calculateTotals);
});
calculateTotals();
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
    if (parts.length) {
        info.innerHTML = '<i class="bi bi-geo-alt me-1"></i>' + parts.join(' | ');
        info.style.display = 'block';
    } else {
        info.style.display = 'none';
    }
}
document.getElementById('customerSelect').addEventListener('change', showCustomerInfo);
showCustomerInfo();
</script>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
