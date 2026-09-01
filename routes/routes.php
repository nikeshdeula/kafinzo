<?php

// Auth Routes
$router->get('/', 'AuthController@loginForm');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->post('/logout', 'AuthController@logout');

// Dashboard
$router->get('/dashboard', 'DashboardController@index');

// Sales — Customers
$router->get('/sales/customers', 'Sales\CustomerController@index');
$router->get('/sales/customers/create', 'Sales\CustomerController@create');
$router->post('/sales/customers/create', 'Sales\CustomerController@create');
$router->get('/sales/customers/edit', 'Sales\CustomerController@edit');
$router->post('/sales/customers/edit', 'Sales\CustomerController@edit');

// Sales — Bills
$router->get('/sales/bills', 'Sales\SalesBillController@index');
$router->get('/sales/bills/create', 'Sales\SalesBillController@create');
$router->post('/sales/bills/create', 'Sales\SalesBillController@create');
$router->get('/sales/bills/edit', 'Sales\SalesBillController@edit');
$router->post('/sales/bills/edit', 'Sales\SalesBillController@edit');
$router->post('/sales/bills/delete', 'Sales\SalesBillController@delete');

// Sales — Orders
$router->get('/sales/orders', 'Sales\SalesOrderController@index');
$router->get('/sales/orders/create', 'Sales\SalesOrderController@create');
$router->post('/sales/orders/create', 'Sales\SalesOrderController@create');
$router->get('/sales/orders/edit', 'Sales\SalesOrderController@edit');
$router->post('/sales/orders/edit', 'Sales\SalesOrderController@edit');
$router->post('/sales/orders/delete', 'Sales\SalesOrderController@delete');

// Sales — Invoices / Quotations / Payments
$router->get('/sales/invoices', 'Sales\InvoiceController@index');
$router->get('/sales/invoices/create', 'Sales\InvoiceController@create');
$router->post('/sales/invoices/create', 'Sales\InvoiceController@create');
$router->get('/sales/invoices/edit', 'Sales\InvoiceController@edit');
$router->post('/sales/invoices/edit', 'Sales\InvoiceController@edit');
$router->post('/sales/invoices/delete', 'Sales\InvoiceController@delete');

$router->get('/sales/quotations', 'Sales\QuotationController@index');
$router->get('/sales/quotations/create', 'Sales\QuotationController@create');
$router->post('/sales/quotations/create', 'Sales\QuotationController@create');
$router->get('/sales/quotations/edit', 'Sales\QuotationController@edit');
$router->post('/sales/quotations/edit', 'Sales\QuotationController@edit');
$router->post('/sales/quotations/delete', 'Sales\QuotationController@delete');
$router->get('/sales/quotations/convert', 'Sales\QuotationController@convert');

$router->get('/sales/payments', 'Sales\SalesPaymentController@index');
$router->get('/sales/payments/create', 'Sales\SalesPaymentController@create');
$router->post('/sales/payments/create', 'Sales\SalesPaymentController@create');
$router->get('/sales/payments/view', 'Sales\SalesPaymentController@view');

// Purchases — Suppliers
$router->get('/purchases/suppliers', 'Purchases\SupplierController@index');
$router->get('/purchases/suppliers/create', 'Purchases\SupplierController@create');
$router->post('/purchases/suppliers/create', 'Purchases\SupplierController@create');
$router->get('/purchases/suppliers/edit', 'Purchases\SupplierController@edit');
$router->post('/purchases/suppliers/edit', 'Purchases\SupplierController@edit');

// Purchases — Bills / Orders / Payments
$router->get('/purchases/bills', 'Purchases\PurchaseBillController@index');
$router->get('/purchases/bills/create', 'Purchases\PurchaseBillController@create');
$router->post('/purchases/bills/create', 'Purchases\PurchaseBillController@create');
$router->get('/purchases/bills/edit', 'Purchases\PurchaseBillController@edit');
$router->post('/purchases/bills/edit', 'Purchases\PurchaseBillController@edit');
$router->post('/purchases/bills/delete', 'Purchases\PurchaseBillController@delete');

$router->get('/purchases/orders', 'Purchases\PurchaseOrderController@index');
$router->get('/purchases/orders/create', 'Purchases\PurchaseOrderController@create');
$router->post('/purchases/orders/create', 'Purchases\PurchaseOrderController@create');
$router->get('/purchases/orders/edit', 'Purchases\PurchaseOrderController@edit');
$router->post('/purchases/orders/edit', 'Purchases\PurchaseOrderController@edit');
$router->post('/purchases/orders/delete', 'Purchases\PurchaseOrderController@delete');
$router->get('/purchases/orders/convert', 'Purchases\PurchaseOrderController@convert');

$router->get('/purchases/payments', 'Purchases\PurchasePaymentController@index');
$router->get('/purchases/payments/create', 'Purchases\PurchasePaymentController@create');
$router->post('/purchases/payments/create', 'Purchases\PurchasePaymentController@create');

