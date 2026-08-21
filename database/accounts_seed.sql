-- Chart of Accounts seed data for Kafinzo
INSERT INTO accounts (business_id, code, name, type, sub_type, is_system) VALUES

-- ========== ASSETS ==========
(1, '1000', 'Assets', 'asset', 'group', TRUE),
(1, '1100', 'Current Assets', 'asset', 'group', TRUE),
(1, '1101', 'Cash in Hand', 'asset', 'cash', TRUE),
(1, '1102', 'Petty Cash', 'asset', 'cash', TRUE),
(1, '1110', 'Bank Accounts', 'asset', 'bank', TRUE),
(1, '1200', 'Accounts Receivable', 'asset', 'receivable', TRUE),
(1, '1300', 'Inventory', 'asset', 'inventory', TRUE),
(1, '1400', 'Input VAT', 'asset', 'tax', TRUE),
(1, '1500', 'Advance Payments', 'asset', 'other_current', TRUE),
(1, '1600', 'Fixed Assets', 'asset', 'group', TRUE),
(1, '1601', 'Land & Building', 'asset', 'fixed', TRUE),
(1, '1602', 'Furniture & Fixtures', 'asset', 'fixed', TRUE),
(1, '1603', 'Office Equipment', 'asset', 'fixed', TRUE),
(1, '1604', 'Vehicles', 'asset', 'fixed', TRUE),
(1, '1605', 'Computer & IT Equipment', 'asset', 'fixed', TRUE),

-- ========== LIABILITIES ==========
(1, '2000', 'Liabilities', 'liability', 'group', TRUE),
(1, '2100', 'Current Liabilities', 'liability', 'group', TRUE),
(1, '2101', 'Accounts Payable', 'liability', 'payable', TRUE),
(1, '2102', 'Output VAT Payable', 'liability', 'tax', TRUE),
(1, '2103', 'TDS Payable', 'liability', 'tax', TRUE),
(1, '2104', 'Salary Payable', 'liability', 'payable', TRUE),
(1, '2105', 'Advance from Customers', 'liability', 'other_current', TRUE),
(1, '2200', 'Long-term Liabilities', 'liability', 'group', TRUE),
(1, '2201', 'Bank Loans', 'liability', 'loan', TRUE),
(1, '2202', 'Other Loans', 'liability', 'loan', TRUE),

-- ========== EQUITY ==========
(1, '3000', 'Equity', 'equity', 'group', TRUE),
(1, '3001', 'Owner Capital', 'equity', 'capital', TRUE),
(1, '3002', 'Retained Earnings', 'equity', 'retained_earnings', TRUE),
(1, '3003', 'Owner Drawings', 'equity', 'drawings', TRUE),

-- ========== REVENUE ==========
(1, '4000', 'Revenue', 'revenue', 'group', TRUE),
(1, '4001', 'Sales Revenue', 'revenue', 'sales', TRUE),
(1, '4002', 'Service Revenue', 'revenue', 'service', TRUE),
(1, '4003', 'Other Income', 'revenue', 'other', TRUE),
(1, '4004', 'Interest Income', 'revenue', 'other', TRUE),
(1, '4005', 'Discount Received', 'revenue', 'other', TRUE),

-- ========== EXPENSES ==========
(1, '5000', 'Expenses', 'expense', 'group', TRUE),
(1, '5001', 'Cost of Goods Sold', 'expense', 'cogs', TRUE),
(1, '5100', 'Operating Expenses', 'expense', 'group', TRUE),
(1, '5101', 'Salaries & Wages', 'expense', 'operating', TRUE),
(1, '5102', 'Rent Expense', 'expense', 'operating', TRUE),
(1, '5103', 'Electricity & Utilities', 'expense', 'operating', TRUE),
(1, '5104', 'Internet & Communication', 'expense', 'operating', TRUE),
(1, '5105', 'Transportation Expense', 'expense', 'operating', TRUE),
(1, '5106', 'Marketing & Advertising', 'expense', 'operating', TRUE),
(1, '5107', 'Office Supplies', 'expense', 'operating', TRUE),
(1, '5108', 'Repairs & Maintenance', 'expense', 'operating', TRUE),
(1, '5109', 'Travel Expense', 'expense', 'operating', TRUE),
(1, '5110', 'Bank Charges', 'expense', 'operating', TRUE),
(1, '5111', 'Depreciation Expense', 'expense', 'operating', TRUE),
(1, '5112', 'Insurance Expense', 'expense', 'operating', TRUE),
(1, '5113', 'Printing & Stationery', 'expense', 'operating', TRUE),
(1, '5114', 'Miscellaneous Expense', 'expense', 'operating', TRUE);
