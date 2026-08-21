<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\Report;
use App\Models\SalesStatement;
use App\Models\PurchaseStatement;

class ReportController extends BaseController
{
    private Report $report;
    private SalesStatement $salesStatement;
    private PurchaseStatement $purchaseStatement;

    public function __construct()
    {
        parent::__construct();
        $this->report = new Report();
        $this->salesStatement = new SalesStatement();
        $this->purchaseStatement = new PurchaseStatement();
    }

    public function profitLoss()
    {
        $this->requireAuth();
        $data = $this->report->profitLoss();
        $pageTitle = 'Profit & Loss';
        $pageDesc = 'View your income statement.';
        $module = 'reports';
        $activeTab = 'profit-loss';
        return view('reports/profit_loss', compact('data', 'pageTitle', 'pageDesc', 'module', 'activeTab'));
    }

    public function balanceSheet()
    {
        $this->requireAuth();
        $data = $this->report->balanceSheet();
        $pageTitle = 'Balance Sheet';
        $pageDesc = 'View your financial position.';
        $module = 'reports';
        $activeTab = 'balance-sheet';
        return view('reports/balance_sheet', compact('data', 'pageTitle', 'pageDesc', 'module', 'activeTab'));
    }

    public function cashFlow()
    {
        $this->requireAuth();
        $data = $this->report->cashFlow();
        $pageTitle = 'Cash Flow';
        $pageDesc = 'View your cash movement.';
        $module = 'reports';
        $activeTab = 'cash-flow';
        return view('reports/cash_flow', compact('data', 'pageTitle', 'pageDesc', 'module', 'activeTab'));
    }

    public function salesStatement()
    {
        $this->requireAuth();
        $from = $_GET['from'] ?? null;
        $to = $_GET['to'] ?? null;
        $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
        $rows = $this->salesStatement->getStatement(1, $from, $to, $customer_id);
        $summary = $this->salesStatement->getSummary(1, $from, $to, $customer_id);
        $customers = $this->salesStatement->customers();
        $pageTitle = 'Sales Statement';
        $pageDesc = 'View sales transactions and payment history.';
        return view('reports/sales_statement', compact('rows', 'summary', 'customers', 'from', 'to', 'customer_id', 'pageTitle', 'pageDesc'));
    }

    public function salesStatementExport()
    {
        $this->requireAuth();
        $from = $_GET['from'] ?? null;
        $to = $_GET['to'] ?? null;
        $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
        $rows = $this->salesStatement->getStatement(1, $from, $to, $customer_id);

        $headers = ['Date', 'Type', 'Ref #', 'Customer', 'Subtotal', 'Tax', 'Discount', 'Total', 'Paid', 'Status'];
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                date('Y-m-d', strtotime($r['date'])),
                ucfirst($r['type']),
                $r['ref_number'],
                $r['party_name'] ?? '—',
                number_format($r['subtotal'], 2),
                number_format($r['tax_amount'], 2),
                number_format($r['discount_amount'], 2),
                number_format($r['total_amount'], 2),
                number_format($r['paid_amount'], 2),
                ucfirst($r['status']),
            ];
        }

        $filename = 'sales_statement_'.($from ?: 'all').'_'.($to ?: 'now').'.xls';
        \excel_export('Sales Statement', $headers, $data, $filename);
    }

    public function purchaseStatement()
    {
        $this->requireAuth();
        $from = $_GET['from'] ?? null;
        $to = $_GET['to'] ?? null;
        $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : null;
        $rows = $this->purchaseStatement->getStatement(1, $from, $to, $supplier_id);
        $summary = $this->purchaseStatement->getSummary(1, $from, $to, $supplier_id);
        $suppliers = $this->purchaseStatement->suppliers();
        $pageTitle = 'Purchase Statement';
        $pageDesc = 'View purchase transactions and payment history.';
        return view('reports/purchase_statement', compact('rows', 'summary', 'suppliers', 'from', 'to', 'supplier_id', 'pageTitle', 'pageDesc'));
    }

    public function purchaseStatementExport()
    {
        $this->requireAuth();
        $from = $_GET['from'] ?? null;
        $to = $_GET['to'] ?? null;
        $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : null;
        $rows = $this->purchaseStatement->getStatement(1, $from, $to, $supplier_id);

        $headers = ['Date', 'Type', 'Ref #', 'Supplier', 'Subtotal', 'Tax', 'Discount', 'Total', 'Paid', 'Status'];
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                date('Y-m-d', strtotime($r['date'])),
                ucfirst($r['type']),
                $r['ref_number'],
                $r['party_name'] ?? '—',
                number_format($r['subtotal'], 2),
                number_format($r['tax_amount'], 2),
                number_format($r['discount_amount'], 2),
                number_format($r['total_amount'], 2),
                number_format($r['paid_amount'], 2),
                ucfirst($r['status']),
            ];
        }

        $filename = 'purchase_statement_'.($from ?: 'all').'_'.($to ?: 'now').'.xls';
        \excel_export('Purchase Statement', $headers, $data, $filename);
    }
}
