<?php
namespace App\Models;
use App\Core\Database;

class BankAccount {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid=1): array {
        $s=$this->db->prepare("SELECT * FROM bank_accounts WHERE business_id=:bid ORDER BY is_default DESC, account_name ASC");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }
    public function find(int $id): array|false {
        $s=$this->db->prepare("SELECT * FROM bank_accounts WHERE id=:id LIMIT 1");
        $s->execute(['id'=>$id]); return $s->fetch();
    }
    public function create(array $d): int {
        $s=$this->db->prepare("INSERT INTO bank_accounts (business_id,account_type,bank_name,account_name,account_number,branch,opening_balance,current_balance) VALUES (:bid,:type,:bank,:name,:num,:branch,:opening,:current)");
        $s->execute(['bid'=>$d['business_id']??1,'type'=>$d['account_type']??'bank','bank'=>$d['bank_name']??null,'name'=>$d['account_name'],'num'=>$d['account_number']??null,'branch'=>$d['branch']??null,'opening'=>$d['opening_balance']??0,'current'=>$d['opening_balance']??0]);
        return (int)$this->db->lastInsertId();
    }
    public function transactions(int $accountId): array {
        $s=$this->db->prepare("SELECT * FROM bank_transactions WHERE bank_account_id=:id ORDER BY transaction_date DESC LIMIT 50");
        $s->execute(['id'=>$accountId]); return $s->fetchAll();
    }
    public function totalBalance(int $bid=1): float {
        $s=$this->db->prepare("SELECT COALESCE(SUM(current_balance),0) FROM bank_accounts WHERE business_id=:bid AND status='active'");
        $s->execute(['bid'=>$bid]); return (float)$s->fetchColumn();
    }
}
