<?php

namespace App\Models;

use App\Core\Database;

class TrialBalance
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getBalance(int $businessId = 1): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.id, a.code, a.name, a.type, a.sub_type,
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

    public function getGroupedBalance(int $businessId = 1): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.type, a.id, a.code, a.name,
                    COALESCE(SUM(le.debit), 0) as total_debit,
                    COALESCE(SUM(le.credit), 0) as total_credit
             FROM accounts a
             LEFT JOIN ledger_entries le ON a.id = le.account_id AND le.business_id = :bid1
             WHERE a.business_id = :bid2 AND a.sub_type != 'group' AND a.is_active = 1
             GROUP BY a.id, a.type
             ORDER BY a.type, a.code ASC"
        );
        $stmt->execute(['bid1' => $businessId, 'bid2' => $businessId]);
        $rows = $stmt->fetchAll();

        $grouped = [
            'asset'     => [
                'label' => 'Assets', 'icon' => 'bi-bank', 'color' => '#4361ee', 'bg' => '#eef0fd',
                'accounts' => [], 'total_debit' => 0, 'total_credit' => 0
            ],
            'liability' => [
                'label' => 'Liabilities', 'icon' => 'bi-exclamation-circle', 'color' => '#ef233c', 'bg' => '#fdedf0',
                'accounts' => [], 'total_debit' => 0, 'total_credit' => 0
            ],
            'equity'    => [
                'label' => 'Equity', 'icon' => 'bi-person-fill', 'color' => '#7209b7', 'bg' => '#f3e8fd',
                'accounts' => [], 'total_debit' => 0, 'total_credit' => 0
            ],
            'revenue'   => [
                'label' => 'Revenue', 'icon' => 'bi-graph-up-arrow', 'color' => '#06d6a0', 'bg' => '#e6fdf7',
                'accounts' => [], 'total_debit' => 0, 'total_credit' => 0
            ],
            'expense'   => [
                'label' => 'Expenses', 'icon' => 'bi-wallet2', 'color' => '#fb8500', 'bg' => '#fff3e6',
                'accounts' => [], 'total_debit' => 0, 'total_credit' => 0
            ],
        ];

        foreach ($rows as $row) {
            $type = $row['type'];
            if (isset($grouped[$type])) {
                $grouped[$type]['accounts'][] = $row;
                $grouped[$type]['total_debit']  += (float)$row['total_debit'];
                $grouped[$type]['total_credit'] += (float)$row['total_credit'];
            }
        }

        return $grouped;
    }
}
