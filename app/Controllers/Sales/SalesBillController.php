<?php
namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Customer;
use App\Models\SalesBill;
use App\Models\Product;

class SalesBillController extends BaseController {
    private SalesBill $model;
    private Customer $customerModel;
    private Product $productModel;

    public function __construct() {
        parent::__construct();
        $this->model = new SalesBill();
        $this->customerModel = new Customer();
        $this->productModel = new Product();
    }

    public function index() {
        $this->requireAuth();
        $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
        $status = $_GET['status'] ?? null;
        $bsYear = isset($_GET['bs_year']) ? (int)$_GET['bs_year'] : null;
        $bsMonth = isset($_GET['bs_month']) ? (int)$_GET['bs_month'] : null;
        $search = trim($_GET['search'] ?? '') ?: null;
        $bills = $this->model->all($this->businessId(), $customer_id, $status, $bsYear, $bsMonth, $search);
        $customers = $this->customerModel->all();
        $business = $this->businessInfo();
        $title = 'Sales Bills';

        $nepaliMonths = ['Baisakh','Jestha','Ashadh','Shrawan','Bhadra','Ashwin','Kartik','Mangsir','Poush','Magh','Falgun','Chaitra'];
        $currentBs = ad_to_bs(date('Y-m-d'));
        $currentYear = $currentBs['year'] ?? 2083;
        $years = range($currentYear - 2, $currentYear + 1);

        return view('sales/bills', compact('bills', 'customers', 'customer_id', 'status', 'bsYear', 'bsMonth', 'search', 'title', 'business', 'nepaliMonths', 'years', 'currentYear'));
    }

