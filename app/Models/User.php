<?php
namespace App\Models;
use App\Core\Database;

class User {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(): array {
        $s = $this->db->query("SELECT u.*, GROUP_CONCAT(DISTINCT r.name ORDER BY r.name SEPARATOR ', ') AS role_names FROM users u LEFT JOIN user_roles ur ON ur.user_id=u.id LEFT JOIN roles r ON r.id=ur.role_id GROUP BY u.id ORDER BY u.full_name ASC");
        return $s->fetchAll();
    }
    public function find(int $id): array|false {
        $s = $this->db->prepare("SELECT u.*, GROUP_CONCAT(DISTINCT r.name ORDER BY r.name SEPARATOR ', ') AS role_names, GROUP_CONCAT(DISTINCT r.id ORDER BY r.id SEPARATOR ',') AS role_ids FROM users u LEFT JOIN user_roles ur ON ur.user_id=u.id LEFT JOIN roles r ON r.id=ur.role_id WHERE u.id=:id GROUP BY u.id LIMIT 1");
        $s->execute(['id'=>$id]); return $s->fetch();
    }
    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO users (full_name,email,mobile_number,password_hash,status) VALUES (:name,:email,:mobile,:pass,:status)");
        $pass = password_hash($d['password'] ?? '', PASSWORD_BCRYPT);
        $s->execute(['name'=>$d['full_name'],'email'=>$d['email'],'mobile'=>$d['mobile_number']??null,'pass'=>$pass,'status'=>$d['status']??'active']);
        $uid = (int)$this->db->lastInsertId();
        if (!empty($d['role_id'])) {
            $s2 = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:uid, :rid)");
            $s2->execute(['uid'=>$uid,'rid'=>$d['role_id']]);
        }
        return $uid;
    }
    public function update(int $id, array $d): bool {
        $fields = ['full_name=:name','email=:email','mobile_number=:mobile','status=:status'];
        $params = ['name'=>$d['full_name'],'email'=>$d['email'],'mobile'=>$d['mobile_number']??null,'status'=>$d['status']??'active','id'=>$id];
        if (!empty($d['password'])) {
            $fields[] = 'password_hash=:pass';
            $params['pass'] = password_hash($d['password'], PASSWORD_BCRYPT);
        }
        $s = $this->db->prepare("UPDATE users SET " . implode(',', $fields) . " WHERE id=:id");
        $ok = $s->execute($params);
        if ($ok && isset($d['role_id'])) {
            $s2 = $this->db->prepare("DELETE FROM user_roles WHERE user_id=:uid");
            $s2->execute(['uid'=>$id]);
            if (!empty($d['role_id'])) {
                $s3 = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:uid, :rid)");
                $s3->execute(['uid'=>$id,'rid'=>$d['role_id']]);
            }
        }
        return $ok;
    }
    public function delete(int $id): bool {
        $s = $this->db->prepare("DELETE FROM users WHERE id=:id");
        return $s->execute(['id'=>$id]);
    }
    public function roles(): array {
        $s = $this->db->query("SELECT * FROM roles ORDER BY name ASC");
        return $s->fetchAll();
    }
}
