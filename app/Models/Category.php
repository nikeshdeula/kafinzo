<?php
namespace App\Models;
use App\Core\Database;

class Category {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 0): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT * FROM product_categories WHERE business_id=:bid ORDER BY name ASC");
        $s->execute(['bid' => $bid]);
        return $s->fetchAll();
    }

    public function find(int $id): array|false {
        $s = $this->db->prepare("SELECT * FROM product_categories WHERE id=:id LIMIT 1");
        $s->execute(['id' => $id]);
        return $s->fetch();
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO product_categories (business_id, name, description, created_at) VALUES (:bid, :name, :desc, NOW())");
        $s->execute([
            'bid'  => $d['business_id'] ?? 1,
            'name' => $d['name'],
            'desc' => $d['description'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(array $d): bool {
        $s = $this->db->prepare("UPDATE product_categories SET name=:name, description=:desc WHERE id=:id");
        return $s->execute([
            'name' => $d['name'],
            'desc' => $d['description'] ?? null,
            'id'   => $d['id'],
        ]);
    }

    public function delete(int $id): bool {
        $s = $this->db->prepare("DELETE FROM product_categories WHERE id=:id");
        return $s->execute(['id' => $id]);
    }

    public function count(int $bid = 0): int {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT COUNT(*) FROM product_categories WHERE business_id=:bid");
        $s->execute(['bid' => $bid]);
        return (int)$s->fetchColumn();
    }
}
