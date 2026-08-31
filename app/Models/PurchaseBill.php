<?php
namespace App\Models;
use App\Core\Database;

class PurchaseBill {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 0, ?int $supplier_id = null, ?string $status = null): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $sql = "SELECT b.*, s.name AS supplier_name, s.address AS supplier_address, s.branch AS supplier_branch FROM purchase_bills b LEFT JOIN suppliers s ON b.supplier_id=s.id WHERE b.business_id=:bid";
        $params = ['bid'=>$bid];
        if ($supplier_id) { $sql .= " AND b.supplier_id=:sid"; $params['sid'] = $supplier_id; }
        if ($status) { $sql .= " AND b.status=:st"; $params['st'] = $status; }
        $sql .= " ORDER BY b.id ASC";
        $s = $this->db->prepare($sql);
        $s->execute($params);
        return $s->fetchAll();
    }

    public function find(int $id, int $bid = 0): array|false {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("SELECT * FROM purchase_bills WHERE id=:id AND business_id=:bid LIMIT 1");
        $s->execute(['id'=>$id,'bid'=>$bid]); return $s->fetch();
    }

    public function findWithItems(int $id, int $bid = 0): array|false {
        $bill = $this->find($id, $bid);
        if (!$bill) return false;
        $s = $this->db->prepare("SELECT * FROM purchase_bill_items WHERE purchase_bill_id=:bid");
        $s->execute(['bid'=>$id]);
        $bill['items'] = $s->fetchAll();
        return $bill;
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO purchase_bills (business_id,supplier_id,bill_number,bill_date,due_date,subtotal,tax_amount,discount_amount,total_amount,paid_amount,status,notes) VALUES (:bid,:sid,:bn,:bd,:dd,:sub,:tax,:disc,:tot,:paid,:st,:notes)");
        $s->execute([
            'bid'=>$d['business_id']??1,
            'sid'=>$d['supplier_id'],
            'bn'=>$d['bill_number'],
            'bd'=>$d['bill_date'],
            'dd'=>$d['due_date']??null,
            'sub'=>$d['subtotal']??0,
            'tax'=>$d['tax_amount']??0,
            'disc'=>$d['discount_amount']??0,
            'tot'=>$d['total_amount']??0,
            'paid'=>$d['paid_amount']??0,
            'st'=>$d['status']??'draft',
            'notes'=>$d['notes']??null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("UPDATE purchase_bills SET supplier_id=:sid,bill_number=:bn,bill_date=:bd,due_date=:dd,subtotal=:sub,tax_amount=:tax,discount_amount=:disc,total_amount=:tot,paid_amount=:paid,status=:st,notes=:notes WHERE id=:id AND business_id=:bid");
        return $s->execute([
            'sid'=>$d['supplier_id'],'bn'=>$d['bill_number'],'bd'=>$d['bill_date'],'dd'=>$d['due_date']??null,'sub'=>$d['subtotal']??0,'tax'=>$d['tax_amount']??0,'disc'=>$d['discount_amount']??0,'tot'=>$d['total_amount']??0,'paid'=>$d['paid_amount']??0,'st'=>$d['status']??'draft','notes'=>$d['notes']??null,'id'=>$id,'bid'=>$bid
        ]);
    }

    public function delete(int $id, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $this->db->prepare("DELETE FROM purchase_bill_items WHERE purchase_bill_id=:id")->execute(['id'=>$id]);
        $s = $this->db->prepare("DELETE FROM purchase_bills WHERE id=:id AND business_id=:bid");
        return $s->execute(['id'=>$id,'bid'=>$bid]);
    }

    public function nextNumber(int $bid = 0): string {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT COUNT(*) FROM purchase_bills WHERE business_id=:bid");
        $s->execute(['bid'=>$bid]);
        $count = (int)$s->fetchColumn() + 1;
        return 'PB-' . str_pad((string)$count, 5, '0', STR_PAD_LEFT);
    }

    public function saveItems(int $bill_id, array $items): void {
        $this->db->prepare("DELETE FROM purchase_bill_items WHERE purchase_bill_id=:bid")->execute(['bid'=>$bill_id]);
        $s = $this->db->prepare("INSERT INTO purchase_bill_items (purchase_bill_id,product_id,description,quantity,unit_price,discount_pct,tax_rate,amount) VALUES (:bid,:pid,:desc,:qty,:price,:disc,:tax,:amt)");
        foreach ($items as $item) {
            $s->execute([
                'bid'=>$bill_id,
                'pid'=>$item['product_id']??null,
                'desc'=>$item['description']??'',
                'qty'=>$item['quantity']??0,
                'price'=>$item['unit_price']??0,
                'disc'=>$item['discount_pct']??0,
                'tax'=>$item['tax_rate']??0,
                'amt'=>$item['amount']??0
            ]);
        }
    }
}
