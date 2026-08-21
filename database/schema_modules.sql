-- Full Schema Extension for all Kafinzo modules

-- CUSTOMERS
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    name VARCHAR(150) NOT NULL,
    company_name VARCHAR(150),
    pan VARCHAR(20),
    vat_number VARCHAR(30),
    phone VARCHAR(30),
    email VARCHAR(100),
    address TEXT,
    credit_limit DECIMAL(15,2) DEFAULT 0,
    opening_balance DECIMAL(15,2) DEFAULT 0,
    payment_terms INT DEFAULT 0 COMMENT 'days',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_business (business_id)
);

-- SUPPLIERS
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    name VARCHAR(150) NOT NULL,
    company_name VARCHAR(150),
    pan VARCHAR(20),
    vat_number VARCHAR(30),
    phone VARCHAR(30),
    email VARCHAR(100),
    address TEXT,
    opening_balance DECIMAL(15,2) DEFAULT 0,
    payment_terms INT DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_business (business_id)
);

-- PRODUCT CATEGORIES
CREATE TABLE IF NOT EXISTS product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- UNITS
CREATE TABLE IF NOT EXISTS units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    name VARCHAR(50) NOT NULL,
    abbreviation VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- WAREHOUSES
CREATE TABLE IF NOT EXISTS warehouses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    name VARCHAR(100) NOT NULL,
    location TEXT,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PRODUCTS
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    category_id INT,
    unit_id INT,
    name VARCHAR(200) NOT NULL,
    sku VARCHAR(100),
    barcode VARCHAR(100),
    type ENUM('product','service') DEFAULT 'product',
    purchase_price DECIMAL(15,2) DEFAULT 0,
    selling_price DECIMAL(15,2) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    opening_stock DECIMAL(15,2) DEFAULT 0,
    current_stock DECIMAL(15,2) DEFAULT 0,
    minimum_stock DECIMAL(15,2) DEFAULT 0,
    description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_business (business_id),
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL
);

-- EXPENSE CATEGORIES
CREATE TABLE IF NOT EXISTS expense_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- EXPENSES
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    category_id INT,
    expense_date DATE NOT NULL,
    vendor VARCHAR(150),
    amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    payment_account VARCHAR(100),
    description TEXT,
    notes TEXT,
    reference VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id) ON DELETE SET NULL
);

-- BANK ACCOUNTS
CREATE TABLE IF NOT EXISTS bank_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    account_type ENUM('bank','cash') DEFAULT 'bank',
    bank_name VARCHAR(100),
    account_name VARCHAR(100) NOT NULL,
    account_number VARCHAR(50),
    branch VARCHAR(100),
    opening_balance DECIMAL(15,2) DEFAULT 0,
    current_balance DECIMAL(15,2) DEFAULT 0,
    is_default BOOLEAN DEFAULT FALSE,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- BANK TRANSACTIONS
CREATE TABLE IF NOT EXISTS bank_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    bank_account_id INT NOT NULL,
    transaction_type ENUM('deposit','withdrawal','transfer','charge') NOT NULL,
    transaction_date DATE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    reference VARCHAR(100),
    description TEXT,
    is_reconciled BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id) ON DELETE CASCADE
);

-- INVOICES
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    customer_id INT NOT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE,
    subtotal DECIMAL(15,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) DEFAULT 0,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    status ENUM('draft','unpaid','partial','paid','overdue','cancelled') DEFAULT 'draft',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT
);

-- INVOICE ITEMS
CREATE TABLE IF NOT EXISTS invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    product_id INT,
    description VARCHAR(255),
    quantity DECIMAL(15,2) DEFAULT 1,
    unit_price DECIMAL(15,2) DEFAULT 0,
    discount_pct DECIMAL(5,2) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    amount DECIMAL(15,2) DEFAULT 0,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

-- QUOTATIONS
CREATE TABLE IF NOT EXISTS quotations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    customer_id INT NOT NULL,
    quotation_number VARCHAR(50) NOT NULL,
    quotation_date DATE NOT NULL,
    valid_until DATE,
    subtotal DECIMAL(15,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) DEFAULT 0,
    status ENUM('draft','sent','accepted','rejected','expired','converted') DEFAULT 'draft',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT
);

-- QUOTATION ITEMS
CREATE TABLE IF NOT EXISTS quotation_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quotation_id INT NOT NULL,
    product_id INT,
    description VARCHAR(255),
    quantity DECIMAL(15,2) DEFAULT 1,
    unit_price DECIMAL(15,2) DEFAULT 0,
    discount_pct DECIMAL(5,2) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    amount DECIMAL(15,2) DEFAULT 0,
    FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE
);

-- SALES PAYMENTS
CREATE TABLE IF NOT EXISTS sales_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    invoice_id INT NOT NULL,
    customer_id INT NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash','bank','cheque','other') DEFAULT 'cash',
    reference_number VARCHAR(100),
    amount DECIMAL(15,2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE RESTRICT,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT
);

-- SEED DEFAULT DATA
INSERT IGNORE INTO units (business_id, name, abbreviation) VALUES
(1, 'Piece', 'pcs'), (1, 'Kilogram', 'kg'), (1, 'Gram', 'g'),
(1, 'Litre', 'ltr'), (1, 'Metre', 'm'), (1, 'Box', 'box'),
(1, 'Dozen', 'dz'), (1, 'Set', 'set'), (1, 'Hour', 'hr');

INSERT IGNORE INTO warehouses (business_id, name, location, is_default) VALUES
(1, 'Main Warehouse', 'Head Office', TRUE);

INSERT IGNORE INTO product_categories (business_id, name) VALUES
(1,'General'),(1,'Electronics'),(1,'Clothing'),(1,'Food & Beverage'),(1,'Stationery');

INSERT IGNORE INTO expense_categories (business_id, name) VALUES
(1,'Rent'),(1,'Salary'),(1,'Electricity'),(1,'Internet'),
(1,'Transportation'),(1,'Marketing'),(1,'Office Supplies'),
(1,'Maintenance'),(1,'Travel'),(1,'Bank Charges'),(1,'Other');

INSERT IGNORE INTO bank_accounts (business_id, account_type, account_name, opening_balance, current_balance, is_default) VALUES
(1, 'cash', 'Cash in Hand', 0, 0, TRUE);
