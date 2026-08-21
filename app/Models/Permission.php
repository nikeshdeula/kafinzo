<?php
namespace App\Models;
use App\Core\Database;

class Permission {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(): array {
        $s = $this->db->query("SELECT * FROM permissions ORDER BY name ASC");
        return $s->fetchAll();
    }
    public function find(int $id): array|false {
        $s = $this->db->prepare("SELECT * FROM permissions WHERE id=:id LIMIT 1");
        $s->execute(['id'=>$id]); return $s->fetch();
    }
    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO permissions (name,description) VALUES (:name,:desc)");
        $s->execute(['name'=>$d['name'],'desc'=>$d['description']??null]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $d): bool {
        $s = $this->db->prepare("UPDATE permissions SET name=:name,description=:desc WHERE id=:id");
        return $s->execute(['name'=>$d['name'],'desc'=>$d['description']??null,'id'=>$id]);
    }
    public function delete(int $id): bool {
        $s = $this->db->prepare("DELETE FROM permissions WHERE id=:id");
        return $s->execute(['id'=>$id]);
    }
}
