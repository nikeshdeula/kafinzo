<?php
namespace App\Models;
use App\Core\Database;

class PurchasePayment {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 1): array {
        $s = $this->db->prepare("SELECT p.*, s.name AS supplier_name FROM purchase_payments p LEFT JOIN suppliers s ON p.supplier_id=s.id WHERE p.business_id=:bid ORDER BY p.payment_date DESC, p.id DESC");
        $s->execute(['bid'=>$bid]);
        return $s->fetchAll();
    }

    public function find(int $id): array|false {
        $s = $this->db->prepare("SELECT * FROM purchase_payments WHERE id=:id LIMIT 1");
        $s->execute(['id'=>$id]); return $s->fetch();
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO purchase_payments (business_id,bill_id,order_id,supplier_id,payment_number,payment_date,amount,payment_method,reference,notes) VALUES (:bid,:bill_id,:order_id,:sid,:pn,:pd,:amt,:pm,:ref,:notes)");
        $s->execute([
            'bid'=>$d['business_id']??1,
            'bill_id'=>$d['bill_id']??null,
            'order_id'=>$d['order_id']??null,
            'sid'=>$d['supplier_id'],
            'pn'=>$d['payment_number'],
            'pd'=>$d['payment_date'],
            'amt'=>$d['amount'],
            'pm'=>$d['payment_method'],
            'ref'=>$d['reference']??null,
            'notes'=>$d['notes']??null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function nextNumber(int $bid = 1): string {
        $s = $this->db->prepare("SELECT COUNT(*) FROM purchase_payments WHERE business_id=:bid");
        $s->execute(['bid'=>$bid]);
        $count = (int)$s->fetchColumn() + 1;
        return 'PP-' . str_pad((string)$count, 5, '0', STR_PAD_LEFT);
    }
}
