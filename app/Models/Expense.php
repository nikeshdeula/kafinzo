<?php
namespace App\Models;
use App\Core\Database;

class Expense {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid=1): array {
        $s=$this->db->prepare("SELECT e.*,c.name AS category_name FROM expenses e LEFT JOIN expense_categories c ON e.category_id=c.id WHERE e.business_id=:bid ORDER BY e.expense_date DESC");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }
    public function create(array $d): int {
        $s=$this->db->prepare("INSERT INTO expenses (business_id,category_id,expense_date,vendor,amount,tax_amount,payment_account,description,reference) VALUES (:bid,:cat,:date,:vendor,:amount,:tax,:account,:desc,:ref)");
        $s->execute(['bid'=>$d['business_id']??1,'cat'=>$d['category_id']??null,'date'=>$d['expense_date'],'vendor'=>$d['vendor']??null,'amount'=>$d['amount']??0,'tax'=>$d['tax_amount']??0,'account'=>$d['payment_account']??null,'desc'=>$d['description']??null,'ref'=>$d['reference']??null]);
        return (int)$this->db->lastInsertId();
    }
    public function categories(int $bid=1): array {
        $s=$this->db->prepare("SELECT * FROM expense_categories WHERE business_id=:bid ORDER BY name");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }
    public function totalThisMonth(int $bid=1): float {
        $s=$this->db->prepare("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE business_id=:bid AND MONTH(expense_date)=MONTH(NOW()) AND YEAR(expense_date)=YEAR(NOW())");
        $s->execute(['bid'=>$bid]); return (float)$s->fetchColumn();
    }
}
