<?php
namespace App\Models;
use App\Core\Database;

class Product {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function all(int $bid = 0): array {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 1;
        $s = $this->db->prepare("SELECT p.*,c.name AS category_name,u.abbreviation AS unit_abbr FROM products p LEFT JOIN product_categories c ON p.category_id=c.id LEFT JOIN units u ON p.unit_id=u.id WHERE p.business_id=:bid ORDER BY p.name ASC");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }
    public function find(int $id, int $bid = 0): array|false {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("SELECT * FROM products WHERE id=:id AND business_id=:bid LIMIT 1");
        $s->execute(['id'=>$id,'bid'=>$bid]); return $s->fetch();
    }
    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO products (business_id,category_id,unit_id,name,sku,type,purchase_price,selling_price,tax_rate,opening_stock,current_stock,minimum_stock,description,status) VALUES (:bid,:cat,:unit,:name,:sku,:type,:pp,:sp,:tax,:os,:cs,:ms,:desc,:status)");
        $s->execute(['bid'=>$d['business_id']??1,'cat'=>$d['category_id']??null,'unit'=>$d['unit_id']??null,'name'=>$d['name'],'sku'=>$d['sku']??null,'type'=>$d['type']??'product','pp'=>$d['purchase_price']??0,'sp'=>$d['selling_price']??0,'tax'=>$d['tax_rate']??0,'os'=>$d['opening_stock']??0,'cs'=>$d['opening_stock']??0,'ms'=>$d['minimum_stock']??0,'desc'=>$d['description']??null,'status'=>$d['status']??'active']);
        return (int)$this->db->lastInsertId();
    }
    public function categories(int $bid=1): array {
        $s=$this->db->prepare("SELECT * FROM product_categories WHERE business_id=:bid ORDER BY name");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }
    public function units(int $bid=1): array {
        $s=$this->db->prepare("SELECT * FROM units WHERE business_id=:bid ORDER BY name");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }
    public function count(int $bid=1): int {
        $s=$this->db->prepare("SELECT COUNT(*) FROM products WHERE business_id=:bid AND status='active'");
        $s->execute(['bid'=>$bid]); return (int)$s->fetchColumn();
    }
    public function lowStock(int $bid=1): array {
        $s=$this->db->prepare("SELECT * FROM products WHERE business_id=:bid AND type='product' AND current_stock <= minimum_stock AND minimum_stock > 0");
        $s->execute(['bid'=>$bid]); return $s->fetchAll();
    }

    public function update(int $id, array $d, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("UPDATE products SET category_id=:cat,unit_id=:unit,name=:name,sku=:sku,type=:type,purchase_price=:pp,selling_price=:sp,tax_rate=:tax,opening_stock=:os,current_stock=:cs,minimum_stock=:ms,description=:desc,status=:status WHERE id=:id AND business_id=:bid");
        return $s->execute(['cat'=>$d['category_id']??null,'unit'=>$d['unit_id']??null,'name'=>$d['name'],'sku'=>$d['sku']??null,'type'=>$d['type']??'product','pp'=>$d['purchase_price']??0,'sp'=>$d['selling_price']??0,'tax'=>$d['tax_rate']??0,'os'=>$d['opening_stock']??0,'cs'=>$d['current_stock']??$d['opening_stock']??0,'ms'=>$d['minimum_stock']??0,'desc'=>$d['description']??null,'status'=>$d['status']??'active','id'=>$id,'bid'=>$bid]);
    }

    public function delete(int $id, int $bid = 0): bool {
        if ($bid === 0) $bid = $_SESSION['business_id'] ?? 0;
        $s = $this->db->prepare("DELETE FROM products WHERE id=:id AND business_id=:bid");
        return $s->execute(['id'=>$id,'bid'=>$bid]);
    }
}
