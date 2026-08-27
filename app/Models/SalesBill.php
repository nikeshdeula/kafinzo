<?php
namespace App\Models;
use App\Core\Database;

class SalesBill {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = $_SESSION['business_id'] ?? 1, ?int $customer_id = null, ?string $status = null): array {
        $sql = "SELECT b.*, c.name AS customer_name FROM sales_bills b LEFT JOIN customers c ON b.customer_id=c.id WHERE b.business_id=:bid";
        $params = ['bid'=>$bid];
        if ($customer_id) { $sql .= " AND b.customer_id=:cid"; $params['cid'] = $customer_id; }
        if ($status) { $sql .= " AND b.status=:st"; $params['st'] = $status; }
        $sql .= " ORDER BY b.bill_date DESC, b.id DESC";
        $s = $this->db->prepare($sql);
        $s->execute($params);
        return $s->fetchAll();
    }

    public function find(int $id): array|false {
        $s = $this->db->prepare("SELECT * FROM sales_bills WHERE id=:id LIMIT 1");
        $s->execute(['id'=>$id]); return $s->fetch();
    }

    public function findWithItems(int $id): array|false {
        $bill = $this->find($id);
        if (!$bill) return false;
        $s = $this->db->prepare("SELECT * FROM sales_bill_items WHERE sales_bill_id=:bid");
        $s->execute(['bid'=>$id]);
        $bill['items'] = $s->fetchAll();
        return $bill;
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO sales_bills (business_id,customer_id,bill_number,bill_date,due_date,subtotal,tax_amount,discount_amount,total_amount,tds_amount,paid_amount,status,notes) VALUES (:bid,:cid,:bn,:bd,:dd,:sub,:tax,:disc,:tot,:tds,:paid,:st,:notes)");
        $s->execute([
            'bid'=>$d['business_id']??1,
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

    public function update(int $id, array $d): bool {
        $s = $this->db->prepare("UPDATE sales_bills SET customer_id=:cid,bill_number=:bn,bill_date=:bd,due_date=:dd,subtotal=:sub,tax_amount=:tax,discount_amount=:disc,total_amount=:tot,tds_amount=:tds,paid_amount=:paid,status=:st,notes=:notes WHERE id=:id");
        return $s->execute([
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
            'notes'=>$d['notes']??null,
            'id'=>$id
        ]);
    }

    public function delete(int $id): bool {
        $this->db->prepare("DELETE FROM sales_bill_items WHERE sales_bill_id=:id")->execute(['id'=>$id]);
        $s = $this->db->prepare("DELETE FROM sales_bills WHERE id=:id");
        return $s->execute(['id'=>$id]);
    }

    public function nextNumber(int $bid = $_SESSION['business_id'] ?? 1): string {
        $s = $this->db->prepare("SELECT COUNT(*) FROM sales_bills WHERE business_id=:bid");
        $s->execute(['bid'=>$bid]);
        $count = (int)$s->fetchColumn() + 1;
        return (string)$count;
    }

    public function saveItems(int $bill_id, array $items): void {
        $this->db->prepare("DELETE FROM sales_bill_items WHERE sales_bill_id=:bid")->execute(['bid'=>$bill_id]);
        $s = $this->db->prepare("INSERT INTO sales_bill_items (sales_bill_id,product_id,description,quantity,unit_price,discount_pct,tax_rate,amount) VALUES (:bid,:pid,:desc,:qty,:price,:disc,:tax,:amt)");
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
