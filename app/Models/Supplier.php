<?php
namespace App\Models;
use App\Core\Database;

class Supplier {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 1): array {
        $s = $this->db->prepare("SELECT * FROM suppliers WHERE business_id=:bid ORDER BY name ASC");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }
    public function find(int $id): array|false {
        $s = $this->db->prepare("SELECT * FROM suppliers WHERE id=:id LIMIT 1");
        $s->execute(['id'=>$id]); return $s->fetch();
    }
    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO suppliers (business_id,name,company_name,pan,vat_number,phone,email,address,opening_balance,payment_terms,status) VALUES (:bid,:name,:company,:pan,:vat,:phone,:email,:address,:opening,:terms,:status)");
        $s->execute(['bid'=>$d['business_id']??1,'name'=>$d['name'],'company'=>$d['company_name']??null,'pan'=>$d['pan']??null,'vat'=>$d['vat_number']??null,'phone'=>$d['phone']??null,'email'=>$d['email']??null,'address'=>$d['address']??null,'opening'=>$d['opening_balance']??0,'terms'=>$d['payment_terms']??0,'status'=>$d['status']??'active']);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $d): bool {
        $s = $this->db->prepare("UPDATE suppliers SET name=:name,company_name=:company,pan=:pan,vat_number=:vat,phone=:phone,email=:email,address=:address,opening_balance=:opening,payment_terms=:terms,status=:status WHERE id=:id");
        return $s->execute(['name'=>$d['name'],'company'=>$d['company_name']??null,'pan'=>$d['pan']??null,'vat'=>$d['vat_number']??null,'phone'=>$d['phone']??null,'email'=>$d['email']??null,'address'=>$d['address']??null,'opening'=>$d['opening_balance']??0,'terms'=>$d['payment_terms']??0,'status'=>$d['status']??'active','id'=>$id]);
    }
    public function count(int $bid=1): int {
        $s=$this->db->prepare("SELECT COUNT(*) FROM suppliers WHERE business_id=:bid AND status='active'");
        $s->execute(['bid'=>$bid]); return (int)$s->fetchColumn();
    }
}
