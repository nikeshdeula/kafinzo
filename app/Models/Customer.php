<?php
namespace App\Models;
use App\Core\Database;

class Customer {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 0): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT * FROM customers WHERE business_id=:bid ORDER BY name ASC");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }
    public function find(int $id, int $bid = 0): array|false {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("SELECT * FROM customers WHERE id=:id AND business_id=:bid LIMIT 1");
        $s->execute(['id'=>$id,'bid'=>$bid]); return $s->fetch();
    }
    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO customers (business_id,name,company_name,branch,pan,vat_number,phone,email,address,credit_limit,opening_balance,payment_terms,status) VALUES (:bid,:name,:company,:branch,:pan,:vat,:phone,:email,:address,:credit,:opening,:terms,:status)");
        $s->execute(['bid'=>$d['business_id']??($_SESSION['business_id']??0),'name'=>$d['name'],'company'=>$d['company_name']??null,'branch'=>$d['branch']??null,'pan'=>$d['pan']??null,'vat'=>$d['vat_number']??null,'phone'=>$d['phone']??null,'email'=>$d['email']??null,'address'=>$d['address']??null,'credit'=>$d['credit_limit']??0,'opening'=>$d['opening_balance']??0,'terms'=>$d['payment_terms']??0,'status'=>$d['status']??'active']);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $d, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("UPDATE customers SET name=:name,company_name=:company,branch=:branch,pan=:pan,vat_number=:vat,phone=:phone,email=:email,address=:address,credit_limit=:credit,payment_terms=:terms,status=:status WHERE id=:id AND business_id=:bid");
        return $s->execute(['name'=>$d['name'],'company'=>$d['company_name']??null,'branch'=>$d['branch']??null,'pan'=>$d['pan']??null,'vat'=>$d['vat_number']??null,'phone'=>$d['phone']??null,'email'=>$d['email']??null,'address'=>$d['address']??null,'credit'=>$d['credit_limit']??0,'terms'=>$d['payment_terms']??0,'status'=>$d['status']??'active','id'=>$id,'bid'=>$bid]);
    }
    public function count(int $bid=1): int {
        $s=$this->db->prepare("SELECT COUNT(*) FROM customers WHERE business_id=:bid AND status='active'");
        $s->execute(['bid'=>$bid]); return (int)$s->fetchColumn();
    }
}
