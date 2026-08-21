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
        $bills = $this->model->all(1, $customer_id, $status);
        $customers = $this->customerModel->all();
        $title = 'Sales Bills';
        return view('sales/bills', compact('bills', 'customers', 'customer_id', 'status', 'title'));
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
            $items = [];

            if (!empty($_POST['items'])) {
                foreach ($_POST['items'] as $item) {
                    $qty = (float)($item['quantity'] ?? 0);
                    $price = (float)($item['unit_price'] ?? 0);
                    $discount = (float)($item['discount_pct'] ?? 0);
                    $tax = (float)($item['tax_rate'] ?? 0) ?: (float)\tax_rate();
                    $amount = $qty * $price * (1 - $discount / 100);
                    $tax_val = $amount * ($tax / 100);
                    $line_total = $amount + $tax_val;

                    $subtotal += $qty * $price;
                    $discount_amount += $qty * $price * ($discount / 100);
                    $tax_amount += $tax_val;
                    $total_amount += $line_total;

                    $items[] = [
                        'product_id' => !empty($item['product_id']) ? (int)$item['product_id'] : null,
                        'description' => trim($item['description'] ?? ''),
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'discount_pct' => $discount,
                        'tax_rate' => $tax,
                        'amount' => $line_total
                    ];
                }
            }

            $bill_id = $this->model->create([
                'business_id' => 1,
                'customer_id' => $customer_id,
                'bill_number' => $bill_number,
                'bill_date' => $bill_date,
                'due_date' => $due_date ?: null,
                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount,
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
        $taxRate = \tax_rate();
        return view('sales/bill_form', compact('title', 'bill_number', 'customers', 'products', 'taxRate'));
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
            $items = [];

            if (!empty($_POST['items'])) {
                foreach ($_POST['items'] as $item) {
                    $qty = (float)($item['quantity'] ?? 0);
                    $price = (float)($item['unit_price'] ?? 0);
                    $discount = (float)($item['discount_pct'] ?? 0);
                    $tax = (float)($item['tax_rate'] ?? 0) ?: (float)\tax_rate();
                    $amount = $qty * $price * (1 - $discount / 100);
                    $tax_val = $amount * ($tax / 100);
                    $line_total = $amount + $tax_val;

                    $subtotal += $qty * $price;
                    $discount_amount += $qty * $price * ($discount / 100);
                    $tax_amount += $tax_val;
                    $total_amount += $line_total;

                    $items[] = [
                        'product_id' => !empty($item['product_id']) ? (int)$item['product_id'] : null,
                        'description' => trim($item['description'] ?? ''),
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'discount_pct' => $discount,
                        'tax_rate' => $tax,
                        'amount' => $line_total
                    ];
                }
            }

            $this->model->update($id, [
                'customer_id' => $customer_id,
                'bill_number' => $bill_number,
                'bill_date' => $bill_date,
                'due_date' => $due_date ?: null,
                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount,
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
        $taxRate = \tax_rate();
        return view('sales/bill_form', compact('title', 'bill', 'customers', 'products', 'taxRate'));
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
