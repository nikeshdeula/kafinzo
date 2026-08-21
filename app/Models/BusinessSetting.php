<?php
namespace App\Models;
use App\Core\Database;

class BusinessSetting {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    public function get(string $key, $default = null) {
        $bid = 1;
        $s = $this->db->prepare("SELECT setting_value FROM business_settings WHERE business_id=:bid AND setting_key=:key LIMIT 1");
        $s->execute(['bid'=>$bid,'key'=>$key]); $row = $s->fetch();
        return $row ? $row['setting_value'] : $default;
    }
    public function set(string $key, $value): bool {
        $bid = 1;
        $s = $this->db->prepare("INSERT INTO business_settings (business_id,setting_key,setting_value) VALUES (:bid,:key,:val) ON DUPLICATE KEY UPDATE setting_value=:val");
        return $s->execute(['bid'=>$bid,'key'=>$key,'val'=>$value]);
    }
}
