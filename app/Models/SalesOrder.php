<?php
namespace App\Models;
use App\Core\Database;

class SalesOrder {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = $_SESSION['business_id'] ?? 1, ?int $customer_id = null, ?string $status = null): array {
        $sql = "SELECT o.*, c.name AS customer_name FROM sales_orders o LEFT JOIN customers c ON o.customer_id=c.id WHERE o.business_id=:bid";
        $params = ['bid'=>$bid];
        if ($customer_id) { $sql .= " AND o.customer_id=:cid"; $params['cid'] = $customer_id; }
        if ($status) { $sql .= " AND o.status=:st"; $params['st'] = $status; }
        $sql .= " ORDER BY o.order_date DESC, o.id DESC";
        $s = $this->db->prepare($sql);
        $s->execute($params);
        return $s->fetchAll();
    }

    public function find(int $id): array|false {
        $s = $this->db->prepare("SELECT * FROM sales_orders WHERE id=:id LIMIT 1");
        $s->execute(['id'=>$id]); return $s->fetch();
    }

    public function findWithItems(int $id): array|false {
        $order = $this->find($id);
        if (!$order) return false;
        $s = $this->db->prepare("SELECT * FROM sales_order_items WHERE sales_order_id=:oid");
        $s->execute(['oid'=>$id]);
        $order['items'] = $s->fetchAll();
        return $order;
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO sales_orders (business_id,customer_id,order_number,order_date,expected_delivery,subtotal,tax_amount,discount_amount,total_amount,status,notes) VALUES (:bid,:cid,:onum,:odate,:ed,:sub,:tax,:disc,:tot,:st,:notes)");
        $s->execute([
            'bid'=>$d['business_id']??1,
            'cid'=>$d['customer_id'],
            'onum'=>$d['order_number'],
            'odate'=>$d['order_date'],
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
        $s = $this->db->prepare("UPDATE sales_orders SET customer_id=:cid,order_number=:onum,order_date=:odate,expected_delivery=:ed,subtotal=:sub,tax_amount=:tax,discount_amount=:disc,total_amount=:tot,status=:st,notes=:notes WHERE id=:id");
        return $s->execute([
            'cid'=>$d['customer_id'],
            'onum'=>$d['order_number'],
            'odate'=>$d['order_date'],
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
        $this->db->prepare("DELETE FROM sales_order_items WHERE sales_order_id=:id")->execute(['id'=>$id]);
        $s = $this->db->prepare("DELETE FROM sales_orders WHERE id=:id");
        return $s->execute(['id'=>$id]);
    }

    public function nextNumber(int $bid = $_SESSION['business_id'] ?? 1): string {
        $s = $this->db->prepare("SELECT COUNT(*) FROM sales_orders WHERE business_id=:bid");
        $s->execute(['bid'=>$bid]);
        $count = (int)$s->fetchColumn() + 1;
        return 'SO-' . str_pad((string)$count, 5, '0', STR_PAD_LEFT);
    }

    public function saveItems(int $order_id, array $items): void {
        $this->db->prepare("DELETE FROM sales_order_items WHERE sales_order_id=:oid")->execute(['oid'=>$order_id]);
        $s = $this->db->prepare("INSERT INTO sales_order_items (sales_order_id,product_id,description,quantity,unit_price,discount_pct,tax_rate,amount) VALUES (:oid,:pid,:desc,:qty,:price,:disc,:tax,:amt)");
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
