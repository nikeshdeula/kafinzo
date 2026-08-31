<?php
$cfg = require __DIR__ . '/config/database.php';
$pdo = new PDO("mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset=utf8mb4", $cfg['user'], $cfg['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "<h2>Users</h2><pre>";
$stmt = $pdo->query("SELECT id, full_name, email, status FROM users");
echo $stmt->fetchAll(PDO::FETCH_ASSOC) ? print_r($stmt->fetchAll(PDO::FETCH_ASSOC), true) : "No users";

echo "</pre><h2>Business Users (user-business links)</h2><pre>";
$stmt = $pdo->query("SELECT * FROM business_users");
echo print_r($stmt->fetchAll(PDO::FETCH_ASSOC), true);

echo "</pre><h2>Businesses</h2><pre>";
$stmt = $pdo->query("SELECT * FROM businesses");
echo print_r($stmt->fetchAll(PDO::FETCH_ASSOC), true);

echo "</pre><h2>Data per business_id</h2><pre>";
$tables = ['customers','suppliers','products','sales_bills','purchase_bills','sales_payments','purchase_payments','invoices','quotations','expenses'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT business_id, COUNT(*) as cnt FROM `$t` GROUP BY business_id");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "$t: " . json_encode($rows) . "\n";
    } catch (PDOException $e) {
        echo "$t: ERROR - " . $e->getMessage() . "\n";
    }
}
echo "</pre>";
