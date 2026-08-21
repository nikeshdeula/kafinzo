<?php
namespace App\Models;
use App\Core\Database;

class SalesStatement {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function getStatement(int $bid = 1, ?string $from = null, ?string $to = null, ?int $customer_id = null): array {
        $rows = [];

        $sql = "SELECT sb.id, sb.bill_number AS ref_number, sb.bill_date AS date, c.name AS party_name, sb.subtotal, sb.tax_amount, sb.discount_amount, sb.total_amount, sb.paid_amount, sb.status, 'bill' AS type FROM sales_bills sb LEFT JOIN customers c ON sb.customer_id=c.id WHERE sb.business_id=:bid";
        $params = ['bid'=>$bid];
        if ($from) { $sql .= " AND sb.bill_date >= :from"; $params['from'] = $from; }
        if ($to) { $sql .= " AND sb.bill_date <= :to"; $params['to'] = $to; }
        if ($customer_id) { $sql .= " AND sb.customer_id = :cid"; $params['cid'] = $customer_id; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = array_merge($rows, $stmt->fetchAll());

        $sql = "SELECT i.id, i.invoice_number AS ref_number, i.invoice_date AS date, c.name AS party_name, i.subtotal, i.tax_amount, i.discount_amount, i.total_amount, i.paid_amount, i.status, 'invoice' AS type FROM invoices i LEFT JOIN customers c ON i.customer_id=c.id WHERE i.business_id=:bid";
        $params = ['bid'=>$bid];
        if ($from) { $sql .= " AND i.invoice_date >= :from"; $params['from'] = $from; }
        if ($to) { $sql .= " AND i.invoice_date <= :to"; $params['to'] = $to; }
        if ($customer_id) { $sql .= " AND i.customer_id = :cid"; $params['cid'] = $customer_id; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = array_merge($rows, $stmt->fetchAll());

        $sql = "SELECT sp.id, sp.reference_number AS ref_number, sp.payment_date AS date, c.name AS party_name, 0 AS subtotal, 0 AS tax_amount, 0 AS discount_amount, sp.amount AS total_amount, sp.amount AS paid_amount, 'completed' AS status, 'payment' AS type FROM sales_payments sp LEFT JOIN customers c ON sp.customer_id=c.id WHERE sp.business_id=:bid";
        $params = ['bid'=>$bid];
        if ($from) { $sql .= " AND sp.payment_date >= :from"; $params['from'] = $from; }
        if ($to) { $sql .= " AND sp.payment_date <= :to"; $params['to'] = $to; }
        if ($customer_id) { $sql .= " AND sp.customer_id = :cid"; $params['cid'] = $customer_id; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = array_merge($rows, $stmt->fetchAll());

        usort($rows, function($a, $b) { return strcmp($a['date'], $b['date']); });
        return $rows;
    }

    public function getSummary(int $bid = 1, ?string $from = null, ?string $to = null, ?int $customer_id = null): array {
        $rows = $this->getStatement($bid, $from, $to, $customer_id);
        $totalSales = 0;
        $totalPayments = 0;
        $totalOutstanding = 0;
        foreach ($rows as $row) {
            if ($row['type'] === 'payment') {
                $totalPayments += $row['total_amount'];
            } else {
                $totalSales += $row['total_amount'];
                $totalOutstanding += ($row['total_amount'] - $row['paid_amount']);
            }
        }
        return [
            'total_sales' => $totalSales,
            'total_payments' => $totalPayments,
            'total_outstanding' => $totalOutstanding,
            'transaction_count' => count($rows)
        ];
    }

    public function customers(int $bid = 1): array {
        $s = $this->db->prepare("SELECT id, name FROM customers WHERE business_id=:bid AND status='active' ORDER BY name");
        $s->execute(['bid'=>$bid]);
        return $s->fetchAll();
    }
}
