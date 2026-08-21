<?php

namespace App\Models;

use App\Core\Database;

class Report
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function profitLoss(int $businessId = 1): array
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(ii.quantity * ii.unit_price), 0) as total_income
            FROM invoices i
            JOIN invoice_items ii ON i.id = ii.invoice_id
            WHERE i.business_id = :bid AND i.status IN ('paid', 'approved')
        ");
        $stmt->execute(['bid' => $businessId]);
        $totalIncome = (float)$stmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0) as total_expenses
            FROM expenses
            WHERE business_id = :bid
        ");
        $stmt->execute(['bid' => $businessId]);
        $totalExpenses = (float)$stmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT a.code, a.name, a.type,
                   COALESCE(SUM(le.debit), 0) as total_debit,
                   COALESCE(SUM(le.credit), 0) as total_credit
            FROM accounts a
            LEFT JOIN ledger_entries le ON a.id = le.account_id AND le.business_id = :bid1
            WHERE a.business_id = :bid2 AND a.type = 'revenue' AND a.sub_type != 'group'
            GROUP BY a.id, a.code, a.name, a.type
            ORDER BY a.code ASC
        ");
        $stmt->execute(['bid1' => $businessId, 'bid2' => $businessId]);
        $revenueAccounts = $stmt->fetchAll();

        $stmt = $this->db->prepare("
            SELECT a.code, a.name, a.type,
                   COALESCE(SUM(le.debit), 0) as total_debit,
                   COALESCE(SUM(le.credit), 0) as total_credit
            FROM accounts a
            LEFT JOIN ledger_entries le ON a.id = le.account_id AND le.business_id = :bid1
            WHERE a.business_id = :bid2 AND a.type = 'expense' AND a.sub_type != 'group'
            GROUP BY a.id, a.code, a.name, a.type
            ORDER BY a.code ASC
        ");
        $stmt->execute(['bid1' => $businessId, 'bid2' => $businessId]);
        $expenseAccounts = $stmt->fetchAll();

        $netProfit = $totalIncome - $totalExpenses;

        return [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'revenueAccounts' => $revenueAccounts,
            'expenseAccounts' => $expenseAccounts,
        ];
    }

    public function balanceSheet(int $businessId = 1): array
    {
        $types = ['asset', 'liability', 'equity'];
        $result = [];

        foreach ($types as $type) {
            $stmt = $this->db->prepare("
                SELECT a.code, a.name, a.type, a.sub_type, a.opening_balance,
                       COALESCE(SUM(le.debit), 0) as total_debit,
                       COALESCE(SUM(le.credit), 0) as total_credit
                FROM accounts a
                LEFT JOIN ledger_entries le ON a.id = le.account_id AND le.business_id = :bid1
                WHERE a.business_id = :bid2 AND a.type = :type AND a.sub_type != 'group'
                GROUP BY a.id, a.code, a.name, a.type, a.sub_type, a.opening_balance
                ORDER BY a.code ASC
            ");
            $stmt->execute(['bid1' => $businessId, 'bid2' => $businessId, 'type' => $type]);
            $accounts = $stmt->fetchAll();

            $total = 0;
            foreach ($accounts as $i => $acc) {
                $debit = (float)$acc['total_debit'];
                $credit = (float)$acc['total_credit'];
                $opening = (float)$acc['opening_balance'];
                $balance = $type === 'asset' ? $opening + $debit - $credit : $credit - $debit - $opening;
                $accounts[$i]['balance'] = $balance;
                $total += $balance;
            }

            $result[$type] = [
                'accounts' => $accounts,
                'total' => $total,
            ];
        }

        $totalAssets = $result['asset']['total'] ?? 0;
        $totalLiabilities = $result['liability']['total'] ?? 0;
        $totalEquity = $result['equity']['total'] ?? 0;

        return [
            'assets' => $result['asset'] ?? ['accounts' => [], 'total' => 0],
            'liabilities' => $result['liability'] ?? ['accounts' => [], 'total' => 0],
            'equity' => $result['equity'] ?? ['accounts' => [], 'total' => 0],
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'totalLiabilitiesEquity' => $totalLiabilities + $totalEquity,
            'isBalanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01,
        ];
    }

    public function cashFlow(int $businessId = 1): array
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(ii.quantity * ii.unit_price), 0) as total_income
            FROM invoices i
            JOIN invoice_items ii ON i.id = ii.invoice_id
            WHERE i.business_id = :bid AND i.status IN ('paid', 'approved')
        ");
        $stmt->execute(['bid' => $businessId]);
        $cashFromCustomers = (float)$stmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0) as total_expenses
            FROM expenses
            WHERE business_id = :bid
        ");
        $stmt->execute(['bid' => $businessId]);
        $cashForOperations = (float)$stmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT bt.transaction_type, COALESCE(SUM(bt.amount), 0) as total
            FROM bank_transactions bt
            JOIN bank_accounts ba ON bt.bank_account_id = ba.id
            WHERE ba.business_id = :bid
            GROUP BY bt.transaction_type
        ");
        $stmt->execute(['bid' => $businessId]);
        $bankTotals = [];
        foreach ($stmt->fetchAll() as $row) {
            $bankTotals[$row['transaction_type']] = (float)$row['total'];
        }

        $totalDeposits = $bankTotals['deposit'] ?? 0;
        $totalWithdrawals = $bankTotals['withdrawal'] ?? 0;

        $netOperating = $cashFromCustomers - $cashForOperations;
        $financingInflow = max(0, $totalDeposits - $cashFromCustomers);
        $financingOutflow = max(0, $totalWithdrawals - $cashForOperations);
        $netFinancing = $financingInflow - $financingOutflow;

        return [
            'operatingInflow' => $cashFromCustomers,
            'operatingOutflow' => $cashForOperations,
            'netOperating' => $netOperating,
            'netInvesting' => 0,
            'financingInflow' => $financingInflow,
            'financingOutflow' => $financingOutflow,
            'netFinancing' => $netFinancing,
            'netChange' => $totalDeposits - $totalWithdrawals,
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
        ];
    }
}
