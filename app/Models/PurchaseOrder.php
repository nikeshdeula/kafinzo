<?php
namespace App\Models;
use App\Core\Database;

class PurchaseOrder {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 0, ?int $supplier_id = null, ?string $status = null): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $sql = "SELECT o.*, s.name AS supplier_name, s.address AS supplier_address, s.branch AS supplier_branch FROM purchase_orders o LEFT JOIN suppliers s ON o.supplier_id=s.id WHERE o.business_id=:bid";
        $params = ['bid'=>$bid];
        if ($supplier_id) { $sql .= " AND o.supplier_id=:sid"; $params['sid'] = $supplier_id; }
        if ($status) { $sql .= " AND o.status=:st"; $params['st'] = $status; }
        $sql .= " ORDER BY o.id ASC";
        $s = $this->db->prepare($sql);
        $s->execute($params);
        return $s->fetchAll();
    }

    public function find(int $id): array|false {
        $s = $this->db->prepare("SELECT * FROM purchase_orders WHERE id=:id LIMIT 1");
        $s->execute(['id'=>$id]); return $s->fetch();
    }

    public function findWithItems(int $id): array|false {
        $order = $this->find($id);
        if (!$order) return false;
        $s = $this->db->prepare("SELECT * FROM purchase_order_items WHERE purchase_order_id=:oid");
        $s->execute(['oid'=>$id]);
        $order['items'] = $s->fetchAll();
        return $order;
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO purchase_orders (business_id,supplier_id,order_number,order_date,expected_delivery,subtotal,tax_amount,discount_amount,total_amount,status,notes) VALUES (:bid,:sid,:on,:od,:ed,:sub,:tax,:disc,:tot,:st,:notes)");
        $s->execute([
            'bid'=>$d['business_id']??1,
            'sid'=>$d['supplier_id'],
            'on'=>$d['order_number'],
            'od'=>$d['order_date'],
            'ed'=>$d['expected_delivery']??null,
            'sub'=>$d['subtotal']??0,
            'tax'=>$d['tax_amount']??0,
            'disc'=>$d['discount_amount']??0,
            'tot'=>$d['total_amount']??0,
            'st'=>$d['status']??'draft',
            'notes'=>$d['notes']??null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool {
        $s = $this->db->prepare("UPDATE purchase_orders SET supplier_id=:sid,order_number=:on,order_date=:od,expected_delivery=:ed,subtotal=:sub,tax_amount=:tax,discount_amount=:disc,total_amount=:tot,status=:st,notes=:notes WHERE id=:id");
        return $s->execute([
            'sid'=>$d['supplier_id'],
            'on'=>$d['order_number'],
            'od'=>$d['order_date'],
            'ed'=>$d['expected_delivery']??null,
            'sub'=>$d['subtotal']??0,
            'tax'=>$d['tax_amount']??0,
            'disc'=>$d['discount_amount']??0,
            'tot'=>$d['total_amount']??0,
            'st'=>$d['status']??'draft',
            'notes'=>$d['notes']??null,
            'id'=>$id
        ]);
    }

    public function delete(int $id): bool {
        $this->db->prepare("DELETE FROM purchase_order_items WHERE purchase_order_id=:id")->execute(['id'=>$id]);
        $s = $this->db->prepare("DELETE FROM purchase_orders WHERE id=:id");
        return $s->execute(['id'=>$id]);
    }

    public function nextNumber(int $bid = 0): string {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT COUNT(*) FROM purchase_orders WHERE business_id=:bid");
        $s->execute(['bid'=>$bid]);
        $count = (int)$s->fetchColumn() + 1;
        return 'PO-' . str_pad((string)$count, 5, '0', STR_PAD_LEFT);
    }

    public function saveItems(int $order_id, array $items): void {
        $this->db->prepare("DELETE FROM purchase_order_items WHERE purchase_order_id=:oid")->execute(['oid'=>$order_id]);
        $s = $this->db->prepare("INSERT INTO purchase_order_items (purchase_order_id,product_id,description,quantity,unit_price,discount_pct,tax_rate,amount) VALUES (:oid,:pid,:desc,:qty,:price,:disc,:tax,:amt)");
        foreach ($items as $item) {
            $s->execute([
                'oid'=>$order_id,
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
