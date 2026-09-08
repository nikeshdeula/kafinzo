<?php
$conn = new mysqli('localhost', 'anamolfa_kafinzo_user', 'k4~CCPWw%Cv]m-o', 'anamolfa_kafinzo');
if ($conn->connect_error) { die('FAIL: ' . $conn->connect_error); }

$queries = [
    "ALTER TABLE customers ADD COLUMN van_number VARCHAR(50) AFTER vat_number",
    "ALTER TABLE suppliers ADD COLUMN van_number VARCHAR(50) AFTER vat_number",
];

foreach ($queries as $sql) {
    $r = $conn->query($sql);
    if ($r) echo "OK: $sql\n";
    else {
        $e = $conn->error;
        if (str_contains($e, 'Duplicate')) echo "SKIP: $sql\n";
        else echo "ERR: $e\n";
    }
}

$conn->close();
echo "Done.\n";
unlink(__FILE__);
