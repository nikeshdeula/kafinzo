<?php
namespace App\Models;
use App\Core\Database;

class PurchaseStatement {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function getStatement(int $bid = 0, ?string $from = null, ?string $to = null, ?int $supplier_id = null): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $rows = [];

        $sql = "SELECT pb.id, pb.bill_number AS ref_number, pb.bill_date AS date, s.name AS party_name, pb.subtotal, pb.tax_amount, pb.discount_amount, pb.total_amount, pb.paid_amount, pb.status, 'bill' AS type FROM purchase_bills pb LEFT JOIN suppliers s ON pb.supplier_id=s.id WHERE pb.business_id=:bid";
        $params = ['bid'=>$bid];
        if ($from) { $sql .= " AND pb.bill_date >= :from"; $params['from'] = $from; }
        if ($to) { $sql .= " AND pb.bill_date <= :to"; $params['to'] = $to; }
        if ($supplier_id) { $sql .= " AND pb.supplier_id = :sid"; $params['sid'] = $supplier_id; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = array_merge($rows, $stmt->fetchAll());

        $sql = "SELECT pp.id, pp.payment_number AS ref_number, pp.payment_date AS date, s.name AS party_name, 0 AS subtotal, 0 AS tax_amount, 0 AS discount_amount, pp.amount AS total_amount, pp.amount AS paid_amount, 'completed' AS status, 'payment' AS type FROM purchase_payments pp LEFT JOIN suppliers s ON pp.supplier_id=s.id WHERE pp.business_id=:bid";
        $params = ['bid'=>$bid];
        if ($from) { $sql .= " AND pp.payment_date >= :from"; $params['from'] = $from; }
        if ($to) { $sql .= " AND pp.payment_date <= :to"; $params['to'] = $to; }
        if ($supplier_id) { $sql .= " AND pp.supplier_id = :sid"; $params['sid'] = $supplier_id; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = array_merge($rows, $stmt->fetchAll());

        usort($rows, function($a, $b) { return strcmp($a['date'], $b['date']); });
        return $rows;
    }

    public function getSummary(int $bid = 0, ?string $from = null, ?string $to = null, ?int $supplier_id = null): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $rows = $this->getStatement($bid, $from, $to, $supplier_id);
        $totalPurchases = 0;
        $totalPayments = 0;
        $totalOutstanding = 0;
        foreach ($rows as $row) {
            if ($row['type'] === 'payment') {
                $totalPayments += $row['total_amount'];
            } else {
                $totalPurchases += $row['total_amount'];
                $totalOutstanding += ($row['total_amount'] - $row['paid_amount']);
            }
        }
        return [
            'total_purchases' => $totalPurchases,
            'total_payments' => $totalPayments,
            'total_outstanding' => $totalOutstanding,
            'transaction_count' => count($rows)
        ];
    }

    public function suppliers(int $bid = 0): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT id, name FROM suppliers WHERE business_id=:bid AND status='active' ORDER BY name");
        $s->execute(['bid'=>$bid]);
        return $s->fetchAll();
    }
}
