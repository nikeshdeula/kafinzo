<?php
namespace App\Models;
use App\Core\Database;

class SalesBill {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 0, ?int $customer_id = null, ?string $status = null): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $sql = "SELECT b.*, c.name AS customer_name, c.address AS customer_address, c.branch AS customer_branch FROM sales_bills b LEFT JOIN customers c ON b.customer_id=c.id WHERE b.business_id=:bid";
        $params = ['bid'=>$bid];
        if ($customer_id) { $sql .= " AND b.customer_id=:cid"; $params['cid'] = $customer_id; }
        if ($status) { $sql .= " AND b.status=:st"; $params['st'] = $status; }
        $sql .= " ORDER BY b.id ASC";
        $s = $this->db->prepare($sql);
        $s->execute($params);
        return $s->fetchAll();
    }

    public function find(int $id, int $bid = 0): array|false {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("SELECT * FROM sales_bills WHERE id=:id AND business_id=:bid LIMIT 1");
        $s->execute(['id'=>$id,'bid'=>$bid]); return $s->fetch();
    }

    public function findWithItems(int $id, int $bid = 0): array|false {
        $bill = $this->find($id, $bid);
        if (!$bill) return false;
        $s = $this->db->prepare("SELECT * FROM sales_bill_items WHERE sales_bill_id=:bid");
        $s->execute(['bid'=>$id]);
        $bill['items'] = $s->fetchAll();
        return $bill;
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO sales_bills (business_id,customer_id,bill_number,bill_date,due_date,subtotal,tax_amount,discount_amount,total_amount,tds_amount,paid_amount,status,notes) VALUES (:bid,:cid,:bn,:bd,:dd,:sub,:tax,:disc,:tot,:tds,:paid,:st,:notes)");
        $s->execute([
            'bid'=>$d['business_id']??($_SESSION['business_id']??0),
            'cid'=>$d['customer_id'],
            'bn'=>$d['bill_number'],
            'bd'=>$d['bill_date'],
            'dd'=>$d['due_date']??null,
            'sub'=>$d['subtotal']??0,
            'tax'=>$d['tax_amount']??0,
            'disc'=>$d['discount_amount']??0,
            'tot'=>$d['total_amount']??0,
            'tds'=>$d['tds_amount']??0,
            'paid'=>$d['paid_amount']??0,
            'st'=>$d['status']??'draft',
            'notes'=>$d['notes']??null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("UPDATE sales_bills SET customer_id=:cid,bill_number=:bn,bill_date=:bd,due_date=:dd,subtotal=:sub,tax_amount=:tax,discount_amount=:disc,total_amount=:tot,tds_amount=:tds,paid_amount=:paid,status=:st,notes=:notes WHERE id=:id AND business_id=:bid");
        return $s->execute([
            'cid'=>$d['customer_id'],'bn'=>$d['bill_number'],'bd'=>$d['bill_date'],'dd'=>$d['due_date']??null,'sub'=>$d['subtotal']??0,'tax'=>$d['tax_amount']??0,'disc'=>$d['discount_amount']??0,'tot'=>$d['total_amount']??0,'tds'=>$d['tds_amount']??0,'paid'=>$d['paid_amount']??0,'st'=>$d['status']??'draft','notes'=>$d['notes']??null,'id'=>$id,'bid'=>$bid
        ]);
    }

    public function delete(int $id, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $this->db->prepare("DELETE FROM sales_bill_items WHERE sales_bill_id=:id AND EXISTS (SELECT 1 FROM sales_bills WHERE id=:id2 AND business_id=:bid)")->execute(['id'=>$id,'id2'=>$id,'bid'=>$bid]);
        $s = $this->db->prepare("DELETE FROM sales_bills WHERE id=:id AND business_id=:bid");
        return $s->execute(['id'=>$id,'bid'=>$bid]);
    }

    public function nextNumber(int $bid = 0): string {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT COUNT(*) FROM sales_bills WHERE business_id=:bid");
        $s->execute(['bid'=>$bid]);
        $count = (int)$s->fetchColumn() + 1;
        return (string)$count;
    }

    public function saveItems(int $bill_id, array $items): void {
        $bid = $_SESSION['business_id'] ?? 0;
        $this->db->prepare("DELETE FROM sales_bill_items WHERE sales_bill_id=:bid AND EXISTS (SELECT 1 FROM sales_bills WHERE id=:bid2 AND business_id=:biz)")->execute(['bid'=>$bill_id,'bid2'=>$bill_id,'biz'=>$bid]);
        $s = $this->db->prepare("INSERT INTO sales_bill_items (sales_bill_id,product_id,unit_id,description,quantity,unit_price,discount_pct,tax_rate,amount) VALUES (:bid,:pid,:uid,:desc,:qty,:price,:disc,:tax,:amt)");
        foreach ($items as $item) {
            $s->execute([
                'bid'=>$bill_id,
                'pid'=>$item['product_id']??null,
                'uid'=>$item['unit_id']??null,
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
