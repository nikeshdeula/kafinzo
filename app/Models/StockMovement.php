<?php
namespace App\Models;
use App\Core\Database;

class StockMovement {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 0, array $filters = []): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $sql = "SELECT sm.*, p.name AS product_name, w.name AS warehouse_name
                FROM stock_movements sm
                LEFT JOIN products p ON sm.product_id = p.id
                LEFT JOIN warehouses w ON sm.warehouse_id = w.id
                WHERE sm.business_id = :bid";
        $params = ['bid' => $bid];

        if (!empty($filters['product_id'])) {
            $sql .= " AND sm.product_id = :pid";
            $params['pid'] = (int)$filters['product_id'];
        }
        if (!empty($filters['reference_type'])) {
            $sql .= " AND sm.reference_type = :rtype";
            $params['rtype'] = $filters['reference_type'];
        }
        if (!empty($filters['from_date'])) {
            $sql .= " AND sm.created_at >= :from";
            $params['from'] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $sql .= " AND sm.created_at <= :to";
            $params['to'] = $filters['to_date'];
        }

        $sql .= " ORDER BY sm.created_at DESC LIMIT 500";
        $s = $this->db->prepare($sql);
        $s->execute($params);
        return $s->fetchAll();
    }

    public function find(int $id, int $bid = 0): array|false {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("SELECT * FROM stock_movements WHERE id=:id AND business_id=:bid LIMIT 1");
        $s->execute(['id'=>$id,'bid'=>$bid]); return $s->fetch();
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO stock_movements (business_id, product_id, warehouse_id, reference_type, reference_id, quantity_change, notes, created_at, created_by) VALUES (:bid, :pid, :wid, :rtype, :rid, :qty, :notes, NOW(), :uid)");
        $s->execute([
            'bid'    => $d['business_id'] ?? 1,
            'pid'    => $d['product_id'],
            'wid'    => $d['warehouse_id'] ?? null,
            'rtype'  => $d['reference_type'],
            'rid'    => $d['reference_id'] ?? null,
            'qty'    => $d['quantity_change'],
            'notes'  => $d['notes'] ?? null,
            'uid'    => $_SESSION['user_id'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }
}
