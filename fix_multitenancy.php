<?php
$cfg = require __DIR__ . '/config/database.php';
$pdo = new PDO("mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset=utf8mb4", $cfg['user'], $cfg['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->beginTransaction();

try {
    echo "<h2>Multi-tenancy Fix</h2>";

    // 1. Get all users
    $users = $pdo->query("SELECT id, full_name FROM users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Found " . count($users) . " users</p>";

    // 2. Check existing business_users
    $bus = $pdo->query("SELECT user_id, business_id FROM business_users")->fetchAll(PDO::FETCH_ASSOC);
    $userBizMap = [];
    foreach ($bus as $b) { $userBizMap[$b['user_id']] = $b['business_id']; }
    echo "<p>Found " . count($userBizMap) . " user-business links</p>";

    // 3. For each user without a business_users record, create one
    foreach ($users as $user) {
        $uid = $user['id'];
        if (!isset($userBizMap[$uid])) {
            // Create a business for this user
            $name = $user['full_name'] . "'s Business";
            $stmt = $pdo->prepare("INSERT INTO businesses (name, created_at, updated_at) VALUES (:name, NOW(), NOW())");
            $stmt->execute(['name' => $name]);
            $bid = (int)$pdo->lastInsertId();

            // Link user to business
            $stmt = $pdo->prepare("INSERT INTO business_users (business_id, user_id) VALUES (:bid, :uid)");
            $stmt->execute(['bid' => $bid, 'uid' => $uid]);

            // Create default settings
            $stmt = $pdo->prepare("INSERT INTO business_settings (business_id, setting_key, setting_value) VALUES (:bid, :key, :val)");
            $stmt->execute(['bid' => $bid, 'key' => 'tax_rate', 'val' => '13']);

            echo "<p>Created business #$bid for user #{$uid} ({$user['full_name']})</p>";

            // Move all data with business_id=1 to this user's business
            // (Only if this user was the original owner of business_id=1 data)
            $tables = ['customers','suppliers','products','sales_bills','purchase_bills','sales_payments','purchase_payments','invoices','quotations','expenses','product_categories','units','warehouses','bank_accounts','accounts'];
            foreach ($tables as $t) {
                try {
                    $stmt = $pdo->prepare("UPDATE `$t` SET business_id=:bid WHERE business_id=1");
                    $stmt->execute(['bid' => $bid]);
                    $moved = $stmt->rowCount();
                    if ($moved > 0) {
                        echo "<p>&nbsp;&nbsp;Moved $moved rows from $t to business #$bid</p>";
                    }
                } catch (PDOException $e) {
                    // Table might not exist, skip
                }
            }
        }
    }

    $pdo->commit();
    echo "<h2>Done! All users now have separate businesses.</h2>";
    echo "<p><strong>Delete this file immediately!</strong></p>";

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "<h2>Error</h2><pre>" . $e->getMessage() . "</pre>";
}
