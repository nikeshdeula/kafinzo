<?php

namespace App\Models;

use App\Core\Database;

class Account
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllGroupedByType(int $businessId = 1): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM accounts WHERE business_id = :bid ORDER BY code ASC"
        );
        $stmt->execute(['bid' => $businessId]);
        $all = $stmt->fetchAll();

        $grouped = [
            'asset'     => [],
            'liability' => [],
            'equity'    => [],
            'revenue'   => [],
            'expense'   => [],
        ];

        foreach ($all as $account) {
            if (isset($grouped[$account['type']])) {
                $grouped[$account['type']][] = $account;
            }
        }

        return $grouped;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO accounts (business_id, code, name, type, sub_type, description, is_active, opening_balance)
             VALUES (:business_id, :code, :name, :type, :sub_type, :description, :is_active, :opening_balance)"
        );
        return $stmt->execute([
            'business_id'     => $data['business_id'] ?? 1,
            'code'            => $data['code'],
            'name'            => $data['name'],
            'type'            => $data['type'],
            'sub_type'        => $data['sub_type'] ?? null,
            'description'     => $data['description'] ?? null,
            'is_active'       => $data['is_active'] ?? 1,
            'opening_balance' => $data['opening_balance'] ?? 0,
        ]);
    }

    public function findById(int $id, int $businessId = 0): array|false
    {
        if ($businessId === 0) $businessId = $_SESSION['business_id'] ?? 0;
        $stmt = $this->db->prepare("SELECT * FROM accounts WHERE id = :id AND business_id = :bid LIMIT 1");
        $stmt->execute(['id' => $id, 'bid' => $businessId]);
        return $stmt->fetch();
    }

    public function update(int $id, array $data, int $businessId = 0): bool
    {
        if ($businessId === 0) $businessId = $_SESSION['business_id'] ?? 0;
        $stmt = $this->db->prepare(
            "UPDATE accounts SET code=:code, name=:name, type=:type, sub_type=:sub_type, description=:description, opening_balance=:opening_balance
             WHERE id=:id AND business_id=:bid AND is_system=FALSE"
        );
        return $stmt->execute([
            'code'            => $data['code'],
            'name'            => $data['name'],
            'type'            => $data['type'],
            'sub_type'        => $data['sub_type'] ?? null,
            'description'     => $data['description'] ?? null,
            'opening_balance' => $data['opening_balance'] ?? 0,
            'id'              => $id,
            'bid'             => $businessId,
        ]);
    }

    public function codeExists(string $code, int $businessId = 1, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM accounts WHERE code = :code AND business_id = :bid AND id != :exclude LIMIT 1"
        );
        $stmt->execute(['code' => $code, 'bid' => $businessId, 'exclude' => $excludeId]);
        return (bool) $stmt->fetch();
    }

    public function getForSelect(int $businessId = 1): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, code, name, type FROM accounts WHERE business_id = :bid AND sub_type != 'group' AND is_active = 1 ORDER BY code ASC"
        );
        $stmt->execute(['bid' => $businessId]);
        return $stmt->fetchAll();
    }
}
