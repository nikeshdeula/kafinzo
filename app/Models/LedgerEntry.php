<?php

namespace App\Models;

use App\Core\Database;

class LedgerEntry
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByAccount(int $accountId, int $businessId = 1): array
    {
        $stmt = $this->db->prepare(
            "SELECT le.*, je.entry_date, je.description as journal_description, je.reference
             FROM ledger_entries le
             JOIN journal_entries je ON le.journal_entry_id = je.id
             WHERE le.account_id = :aid AND je.business_id = :bid
             ORDER BY je.entry_date DESC, le.id DESC"
        );
        $stmt->execute(['aid' => $accountId, 'bid' => $businessId]);
        return $stmt->fetchAll();
    }

    public function getByDateRange(int $accountId, string $from, string $to, int $businessId = 1): array
    {
        $stmt = $this->db->prepare(
            "SELECT le.*, je.entry_date, je.description as journal_description, je.reference
             FROM ledger_entries le
             JOIN journal_entries je ON le.journal_entry_id = je.id
             WHERE le.account_id = :aid AND je.business_id = :bid
               AND je.entry_date BETWEEN :from AND :to
             ORDER BY je.entry_date DESC, le.id DESC"
        );
        $stmt->execute([
            'aid'   => $accountId,
            'bid'   => $businessId,
            'from'  => $from,
            'to'    => $to,
        ]);
        return $stmt->fetchAll();
    }

    public function getByJournal(int $journalEntryId, int $businessId = 0): array
    {
        if ($businessId === 0) $businessId = $_SESSION['business_id'] ?? 0;
        $stmt = $this->db->prepare(
            "SELECT le.*, a.code, a.name as account_name
             FROM ledger_entries le
             JOIN accounts a ON le.account_id = a.id
             WHERE le.journal_entry_id = :jid AND le.business_id = :bid
             ORDER BY le.id ASC"
        );
        $stmt->execute(['jid' => $journalEntryId, 'bid' => $businessId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO ledger_entries (business_id, journal_entry_id, account_id, description, debit, credit)
             VALUES (:business_id, :journal_entry_id, :account_id, :description, :debit, :credit)"
        );
        return $stmt->execute([
            'business_id'      => $data['business_id'] ?? 1,
            'journal_entry_id' => $data['journal_entry_id'],
            'account_id'       => $data['account_id'],
            'description'      => $data['description'] ?? null,
            'debit'            => $data['debit'] ?? 0,
            'credit'           => $data['credit'] ?? 0,
        ]);
    }

    public function deleteByJournal(int $journalEntryId, int $businessId = 0): bool
    {
        if ($businessId === 0) $businessId = $_SESSION['business_id'] ?? 0;
        $stmt = $this->db->prepare("DELETE FROM ledger_entries WHERE journal_entry_id = :jid AND business_id = :bid");
        $stmt->execute(['jid' => $journalEntryId, 'bid' => $businessId]);
        return true;
    }

    public function getAccountBalances(int $businessId = 1): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.id, a.code, a.name, a.type,
                    COALESCE(SUM(le.debit), 0) as total_debit,
                    COALESCE(SUM(le.credit), 0) as total_credit
             FROM accounts a
             LEFT JOIN ledger_entries le ON a.id = le.account_id AND le.business_id = :bid
             WHERE a.business_id = :bid AND a.sub_type != 'group' AND a.is_active = 1
             GROUP BY a.id
             ORDER BY a.code ASC"
        );
        $stmt->execute(['bid' => $businessId]);
        return $stmt->fetchAll();
    }
}
