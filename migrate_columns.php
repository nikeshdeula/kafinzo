<?php
// Temporary migration script — delete after running

// First, show what database.php has
$cfg = require __DIR__ . '/config/database.php';
echo "<h2>DB Config from config/database.php</h2>";
echo "<pre>";
echo "Host: " . $cfg['host'] . "\n";
echo "DB: " . $cfg['dbname'] . "\n";
echo "User: " . $cfg['user'] . "\n";
echo "Pass: " . $cfg['password'] . "\n";
echo "</pre>";

$host = $cfg['host'];
$dbname = $cfg['dbname'];
$user = $cfg['user'];
$pass = $cfg['password'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = [
        'customers' => ['branch' => "VARCHAR(100) DEFAULT NULL AFTER name", 'address' => "TEXT DEFAULT NULL AFTER branch"],
        'suppliers' => ['branch' => "VARCHAR(100) DEFAULT NULL AFTER name", 'address' => "TEXT DEFAULT NULL AFTER branch"],
        'sales_bills' => [
            'customer_branch' => "VARCHAR(100) DEFAULT NULL AFTER customer_name",
            'customer_address' => "TEXT DEFAULT NULL AFTER customer_branch",
            'customer_phone' => "VARCHAR(50) DEFAULT NULL AFTER customer_address",
            'customer_email' => "VARCHAR(100) DEFAULT NULL AFTER customer_phone",
        ],
        'sales_orders' => [
            'customer_branch' => "VARCHAR(100) DEFAULT NULL AFTER customer_name",
            'customer_address' => "TEXT DEFAULT NULL AFTER customer_branch",
        ],
        'purchase_bills' => [
            'supplier_branch' => "VARCHAR(100) DEFAULT NULL AFTER supplier_name",
            'supplier_address' => "TEXT DEFAULT NULL AFTER supplier_branch",
            'supplier_phone' => "VARCHAR(50) DEFAULT NULL AFTER supplier_address",
            'supplier_email' => "VARCHAR(100) DEFAULT NULL AFTER supplier_phone",
        ],
        'purchase_orders' => [
            'supplier_branch' => "VARCHAR(100) DEFAULT NULL AFTER supplier_name",
            'supplier_address' => "TEXT DEFAULT NULL AFTER supplier_branch",
        ],
        'sales_payments' => [
            'customer_branch' => "VARCHAR(100) DEFAULT NULL AFTER customer_name",
            'customer_address' => "TEXT DEFAULT NULL AFTER customer_branch",
        ],
        'purchase_payments' => [
            'supplier_branch' => "VARCHAR(100) DEFAULT NULL AFTER supplier_name",
            'supplier_address' => "TEXT DEFAULT NULL AFTER supplier_branch",
        ],
        'sales_statements' => [
            'customer_branch' => "VARCHAR(100) DEFAULT NULL AFTER customer_name",
            'customer_address' => "TEXT DEFAULT NULL AFTER customer_branch",
        ],
        'purchase_statements' => [
            'supplier_branch' => "VARCHAR(100) DEFAULT NULL AFTER supplier_name",
            'supplier_address' => "TEXT DEFAULT NULL AFTER supplier_branch",
        ],
        'invoices' => [
            'customer_branch' => "VARCHAR(100) DEFAULT NULL AFTER customer_name",
            'customer_address' => "TEXT DEFAULT NULL AFTER customer_branch",
        ],
        'quotations' => [
            'customer_branch' => "VARCHAR(100) DEFAULT NULL AFTER customer_name",
            'customer_address' => "TEXT DEFAULT NULL AFTER customer_branch",
        ],
    ];

    $results = [];
    foreach ($tables as $table => $columns) {
        foreach ($columns as $col => $def) {
            try {
                $pdo->exec("ALTER TABLE `$table` ADD COLUMN IF NOT EXISTS `$col` $def");
                $results[] = "OK: $table.$col";
            } catch (PDOException $e) {
                if (str_contains($e->getMessage(), 'Duplicate column')) {
                    $results[] = "SKIP: $table.$col (already exists)";
                } else {
                    $results[] = "ERROR: $table.$col — " . $e->getMessage();
                }
            }
        }
    }

    echo "<h2>Migration Complete</h2>";
    echo "<pre>" . implode("\n", $results) . "</pre>";
    echo "<p><strong>Delete this file immediately!</strong></p>";

} catch (PDOException $e) {
    echo "<h2>Connection Error</h2><pre>" . $e->getMessage() . "</pre>";
}
