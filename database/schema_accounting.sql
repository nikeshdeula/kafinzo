-- Journal Entries and Ledger Entries tables for Kafinzo Accounting module

CREATE TABLE IF NOT EXISTS journal_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    journal_number VARCHAR(20) NOT NULL,
    entry_date DATE NOT NULL,
    description TEXT NOT NULL,
    reference VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_business (business_id),
    INDEX idx_number (journal_number),
    INDEX idx_date (entry_date)
);

CREATE TABLE IF NOT EXISTS ledger_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL DEFAULT 1,
    journal_entry_id INT NOT NULL,
    account_id INT NOT NULL,
    description TEXT,
    debit DECIMAL(15,2) DEFAULT 0,
    credit DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE RESTRICT,
    INDEX idx_business (business_id),
    INDEX idx_journal (journal_entry_id),
    INDEX idx_account (account_id)
);
