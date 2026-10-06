<?php

namespace App\Controllers;

use App\Core\Database;

class DashboardController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function index()
    {
        $bid = $this->businessId();
        $db = Database::getInstance()->getConnection();

        $s = $db->prepare("SELECT COALESCE(SUM(total_amount),0) FROM sales_bills WHERE business_id=:bid AND status!='cancelled'");
        $s->execute(['bid' => $bid]);
        $totalSales = (float)$s->fetchColumn();

        $s = $db->prepare("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE business_id=:bid");
        $s->execute(['bid' => $bid]);
        $totalExpenses = (float)$s->fetchColumn();

        $netProfit = $totalSales - $totalExpenses;

        $s = $db->prepare("SELECT COALESCE(SUM(current_balance),0) FROM bank_accounts WHERE business_id=:bid AND status='active'");
        $s->execute(['bid' => $bid]);
        $bankBalance = (float)$s->fetchColumn();

        $fromDate = date('Y-m-01', strtotime('-5 months'));

        $s = $db->prepare("SELECT DATE_FORMAT(bill_date,'%Y-%m') ym, COALESCE(SUM(total_amount),0) t FROM sales_bills WHERE business_id=:bid AND status!='cancelled' AND bill_date >= :from GROUP BY ym");
        $s->execute(['bid' => $bid, 'from' => $fromDate]);
        $salesByMonth = [];
        foreach ($s->fetchAll() as $r) {
            $salesByMonth[$r['ym']] = (float)$r['t'];
        }

        $s = $db->prepare("SELECT DATE_FORMAT(expense_date,'%Y-%m') ym, COALESCE(SUM(amount),0) t FROM expenses WHERE business_id=:bid AND expense_date >= :from GROUP BY ym");
        $s->execute(['bid' => $bid, 'from' => $fromDate]);
        $expensesByMonth = [];
        foreach ($s->fetchAll() as $r) {
            $expensesByMonth[$r['ym']] = (float)$r['t'];
        }

        $chartLabels = [];
        $chartSales = [];
        $chartExpenses = [];
        for ($i = 5; $i >= 0; $i--) {
            $ym = date('Y-m', strtotime(date('Y-m-01') . " -{$i} months"));
            $chartLabels[] = date('M Y', strtotime($ym . '-01'));
            $chartSales[] = $salesByMonth[$ym] ?? 0;
            $chartExpenses[] = $expensesByMonth[$ym] ?? 0;
        }

        $s = $db->prepare("
            (SELECT 'sale' AS kind, b.bill_number AS ref, COALESCE(c.name,'—') AS party, b.total_amount AS amount, b.bill_date AS txn_date, b.status AS status
             FROM sales_bills b LEFT JOIN customers c ON b.customer_id=c.id
             WHERE b.business_id=:bid1)
            UNION ALL
            (SELECT 'expense' AS kind, COALESCE(NULLIF(e.reference,''),'—') AS ref, COALESCE(e.vendor, c.name, '—') AS party, e.amount AS amount, e.expense_date AS txn_date, 'paid' AS status
             FROM expenses e LEFT JOIN expense_categories c ON e.category_id=c.id
             WHERE e.business_id=:bid2)
            ORDER BY txn_date DESC
            LIMIT 8
        ");
        $s->execute(['bid1' => $bid, 'bid2' => $bid]);
        $recent = $s->fetchAll();

        return view('dashboard/index', [
            'title' => 'Dashboard',
            'userName' => $_SESSION['user_name'] ?? 'User',
            'totalSales' => $totalSales,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'bankBalance' => $bankBalance,
            'chartLabels' => $chartLabels,
            'chartSales' => $chartSales,
            'chartExpenses' => $chartExpenses,
            'recent' => $recent,
        ]);
    }
}
