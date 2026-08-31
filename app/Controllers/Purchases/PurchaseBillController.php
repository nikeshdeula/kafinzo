<?php
namespace App\Controllers\Purchases;

use App\Controllers\BaseController;
use App\Models\Supplier;
use App\Models\PurchaseBill;
use App\Models\Product;

class PurchaseBillController extends BaseController {
    private PurchaseBill $model;
    private Supplier $supplierModel;
    private Product $productModel;

    public function __construct() {
        parent::__construct();
        $this->model = new PurchaseBill();
        $this->supplierModel = new Supplier();
        $this->productModel = new Product();
    }

    public function index() {
        $this->requireAuth();
        $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : null;
        $status = $_GET['status'] ?? null;
        $bills = $this->model->all($this->businessId(), $supplier_id, $status);
        $suppliers = $this->supplierModel->all();
        $business = $this->businessInfo();
        $title = 'Purchase Bills';
        return view('purchases/bills', compact('bills', 'suppliers', 'supplier_id', 'status', 'title', 'business'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bill_number = trim($_POST['bill_number'] ?? '');
            if ($bill_number === '') {
                $bill_number = $this->model->nextNumber();
            }
            $supplier_id = (int)($_POST['supplier_id'] ?? 0);
            $bill_date = trim($_POST['bill_date'] ?? '');
            $due_date = trim($_POST['due_date'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');

            if (empty($bill_number) || empty($supplier_id) || empty($bill_date)) {
                $_SESSION['error'] = 'Bill number, supplier, and date are required.';
                redirect('/purchases/bills/create');
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
                'business_id' => $this->businessId(),
                'supplier_id' => $supplier_id,
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
            $_SESSION['success'] = "Purchase bill '{$bill_number}' created successfully.";
            redirect('/purchases/bills');
        }

        $title = 'Add Purchase Bill';
        $bill_number = $this->model->nextNumber();
        $suppliers = $this->supplierModel->all();
        $products = $this->productModel->all();
        $taxRate = \tax_rate();
        $business = $this->businessInfo();
        return view('purchases/bill_form', compact('title', 'bill_number', 'suppliers', 'products', 'taxRate', 'business'));
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $bill = $this->model->findWithItems($id);
        if (!$bill) { $_SESSION['error'] = 'Bill not found.'; redirect('/purchases/bills'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bill_number = trim($_POST['bill_number'] ?? '');
            $supplier_id = (int)($_POST['supplier_id'] ?? 0);
            $bill_date = trim($_POST['bill_date'] ?? '');
            $due_date = trim($_POST['due_date'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');

            if (empty($bill_number) || empty($supplier_id) || empty($bill_date)) {
                $_SESSION['error'] = 'Bill number, supplier, and date are required.';
                redirect('/purchases/bills/edit?id=' . $id);
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
                'supplier_id' => $supplier_id,
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
            $_SESSION['success'] = 'Purchase bill updated.';
            redirect('/purchases/bills');
        }

        $title = 'Edit Purchase Bill';
        $suppliers = $this->supplierModel->all();
        $products = $this->productModel->all();
        $taxRate = \tax_rate();
        $business = $this->businessInfo();
        return view('purchases/bill_form', compact('title', 'bill', 'suppliers', 'products', 'taxRate', 'business'));
    }

    public function delete() {
        $this->requireAuth();
        $id = (int)($_POST['id'] ?? 0);
        $bill = $this->model->find($id);
        if (!$bill) { $_SESSION['error'] = 'Bill not found.'; redirect('/purchases/bills'); }
        $this->model->delete($id);
        $_SESSION['success'] = 'Purchase bill deleted.';
        redirect('/purchases/bills');
    }
}
