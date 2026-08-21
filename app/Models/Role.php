<?php
namespace App\Models;
use App\Core\Database;

class Role {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(): array {
        $s = $this->db->query("SELECT * FROM roles ORDER BY name ASC");
        return $s->fetchAll();
    }
    public function find(int $id): array|false {
        $s = $this->db->prepare("SELECT * FROM roles WHERE id=:id LIMIT 1");
        $s->execute(['id'=>$id]); return $s->fetch();
    }
    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO roles (name,description) VALUES (:name,:desc)");
        $s->execute(['name'=>$d['name'],'desc'=>$d['description']??null]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $d): bool {
        $s = $this->db->prepare("UPDATE roles SET name=:name,description=:desc WHERE id=:id");
        return $s->execute(['name'=>$d['name'],'desc'=>$d['description']??null,'id'=>$id]);
    }
    public function delete(int $id): bool {
        $s = $this->db->prepare("DELETE FROM roles WHERE id=:id");
        return $s->execute(['id'=>$id]);
    }
    public function permissions(int $rid): array {
        $s = $this->db->prepare("SELECT p.* FROM permissions p JOIN role_permissions rp ON p.id=rp.permission_id WHERE rp.role_id=:rid ORDER BY p.name ASC");
        $s->execute(['rid'=>$rid]); return $s->fetchAll();
    }
    public function setPermissions(int $rid, array $pids): void {
        $s = $this->db->prepare("DELETE FROM role_permissions WHERE role_id=:rid");
        $s->execute(['rid'=>$rid]);
        $ins = $this->db->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:rid, :pid)");
        foreach ($pids as $pid) {
            $ins->execute(['rid'=>$rid,'pid'=>$pid]);
        }
    }
}
