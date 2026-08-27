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

    private function getBusinessName(): string
    {
        $db = \App\Core\Database::getInstance()->getConnection();
        $s = $db->prepare("SELECT name FROM businesses WHERE id=1 LIMIT 1");
        $s->execute();
        return $s->fetchColumn() ?: 'My Business';
    }

    private function getPeriodLabel(?string $from, ?string $to): string
    {
        if ($from && $to) {
            return $from . ' to ' . $to;
        } elseif ($from) {
            return 'From ' . $from;
        } elseif ($to) {
            return 'Until ' . $to;
        }
        return 'All Time';
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
        $rows = $this->salesStatement->getStatement($this->businessId(), $from, $to, $customer_id);
        $summary = $this->salesStatement->getSummary($this->businessId(), $from, $to, $customer_id);
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
        $rows = $this->salesStatement->getStatement($this->businessId(), $from, $to, $customer_id);

        $business = $this->getBusinessName();
        $period = $this->getPeriodLabel($from, $to);

        $headers = ['Date', 'Company name', 'Bill no.', 'Amount', 'Discount', 'Net amt', '13% Vat Amt', 'Total Amount', '1.5% TDS'];
        $data = [];
        $totalAmount = 0;
        $totalDiscount = 0;
        $totalNet = 0;
        $totalVat = 0;
        $totalGrand = 0;
        $totalTds = 0;

        foreach ($rows as $r) {
            if ($r['type'] === 'payment') continue;
            $amount = $r['subtotal'];
            $discount = $r['discount_amount'];
            $net = $amount - $discount;
            $vat = $r['tax_amount'];
            $grand = $r['total_amount'];
            $tds = $r['tds_amount'] ?? 0;

            $totalAmount += $amount;
            $totalDiscount += $discount;
            $totalNet += $net;
            $totalVat += $vat;
            $totalGrand += $grand;
            $totalTds += $tds;

            $data[] = [
                nepali_date('Y-m-d', $r['date']),
                $r['party_name'] ?? '—',
                $r['ref_number'],
                number_format($amount, 2),
                number_format($discount, 2),
                number_format($net, 2),
                number_format($vat, 2),
                number_format($grand, 2),
                number_format($tds, 2),
            ];
        }

        $totals = [
            'total sales',
            '',
            '',
            number_format($totalAmount, 2),
            number_format($totalDiscount, 2),
            number_format($totalNet, 2),
            number_format($totalVat, 2),
            number_format($totalGrand, 2),
            number_format($totalTds, 2),
        ];

        $filename = 'sales_statement_'.($from ?: 'all').'_'.($to ?: 'now').'.xls';
        \excel_export_styled($business, 'Sales Details', $period, $headers, $data, $totals, $filename);
    }

    public function purchaseStatement()
    {
        $this->requireAuth();
        $from = $_GET['from'] ?? null;
        $to = $_GET['to'] ?? null;
        $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : null;
        $rows = $this->purchaseStatement->getStatement($this->businessId(), $from, $to, $supplier_id);
        $summary = $this->purchaseStatement->getSummary($this->businessId(), $from, $to, $supplier_id);
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
        $rows = $this->purchaseStatement->getStatement($this->businessId(), $from, $to, $supplier_id);

        $business = $this->getBusinessName();
        $period = $this->getPeriodLabel($from, $to);

        $headers = ['Date', 'Company name', 'Bill no.', 'Amount', 'Discount', 'Net amt', '13% Vat Amt', 'Total Amount'];
        $data = [];
        $totalAmount = 0;
        $totalDiscount = 0;
        $totalNet = 0;
        $totalVat = 0;
        $totalGrand = 0;

        foreach ($rows as $r) {
            if ($r['type'] === 'payment') continue;
            $amount = $r['subtotal'];
            $discount = $r['discount_amount'];
            $net = $amount - $discount;
            $vat = $r['tax_amount'];
            $grand = $r['total_amount'];

            $totalAmount += $amount;
            $totalDiscount += $discount;
            $totalNet += $net;
            $totalVat += $vat;
            $totalGrand += $grand;

            $data[] = [
                nepali_date('Y-m-d', $r['date']),
                $r['party_name'] ?? '—',
                $r['ref_number'],
                number_format($amount, 2),
                number_format($discount, 2),
                number_format($net, 2),
                number_format($vat, 2),
                number_format($grand, 2),
            ];
        }

        $totals = [
            'total purchase',
            '',
            '',
            number_format($totalAmount, 2),
            number_format($totalDiscount, 2),
            number_format($totalNet, 2),
            number_format($totalVat, 2),
            number_format($totalGrand, 2),
        ];

        $filename = 'purchase_statement_'.($from ?: 'all').'_'.($to ?: 'now').'.xls';
        \excel_export_styled($business, 'Purchase Details', $period, $headers, $data, $totals, $filename);
    }
}
