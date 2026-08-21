<?php

namespace App\Models;

use App\Core\Database;

class JournalEntry
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function all(int $businessId = 1): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM journal_entries WHERE business_id = :bid ORDER BY entry_date DESC, id DESC"
        );
        $stmt->execute(['bid' => $businessId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM journal_entries WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO journal_entries (business_id, journal_number, entry_date, description, reference)
             VALUES (:business_id, :journal_number, :entry_date, :description, :reference)"
        );
        $stmt->execute([
            'business_id'   => $data['business_id'] ?? 1,
            'journal_number' => $data['journal_number'],
            'entry_date'     => $data['entry_date'],
            'description'    => $data['description'],
            'reference'      => $data['reference'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE journal_entries SET entry_date = :entry_date, description = :description, reference = :reference
             WHERE id = :id"
        );
        return $stmt->execute([
            'entry_date'  => $data['entry_date'],
            'description' => $data['description'],
            'reference'   => $data['reference'] ?? null,
            'id'          => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM journal_entries WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return true;
    }

    public function nextNumber(int $businessId = 1): string
    {
        $stmt = $this->db->prepare(
            "SELECT MAX(journal_number) FROM journal_entries WHERE business_id = :bid"
        );
        $stmt->execute(['bid' => $businessId]);
        $max = $stmt->fetchColumn();

        if (!$max) {
            return 'JE-001';
        }

        $num = (int)str_replace('JE-', '', $max);
        return 'JE-' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
    }
}