// Inventory — Products
$router->get('/inventory/products', 'Inventory\ProductController@index');
$router->get('/inventory/products/create', 'Inventory\ProductController@create');
$router->post('/inventory/products/create', 'Inventory\ProductController@create');
$router->get('/inventory/products/edit', 'Inventory\ProductController@edit');
$router->post('/inventory/products/edit', 'Inventory\ProductController@edit');
$router->post('/inventory/products/delete', 'Inventory\ProductController@delete');

// Inventory — Categories
$router->get('/inventory/categories', 'Inventory\CategoryController@index');
$router->get('/inventory/categories/create', 'Inventory\CategoryController@create');
$router->post('/inventory/categories/create', 'Inventory\CategoryController@create');
$router->get('/inventory/categories/edit', 'Inventory\CategoryController@edit');
$router->post('/inventory/categories/edit', 'Inventory\CategoryController@edit');
$router->post('/inventory/categories/delete', 'Inventory\CategoryController@delete');

// Inventory — Warehouses
$router->get('/inventory/warehouses', 'Inventory\WarehouseController@index');
$router->get('/inventory/warehouses/create', 'Inventory\WarehouseController@create');
$router->post('/inventory/warehouses/create', 'Inventory\WarehouseController@create');
$router->get('/inventory/warehouses/edit', 'Inventory\WarehouseController@edit');
$router->post('/inventory/warehouses/edit', 'Inventory\WarehouseController@edit');
$router->post('/inventory/warehouses/delete', 'Inventory\WarehouseController@delete');

// Inventory — Stock Movement
$router->get('/inventory/stock-movement', 'Inventory\StockController@index');
$router->get('/inventory/stock-movement/create', 'Inventory\StockController@create');
$router->post('/inventory/stock-movement/create', 'Inventory\StockController@create');

// Expenses
$router->get('/expenses', 'ExpenseController@index');
$router->get('/expenses/create', 'ExpenseController@create');
$router->post('/expenses/create', 'ExpenseController@create');
$router->get('/expenses/categories', 'ExpenseController@categories');

// Banking
$router->get('/banking/accounts', 'BankingController@index');
$router->get('/banking/create', 'BankingController@create');
$router->post('/banking/create', 'BankingController@create');
$router->get('/banking/transactions', 'BankingController@transactions');
$router->get('/banking/reconciliation', 'BankingController@reconciliation');

// Accounting
$router->get('/accounting/chart-of-accounts', 'Accounting\ChartOfAccountsController@index');
$router->get('/accounting/chart-of-accounts/create', 'Accounting\ChartOfAccountsController@create');
$router->post('/accounting/chart-of-accounts/create', 'Accounting\ChartOfAccountsController@create');
$router->get('/accounting/journal-entries', 'Accounting\JournalController@index');
$router->get('/accounting/journal-entries/create', 'Accounting\JournalController@create');
$router->post('/accounting/journal-entries/create', 'Accounting\JournalController@create');
$router->get('/accounting/journal-entries/edit', 'Accounting\JournalController@edit');
$router->post('/accounting/journal-entries/edit', 'Accounting\JournalController@edit');
$router->post('/accounting/journal-entries/delete', 'Accounting\JournalController@delete');
$router->get('/accounting/general-ledger', 'Accounting\LedgerController@index');
$router->get('/accounting/trial-balance', 'Accounting\TrialBalanceController@index');

// Reports
$router->get('/reports/profit-loss', 'Reports\ReportController@profitLoss');
$router->get('/reports/balance-sheet', 'Reports\ReportController@balanceSheet');
$router->get('/reports/cash-flow', 'Reports\ReportController@cashFlow');
$router->get('/reports/sales-statement', 'Reports\ReportController@salesStatement');
$router->get('/reports/sales-statement/export', 'Reports\ReportController@salesStatementExport');
$router->get('/reports/purchase-statement', 'Reports\ReportController@purchaseStatement');
$router->get('/reports/purchase-statement/export', 'Reports\ReportController@purchaseStatementExport');

// Settings
$router->get('/settings/business', 'SettingsController@business');
$router->post('/settings/business', 'SettingsController@business');
$router->get('/settings/users', 'SettingsController@users');
$router->post('/settings/users/create', 'SettingsController@createUser');
$router->post('/settings/users/edit', 'SettingsController@editUser');
$router->post('/settings/users/delete', 'SettingsController@deleteUser');
$router->get('/settings/roles', 'SettingsController@roles');
$router->post('/settings/roles', 'SettingsController@roles');
$router->post('/settings/roles/delete', 'SettingsController@deleteRole');
$router->get('/settings/tax', 'SettingsController@tax');
$router->post('/settings/tax', 'SettingsController@tax');

// API
$router->get('/api/nepali-date', 'ApiController@nepaliDate');
