<?php ob_start(); ?>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<?php
$modules = [
    'sales' => [
        ['id' => 'customers', 'label' => 'Customers', 'icon' => 'bi-people-fill', 'url' => '/sales/customers'],
        ['id' => 'invoices', 'label' => 'Invoices', 'icon' => 'bi-receipt', 'url' => '/sales/invoices'],
        ['id' => 'quotations', 'label' => 'Quotations', 'icon' => 'bi-file-earmark-text', 'url' => '/sales/quotations'],
        ['id' => 'payments', 'label' => 'Payments Received', 'icon' => 'bi-cash-coin', 'url' => '/sales/payments'],
    ],
    'purchases' => [
        ['id' => 'suppliers', 'label' => 'Suppliers', 'icon' => 'bi-truck', 'url' => '/purchases/suppliers'],
        ['id' => 'bills', 'label' => 'Purchase Bills', 'icon' => 'bi-file-earmark-minus', 'url' => '/purchases/bills'],
        ['id' => 'orders', 'label' => 'Purchase Orders', 'icon' => 'bi-cart-fill', 'url' => '/purchases/orders'],
        ['id' => 'payments', 'label' => 'Supplier Payments', 'icon' => 'bi-credit-card', 'url' => '/purchases/payments'],
    ],
    'inventory' => [
        ['id' => 'products', 'label' => 'Products', 'icon' => 'bi-box-seam-fill', 'url' => '/inventory/products'],
        ['id' => 'categories', 'label' => 'Categories', 'icon' => 'bi-tags-fill', 'url' => '/inventory/categories'],
        ['id' => 'warehouses', 'label' => 'Warehouses', 'icon' => 'bi-building', 'url' => '/inventory/warehouses'],
        ['id' => 'stock-movement', 'label' => 'Stock Movement', 'icon' => 'bi-arrow-left-right', 'url' => '/inventory/stock-movement'],
    ],
    'accounting' => [
        ['id' => 'chart-of-accounts', 'label' => 'Chart of Accounts', 'icon' => 'bi-diagram-3-fill', 'url' => '/accounting/chart-of-accounts'],
        ['id' => 'journal-entries', 'label' => 'Journal Entries', 'icon' => 'bi-journal-text', 'url' => '/accounting/journal-entries'],
        ['id' => 'general-ledger', 'label' => 'General Ledger', 'icon' => 'bi-book-fill', 'url' => '/accounting/general-ledger'],
        ['id' => 'trial-balance', 'label' => 'Trial Balance', 'icon' => 'bi-bar-chart-steps', 'url' => '/accounting/trial-balance'],
    ],
    'reports' => [
        ['id' => 'profit-loss', 'label' => 'Profit & Loss', 'icon' => 'bi-graph-up-arrow', 'url' => '/reports/profit-loss'],
        ['id' => 'balance-sheet', 'label' => 'Balance Sheet', 'icon' => 'bi-clipboard-data-fill', 'url' => '/reports/balance-sheet'],
        ['id' => 'cash-flow', 'label' => 'Cash Flow', 'icon' => 'bi-water', 'url' => '/reports/cash-flow'],
    ],
    'settings' => [
        ['id' => 'business', 'label' => 'Business', 'icon' => 'bi-building', 'url' => '/settings/business'],
        ['id' => 'users', 'label' => 'Users', 'icon' => 'bi-people', 'url' => '/settings/users'],
        ['id' => 'roles', 'label' => 'Roles & Permissions', 'icon' => 'bi-shield-lock', 'url' => '/settings/roles'],
        ['id' => 'tax', 'label' => 'Tax', 'icon' => 'bi-percent', 'url' => '/settings/tax'],
    ],
];

$currentModule = $module ?? null;
$currentTab = $activeTab ?? null;
$tabs = $modules[$currentModule] ?? [];
?>

<?php if (!empty($tabs)): ?>
<ul class="nav nav-tabs mb-4">
    <?php foreach ($tabs as $tab): ?>
    <li class="nav-item">
        <a class="nav-link <?= $currentTab === $tab['id'] ? 'active' : '' ?>" href="<?= $tab['url'] ?>">
            <i class="bi <?= $tab['icon'] ?> me-1"></i> <?= htmlspecialchars($tab['label']) ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-tools text-primary me-2"></i><?= htmlspecialchars($pageTitle ?? 'Coming Soon') ?></h4>
        <p><?= htmlspecialchars($pageDesc ?? 'This module is currently under development.') ?></p>
    </div>
</div>

<div class="card p-5 text-center">
    <div class="text-primary mb-3" style="font-size:4rem;opacity:0.4;">
        <i class="bi bi-tools"></i>
    </div>
    <h5 class="fw-bold text-muted">Under Development</h5>
    <p class="text-muted small">This module will be available in an upcoming phase.</p>
    <a href="/dashboard" class="btn btn-primary btn-sm mt-2" style="width:fit-content;margin:auto;">
        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>

<?php
$content = ob_get_clean();
$title = $pageTitle ?? 'Coming Soon';
require BASE_PATH . 'views/layouts/app.php';
?>