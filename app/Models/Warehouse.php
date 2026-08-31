<?php
namespace App\Models;
use App\Core\Database;

class Warehouse {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 0): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT * FROM warehouses WHERE business_id=:bid ORDER BY name ASC");
        $s->execute(['bid' => $bid]);
        return $s->fetchAll();
    }

    public function find(int $id, int $bid = 0): array|false {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("SELECT * FROM warehouses WHERE id=:id AND business_id=:bid LIMIT 1");
        $s->execute(['id'=>$id,'bid'=>$bid]); return $s->fetch();
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO warehouses (business_id, name, location, is_default, created_at) VALUES (:bid, :name, :loc, :def, NOW())");
        $s->execute([
            'bid'  => $d['business_id'] ?? 1,
            'name' => $d['name'],
            'loc'  => $d['location'] ?? null,
            'def'  => !empty($d['is_default']) ? 1 : 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(array $d, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("UPDATE warehouses SET name=:name, location=:loc, is_default=:def WHERE id=:id AND business_id=:bid");
        return $s->execute(['name'=>$d['name'],'loc'=>$d['location']??null,'def'=>!empty($d['is_default']) ? 1 : 0,'id'=>$d['id'],'bid'=>$bid]);
    }

    public function delete(int $id, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("DELETE FROM warehouses WHERE id=:id AND business_id=:bid");
        return $s->execute(['id'=>$id,'bid'=>$bid]);
    }

    public function defaultWarehouse(int $bid = 0): array|false {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT * FROM warehouses WHERE business_id=:bid AND is_default=1 LIMIT 1");
        $s->execute(['bid' => $bid]);
        return $s->fetch();
    }
}