    public function export() {
        $this->requireAuth();
        $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
        $status = $_GET['status'] ?? null;
        $bsYear = isset($_GET['bs_year']) ? (int)$_GET['bs_year'] : null;
        $bsMonth = isset($_GET['bs_month']) ? (int)$_GET['bs_month'] : null;
        $search = trim($_GET['search'] ?? '') ?: null;
        $bills = $this->model->all($this->businessId(), $customer_id, $status, $bsYear, $bsMonth, $search);
        $business = $this->businessInfo();

        $nepaliMonths = ['','Baisakh','Jestha','Ashadh','Shrawan','Bhadra','Ashwin','Kartik','Mangsir','Poush','Magh','Falgun','Chaitra'];
        $period = 'All Bills';
        if ($bsYear && $bsMonth) {
            $period = ($nepaliMonths[$bsMonth] ?? '') . ' ' . $bsYear;
        }

        $headers = ['Bill #', 'Date', 'Customer', 'Address', 'Subtotal', 'Discount', 'Total', 'TDS (1.5%)', 'Grand Total', 'Paid', 'Balance', 'Status'];
        $rows = [];
        foreach ($bills as $b) {
            $tds = $b['total_amount'] * 0.015;
            $grandTotal = $b['total_amount'] - $tds;
            $balance = $grandTotal - $b['paid_amount'];
            $rows[] = [
                $b['bill_number'],
                nepali_date('d M Y', $b['bill_date']),
                $b['customer_name'] ?? '—',
                $b['customer_address'] ?? '—',
                number_format($b['subtotal'] ?? 0, 2),
                number_format($b['discount_amount'] ?? 0, 2),
                number_format($b['total_amount'], 2),
                number_format($tds, 2),
                number_format($grandTotal, 2),
                number_format($b['paid_amount'], 2),
                number_format($balance, 2),
                ucfirst($b['status']),
            ];
        }

        \excel_export_styled($business['name'] ?? 'Business', 'Sales Bills', $period, $headers, $rows, null, 'sales_bills.xls');
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bill_number = trim($_POST['bill_number'] ?? '');
            $customer_id = (int)($_POST['customer_id'] ?? 0);
            $bill_date = trim($_POST['bill_date'] ?? '');
            $due_date = trim($_POST['due_date'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');

            if (empty($bill_number) || empty($customer_id) || empty($bill_date)) {
                $_SESSION['error'] = 'Bill number, customer, and date are required.';
                redirect('/sales/bills/create');
            }

            $subtotal = 0;
            $tax_amount = 0;
            $discount_amount = 0;
            $total_amount = 0;
            $tds_amount = 0;
            $taxRate = (float)\tax_rate();
            $items = [];

            if (!empty($_POST['items'])) {
                foreach ($_POST['items'] as $item) {
                    $qty = (float)($item['quantity'] ?? 0);
                    $price = (float)($item['unit_price'] ?? 0);
                    if ($qty <= 0 || $price < 0) {
                        $_SESSION['error'] = 'Quantity must be positive and price cannot be negative.';
                        redirect('/sales/bills/create');
                    }
                    $discount = (float)($item['discount_pct'] ?? 0);
                    $amount = $qty * $price * (1 - $discount / 100);

                    $subtotal += $qty * $price;
                    $discount_amount += $qty * $price * ($discount / 100);

                    $items[] = [
                        'product_id' => !empty($item['product_id']) ? (int)$item['product_id'] : null,
                        'unit_id' => !empty($item['unit_id']) ? (int)$item['unit_id'] : null,
                        'description' => trim($item['description'] ?? ''),
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'discount_pct' => $discount,
                        'tax_rate' => $taxRate,
                        'amount' => $amount
                    ];
                }
            }

            $taxableAmount = $subtotal - $discount_amount;
            $tax_amount = $taxableAmount * ($taxRate / 100);
            $total_amount = $taxableAmount + $tax_amount;
            $tds_amount = $total_amount * 0.015;

            $bill_id = $this->model->create([
                'business_id' => $this->businessId(),
                'customer_id' => $customer_id,
                'bill_number' => $bill_number,
                'bill_date' => $bill_date,
                'due_date' => $due_date ?: null,
                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount,
                'tds_amount' => $tds_amount,
                'paid_amount' => 0,
                'status' => $status,
                'notes' => $notes
            ]);

            $this->model->saveItems($bill_id, $items);
            $_SESSION['success'] = "Sales bill '{$bill_number}' created successfully.";
            redirect('/sales/bills');
        }

        $title = 'Add Sales Bill';
        $bill_number = $this->model->nextNumber();
        $customers = $this->customerModel->all();
        $products = $this->productModel->all();
        $units = $this->productModel->units($this->businessId());
        $taxRate = \tax_rate();
        $business = $this->businessInfo();
        return view('sales/bill_form', compact('title', 'bill_number', 'customers', 'products', 'units', 'taxRate', 'business'));
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $bill = $this->model->findWithItems($id);
        if (!$bill) { $_SESSION['error'] = 'Bill not found.'; redirect('/sales/bills'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bill_number = trim($_POST['bill_number'] ?? '');
            $customer_id = (int)($_POST['customer_id'] ?? 0);
            $bill_date = trim($_POST['bill_date'] ?? '');
            $due_date = trim($_POST['due_date'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');

            if (empty($bill_number) || empty($customer_id) || empty($bill_date)) {
                $_SESSION['error'] = 'Bill number, customer, and date are required.';
                redirect('/sales/bills/edit?id=' . $id);
            }

            $subtotal = 0;
            $tax_amount = 0;
            $discount_amount = 0;
            $total_amount = 0;
            $tds_amount = 0;
            $taxRate = (float)\tax_rate();
            $items = [];

            if (!empty($_POST['items'])) {
                foreach ($_POST['items'] as $item) {
                    $qty = (float)($item['quantity'] ?? 0);
                    $price = (float)($item['unit_price'] ?? 0);
                    $discount = (float)($item['discount_pct'] ?? 0);
                    $amount = $qty * $price * (1 - $discount / 100);

                    $subtotal += $qty * $price;
                    $discount_amount += $qty * $price * ($discount / 100);

                    $items[] = [
                        'product_id' => !empty($item['product_id']) ? (int)$item['product_id'] : null,
                        'unit_id' => !empty($item['unit_id']) ? (int)$item['unit_id'] : null,
                        'description' => trim($item['description'] ?? ''),
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'discount_pct' => $discount,
                        'tax_rate' => $taxRate,
                        'amount' => $amount
                    ];
                }
            }

            $taxableAmount = $subtotal - $discount_amount;
            $tax_amount = $taxableAmount * ($taxRate / 100);
            $total_amount = $taxableAmount + $tax_amount;
            $tds_amount = $total_amount * 0.015;

            $this->model->update($id, [
                'customer_id' => $customer_id,
                'bill_number' => $bill_number,
                'bill_date' => $bill_date,
                'due_date' => $due_date ?: null,
                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount,
                'tds_amount' => $tds_amount,
                'paid_amount' => $bill['paid_amount'],
                'status' => $status,
                'notes' => $notes
            ]);

            $this->model->saveItems($id, $items);
            $_SESSION['success'] = 'Sales bill updated.';
            redirect('/sales/bills');
        }

        $title = 'Edit Sales Bill';
        $customers = $this->customerModel->all();
        $products = $this->productModel->all();
        $units = $this->productModel->units($this->businessId());
        $taxRate = \tax_rate();
        $business = $this->businessInfo();
        return view('sales/bill_form', compact('title', 'bill', 'customers', 'products', 'units', 'taxRate', 'business'));
    }

    public function delete() {
        $this->requireAuth();
        $id = (int)($_POST['id'] ?? 0);
        $bill = $this->model->find($id);
        if (!$bill) { $_SESSION['error'] = 'Bill not found.'; redirect('/sales/bills'); }
        $this->model->delete($id);
        $_SESSION['success'] = 'Sales bill deleted.';
        redirect('/sales/bills');
    }
}
