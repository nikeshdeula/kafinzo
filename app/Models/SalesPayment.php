<?php
namespace App\Models;
use App\Core\Database;

class SalesPayment {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 0): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT sp.*,c.name AS customer_name,c.address AS customer_address,c.branch AS customer_branch,i.invoice_number FROM sales_payments sp LEFT JOIN customers c ON sp.customer_id=c.id LEFT JOIN invoices i ON sp.invoice_id=i.id WHERE sp.business_id=:bid ORDER BY sp.id ASC");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }

    public function find(int $id, int $bid = 0): array|false {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("SELECT sp.*,c.name AS customer_name,c.address AS customer_address,c.branch AS customer_branch,i.invoice_number FROM sales_payments sp LEFT JOIN customers c ON sp.customer_id=c.id LEFT JOIN invoices i ON sp.invoice_id=i.id WHERE sp.id=:id AND sp.business_id=:bid LIMIT 1");
        $s->execute(['id'=>$id,'bid'=>$bid]); return $s->fetch();
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO sales_payments (business_id,invoice_id,customer_id,payment_date,payment_method,reference_number,amount,notes) VALUES (:bid,:iid,:cid,:pdate,:method,:ref,:amount,:notes)");
        $s->execute(['bid'=>$d['business_id']??1,'iid'=>$d['invoice_id'],'cid'=>$d['customer_id'],'pdate'=>$d['payment_date'],'method'=>$d['payment_method'],'ref'=>$d['reference_number']??null,'amount'=>$d['amount'],'notes'=>$d['notes']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function totalReceived(int $bid = 0): float {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT COALESCE(SUM(amount),0) FROM sales_payments WHERE business_id=:bid");
        $s->execute(['bid'=>$bid]); return (float)$s->fetchColumn();
    }
}
