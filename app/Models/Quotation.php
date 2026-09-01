<?php
namespace App\Models;
use App\Core\Database;

class Quotation {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 0): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT q.*,c.name AS customer_name,c.address AS customer_address,c.branch AS customer_branch FROM quotations q LEFT JOIN customers c ON q.customer_id=c.id WHERE q.business_id=:bid ORDER BY q.id ASC");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }

    public function find(int $id, int $bid = 0): array|false {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("SELECT q.*,c.name AS customer_name,c.address AS customer_address,c.branch AS customer_branch FROM quotations q LEFT JOIN customers c ON q.customer_id=c.id WHERE q.id=:id AND q.business_id=:bid LIMIT 1");
        $s->execute(['id'=>$id,'bid'=>$bid]); return $s->fetch();
    }

    public function items(int $quotationId): array {
        $s = $this->db->prepare("SELECT * FROM quotation_items WHERE quotation_id=:qid ORDER BY id ASC");
        $s->execute(['qid'=>$quotationId]); return $s->fetchAll();
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO quotations (business_id,customer_id,quotation_number,quotation_date,valid_until,subtotal,tax_amount,discount_amount,total_amount,status,notes) VALUES (:bid,:cid,:num,:qdate,:valid,:sub,:tax,:disc,:total,:status,:notes)");
        $s->execute(['bid'=>$d['business_id']??($_SESSION['business_id']??0),'cid'=>$d['customer_id'],'num'=>$d['quotation_number'],'qdate'=>$d['quotation_date'],'valid'=>$d['valid_until']??null,'sub'=>$d['subtotal']??0,'tax'=>$d['tax_amount']??0,'disc'=>$d['discount_amount']??0,'total'=>$d['total_amount']??0,'status'=>$d['status']??'draft','notes'=>$d['notes']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("UPDATE quotations SET customer_id=:cid,quotation_number=:num,quotation_date=:qdate,valid_until=:valid,subtotal=:sub,tax_amount=:tax,discount_amount=:disc,total_amount=:total,status=:status,notes=:notes WHERE id=:id AND business_id=:bid");
        return $s->execute(['cid'=>$d['customer_id'],'num'=>$d['quotation_number'],'qdate'=>$d['quotation_date'],'valid'=>$d['valid_until']??null,'sub'=>$d['subtotal']??0,'tax'=>$d['tax_amount']??0,'disc'=>$d['discount_amount']??0,'total'=>$d['total_amount']??0,'status'=>$d['status'],'notes'=>$d['notes']??null,'id'=>$id,'bid'=>$bid]);
    }

    public function delete(int $id, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $this->db->prepare("DELETE FROM quotation_items WHERE quotation_id=:id AND EXISTS (SELECT 1 FROM quotations WHERE id=:id2 AND business_id=:bid)")->execute(['id'=>$id,'id2'=>$id,'bid'=>$bid]);
        $s = $this->db->prepare("DELETE FROM quotations WHERE id=:id AND business_id=:bid");
        return $s->execute(['id'=>$id,'bid'=>$bid]);
    }

    public function nextNumber(int $bid = 0): string {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $y = date('y');
        $prefix = "QT-{$y}-";
        $s = $this->db->prepare("SELECT quotation_number FROM quotations WHERE business_id=:bid AND quotation_number LIKE :p ORDER BY id DESC LIMIT 1");
        $s->execute(['bid'=>$bid,'p'=>$prefix.'%']);
        $last = $s->fetchColumn();
        if ($last) {
            $num = (int)substr($last, strlen($prefix)) + 1;
        } else {
            $num = 1;
        }
        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }
}
