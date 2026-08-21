<?php
namespace App\Models;
use App\Core\Database;

class Invoice {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 1): array {
        $s = $this->db->prepare("SELECT i.*,c.name AS customer_name FROM invoices i LEFT JOIN customers c ON i.customer_id=c.id WHERE i.business_id=:bid ORDER BY i.invoice_date DESC, i.id DESC");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }

    public function find(int $id): array|false {
        $s = $this->db->prepare("SELECT i.*,c.name AS customer_name FROM invoices i LEFT JOIN customers c ON i.customer_id=c.id WHERE i.id=:id LIMIT 1");
        $s->execute(['id'=>$id]); return $s->fetch();
    }

    public function items(int $invoiceId): array {
        $s = $this->db->prepare("SELECT * FROM invoice_items WHERE invoice_id=:iid ORDER BY id ASC");
        $s->execute(['iid'=>$invoiceId]); return $s->fetchAll();
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO invoices (business_id,customer_id,invoice_number,invoice_date,due_date,subtotal,tax_amount,discount_amount,total_amount,paid_amount,status,notes) VALUES (:bid,:cid,:num,:idate,:due,:sub,:tax,:disc,:total,:paid,:status,:notes)");
        $s->execute(['bid'=>$d['business_id']??1,'cid'=>$d['customer_id'],'num'=>$d['invoice_number'],'idate'=>$d['invoice_date'],'due'=>$d['due_date']??null,'sub'=>$d['subtotal']??0,'tax'=>$d['tax_amount']??0,'disc'=>$d['discount_amount']??0,'total'=>$d['total_amount']??0,'paid'=>$d['paid_amount']??0,'status'=>$d['status']??'draft','notes'=>$d['notes']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool {
        $s = $this->db->prepare("UPDATE invoices SET customer_id=:cid,invoice_number=:num,invoice_date=:idate,due_date=:due,subtotal=:sub,tax_amount=:tax,discount_amount=:disc,total_amount=:total,paid_amount=:paid,status=:status,notes=:notes WHERE id=:id");
        return $s->execute(['cid'=>$d['customer_id'],'num'=>$d['invoice_number'],'idate'=>$d['invoice_date'],'due'=>$d['due_date']??null,'sub'=>$d['subtotal']??0,'tax'=>$d['tax_amount']??0,'disc'=>$d['discount_amount']??0,'total'=>$d['total_amount']??0,'paid'=>$d['paid_amount']??0,'status'=>$d['status'],'notes'=>$d['notes']??null,'id'=>$id]);
    }

    public function delete(int $id): bool {
        $this->db->prepare("DELETE FROM invoice_items WHERE invoice_id=:id")->execute(['id'=>$id]);
        $s = $this->db->prepare("DELETE FROM invoices WHERE id=:id");
        return $s->execute(['id'=>$id]);
    }

    public function nextNumber(int $bid = 1): string {
        $y = date('y');
        $prefix = "INV-{$y}-";
        $s = $this->db->prepare("SELECT invoice_number FROM invoices WHERE business_id=:bid AND invoice_number LIKE :p ORDER BY id DESC LIMIT 1");
        $s->execute(['bid'=>$bid,'p'=>$prefix.'%']);
        $last = $s->fetchColumn();
        if ($last) {
            $num = (int)substr($last, strlen($prefix)) + 1;
        } else {
            $num = 1;
        }
        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }

    public function updateStatus(int $id, string $status): bool {
        $s = $this->db->prepare("UPDATE invoices SET status=:status WHERE id=:id");
        return $s->execute(['status'=>$status,'id'=>$id]);
    }
}
