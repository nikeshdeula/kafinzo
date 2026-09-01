<?php
namespace App\Models;
use App\Core\Database;

class Invoice {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 0): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT i.*,c.name AS customer_name,c.address AS customer_address,c.branch AS customer_branch FROM invoices i LEFT JOIN customers c ON i.customer_id=c.id WHERE i.business_id=:bid ORDER BY i.id ASC");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }

    public function find(int $id, int $bid = 0): array|false {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("SELECT i.*,c.name AS customer_name,c.address AS customer_address,c.branch AS customer_branch FROM invoices i LEFT JOIN customers c ON i.customer_id=c.id WHERE i.id=:id AND i.business_id=:bid LIMIT 1");
        $s->execute(['id'=>$id,'bid'=>$bid]); return $s->fetch();
    }

    public function items(int $invoiceId): array {
        $s = $this->db->prepare("SELECT * FROM invoice_items WHERE invoice_id=:iid ORDER BY id ASC");
        $s->execute(['iid'=>$invoiceId]); return $s->fetchAll();
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO invoices (business_id,customer_id,invoice_number,invoice_date,due_date,subtotal,tax_amount,discount_amount,total_amount,paid_amount,status,notes) VALUES (:bid,:cid,:num,:idate,:due,:sub,:tax,:disc,:total,:paid,:status,:notes)");
        $s->execute(['bid'=>$d['business_id']??($_SESSION['business_id']??0),'cid'=>$d['customer_id'],'num'=>$d['invoice_number'],'idate'=>$d['invoice_date'],'due'=>$d['due_date']??null,'sub'=>$d['subtotal']??0,'tax'=>$d['tax_amount']??0,'disc'=>$d['discount_amount']??0,'total'=>$d['total_amount']??0,'paid'=>$d['paid_amount']??0,'status'=>$d['status']??'draft','notes'=>$d['notes']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("UPDATE invoices SET customer_id=:cid,invoice_number=:num,invoice_date=:idate,due_date=:due,subtotal=:sub,tax_amount=:tax,discount_amount=:disc,total_amount=:total,paid_amount=:paid,status=:status,notes=:notes WHERE id=:id AND business_id=:bid");
        return $s->execute(['cid'=>$d['customer_id'],'num'=>$d['invoice_number'],'idate'=>$d['invoice_date'],'due'=>$d['due_date']??null,'sub'=>$d['subtotal']??0,'tax'=>$d['tax_amount']??0,'disc'=>$d['discount_amount']??0,'total'=>$d['total_amount']??0,'paid'=>$d['paid_amount']??0,'status'=>$d['status'],'notes'=>$d['notes']??null,'id'=>$id,'bid'=>$bid]);
    }

    public function delete(int $id, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $this->db->prepare("DELETE FROM invoice_items WHERE invoice_id=:id AND EXISTS (SELECT 1 FROM invoices WHERE id=:id2 AND business_id=:bid)")->execute(['id'=>$id,'id2'=>$id,'bid'=>$bid]);
        $s = $this->db->prepare("DELETE FROM invoices WHERE id=:id AND business_id=:bid");
        return $s->execute(['id'=>$id,'bid'=>$bid]);
    }

    public function nextNumber(int $bid = 0): string {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
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

    public function updateStatus(int $id, string $status, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("UPDATE invoices SET status=:status WHERE id=:id AND business_id=:bid");
        return $s->execute(['status'=>$status,'id'=>$id,'bid'=>$bid]);
    }
}
