<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain');

require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;
$db = Database::getInstance()->getConnection();

$queries = [
    "ALTER TABLE sales_bill_items ADD COLUMN unit_id INT DEFAULT NULL AFTER product_id",
    "ALTER TABLE purchase_bill_items ADD COLUMN unit_id INT DEFAULT NULL AFTER product_id",
];

foreach ($queries as $q) {
    try {
        $db->exec($q);
        echo "OK: $q\n";
    } catch (PDOException $e) {
        echo "SKIP: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
