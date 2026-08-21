<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Kafinzo') ?> — Kafinzo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
    .nepali-calendar-popup .btn-sm {
        padding: 2px 4px;
        font-size: 0.75rem;
    }
    .nepali-calendar-popup .btn-light {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span>KAF<span style="color:#3f37c9">INZO</span></span>
        </div>

        <?php
        $currentUri = parse_url($_SERVER['REQUEST_URI'])['path'];
        function isActive($path) {
            global $currentUri;
            return str_starts_with($currentUri, $path) ? 'active' : '';
        }
        ?>

        <ul class="sidebar-nav">
            <li class="nav-section-label">MAIN</li>
            <li>
                <a href="/dashboard" class="<?= $currentUri === '/dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>

            <!-- Sales -->
            <li class="nav-section-label">SALES</li>
            <li>
                <a href="/sales/customers" class="<?= isActive('/sales/customers') ?>">
                    <i class="bi bi-people-fill"></i> Customers
                </a>
            </li>
            <li>
                <a href="/sales/bills" class="<?= isActive('/sales/bills') ?>">
                    <i class="bi bi-receipt"></i> Sales Bill
                </a>
            </li>
            <li>
                <a href="/sales/orders" class="<?= isActive('/sales/orders') ?>">
                    <i class="bi bi-cart-fill"></i> Sales Order
                </a>
            </li>
            <li>
                <a href="/sales/invoices" class="<?= isActive('/sales/invoices') ?>">
                    <i class="bi bi-receipt"></i> Invoices
                </a>
            </li>
            <li>
                <a href="/sales/quotations" class="<?= isActive('/sales/quotations') ?>">
                    <i class="bi bi-file-earmark-text"></i> Quotations
                </a>
            </li>
            <li>
                <a href="/sales/payments" class="<?= isActive('/sales/payments') ?>">
                    <i class="bi bi-cash-coin"></i> Payments Received
                </a>
            </li>

            <!-- Purchases -->
            <li class="nav-section-label">PURCHASES</li>
            <li>
                <a href="/purchases/suppliers" class="<?= isActive('/purchases/suppliers') ?>">
                    <i class="bi bi-truck"></i> Suppliers
                </a>
            </li>
            <li>
                <a href="/purchases/orders" class="<?= isActive('/purchases/orders') ?>">
                    <i class="bi bi-cart-fill"></i> Purchase Orders
                </a>
            </li>
            <li>
                <a href="/purchases/bills" class="<?= isActive('/purchases/bills') ?>">
                    <i class="bi bi-file-earmark-minus"></i> Purchase Bills
                </a>
            </li>
            <li>
                <a href="/purchases/payments" class="<?= isActive('/purchases/payments') ?>">
                    <i class="bi bi-credit-card"></i> Supplier Payments
                </a>
            </li>

            <!-- Inventory -->
            <li class="nav-section-label">INVENTORY</li>
            <li>
                <a href="/inventory/products" class="<?= isActive('/inventory/products') ?>">
                    <i class="bi bi-box-seam-fill"></i> Products
                </a>
            </li>
            <li>
                <a href="/inventory/categories" class="<?= isActive('/inventory/categories') ?>">
                    <i class="bi bi-tags-fill"></i> Categories
                </a>
            </li>
            <li>
                <a href="/inventory/warehouses" class="<?= isActive('/inventory/warehouses') ?>">
                    <i class="bi bi-building"></i> Warehouses
                </a>
            </li>
            <li>
                <a href="/inventory/stock-movement" class="<?= isActive('/inventory/stock-movement') ?>">
                    <i class="bi bi-arrow-left-right"></i> Stock Movement
                </a>
            </li>

            <!-- Finance -->
            <li class="nav-section-label">FINANCE</li>
            <li>
                <a href="/expenses" class="<?= isActive('/expenses') ?>">
                    <i class="bi bi-wallet-fill"></i> Expenses
                </a>
            </li>
            <li>
                <a href="/banking/accounts" class="<?= isActive('/banking') ?>">
                    <i class="bi bi-bank2"></i> Banking
                </a>
            </li>

            <!-- Accounting -->
            <li class="nav-section-label">ACCOUNTING</li>
            <li>
                <a href="/accounting/chart-of-accounts" class="<?= isActive('/accounting/chart-of-accounts') ?>">
                    <i class="bi bi-diagram-3-fill"></i> Chart of Accounts
                </a>
            </li>
            <li>
                <a href="/accounting/journal-entries" class="<?= isActive('/accounting/journal-entries') ?>">
                    <i class="bi bi-journal-text"></i> Journal Entries
                </a>
            </li>
            <li>
                <a href="/accounting/general-ledger" class="<?= isActive('/accounting/general-ledger') ?>">
                    <i class="bi bi-book-fill"></i> General Ledger
                </a>
            </li>
            <li>
                <a href="/accounting/trial-balance" class="<?= isActive('/accounting/trial-balance') ?>">
                    <i class="bi bi-bar-chart-steps"></i> Trial Balance
                </a>
            </li>

            <!-- Reports -->
            <li class="nav-section-label">REPORTS</li>
            <li>
                <a href="/reports/profit-loss" class="<?= isActive('/reports/profit-loss') ?>">
                    <i class="bi bi-graph-up-arrow"></i> Profit & Loss
                </a>
            </li>
            <li>
                <a href="/reports/balance-sheet" class="<?= isActive('/reports/balance-sheet') ?>">
                    <i class="bi bi-clipboard-data-fill"></i> Balance Sheet
                </a>
            </li>
            <li>
                <a href="/reports/cash-flow" class="<?= isActive('/reports/cash-flow') ?>">
                    <i class="bi bi-water"></i> Cash Flow
                </a>
            </li>
            <li>
                <a href="/reports/sales-statement" class="<?= isActive('/reports/sales-statement') ?>">
                    <i class="bi bi-receipt"></i> Sales Statement
                </a>
            </li>
            <li>
                <a href="/reports/purchase-statement" class="<?= isActive('/reports/purchase-statement') ?>">
                    <i class="bi bi-cart-fill"></i> Purchase Statement
                </a>
            </li>

            <!-- Settings -->
            <li class="nav-section-label">SYSTEM</li>
            <li>
                <a href="/settings/business" class="<?= isActive('/settings') ?>">
                    <i class="bi bi-gear-fill"></i> Settings
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Topbar -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light btn-sm d-md-none" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="input-group" style="width: 280px;">
                    <span class="input-group-text bg-light border-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-0" placeholder="Search...">
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light rounded-circle position-relative">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem">0</span>
                </button>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none gap-2 text-dark dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:35px;height:35px;font-size:0.9rem;">
                            <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="d-none d-md-inline fw-500 small"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="/settings/business"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="/logout" method="POST">
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="content-area">
            <?= $content ?? '' ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sidebar mobile toggle
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('open');
});

function updateNepaliDate(input) {
    const display = input.closest('.input-group')?.querySelector('.nepali-date-display');
    if (!display || !input.value) return;
    fetch('/api/nepali-date?date=' + encodeURIComponent(input.value))
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                display.textContent = 'BS ' + data.date;
            }
        })
        .catch(() => {
            display.textContent = 'BS ' + input.value;
        });
}

// Keep the selected navigation item visible after each page navigation.
const sidebar = document.getElementById('sidebar');
const savedSidebarScroll = sessionStorage.getItem('sidebarScrollTop');
if (savedSidebarScroll !== null) {
    sidebar.scrollTop = Number(savedSidebarScroll);
}
sidebar.addEventListener('click', function(event) {
    const link = event.target.closest('a');
    if (link && link.closest('.sidebar-nav')) {
        sessionStorage.setItem('sidebarScrollTop', String(sidebar.scrollTop));
    }
});
</script>
</body>
</html>
