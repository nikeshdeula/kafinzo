<?php
namespace App\Controllers\Purchases;

use App\Controllers\BaseController;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\PurchaseBill;

class PurchaseOrderController extends BaseController {
    private PurchaseOrder $model;
    private Supplier $supplierModel;
    private Product $productModel;
    private PurchaseBill $billModel;

    public function __construct() {
        parent::__construct();
        $this->model = new PurchaseOrder();
        $this->supplierModel = new Supplier();
        $this->productModel = new Product();
        $this->billModel = new PurchaseBill();
    }

    public function index() {
        $this->requireAuth();
        $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : null;
        $status = $_GET['status'] ?? null;
        $orders = $this->model->all(1, $supplier_id, $status);
        $suppliers = $this->supplierModel->all();
        $title = 'Purchase Orders';
        return view('purchases/orders', compact('orders', 'suppliers', 'supplier_id', 'status', 'title'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_number = trim($_POST['order_number'] ?? '');
            $supplier_id = (int)($_POST['supplier_id'] ?? 0);
            $order_date = trim($_POST['order_date'] ?? '');
            $expected_delivery = trim($_POST['expected_delivery'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');

            if (empty($order_number) || empty($supplier_id) || empty($order_date)) {
                $_SESSION['error'] = 'Order number, supplier, and date are required.';
                redirect('/purchases/orders/create');
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

            $order_id = $this->model->create([
                'business_id' => 1,
                'supplier_id' => $supplier_id,
                'order_number' => $order_number,
                'order_date' => $order_date,
                'expected_delivery' => $expected_delivery ?: null,
                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount,
                'status' => $status,
                'notes' => $notes
            ]);

            $this->model->saveItems($order_id, $items);
            $_SESSION['success'] = "Purchase order '{$order_number}' created successfully.";
            redirect('/purchases/orders');
        }

        $title = 'Add Purchase Order';
        $order_number = $this->model->nextNumber();
        $suppliers = $this->supplierModel->all();
        $products = $this->productModel->all();
        $taxRate = \tax_rate();
        return view('purchases/order_form', compact('title', 'order_number', 'suppliers', 'products', 'taxRate'));
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $order = $this->model->findWithItems($id);
        if (!$order) { $_SESSION['error'] = 'Order not found.'; redirect('/purchases/orders'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_number = trim($_POST['order_number'] ?? '');
            $supplier_id = (int)($_POST['supplier_id'] ?? 0);
            $order_date = trim($_POST['order_date'] ?? '');
            $expected_delivery = trim($_POST['expected_delivery'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');

            if (empty($order_number) || empty($supplier_id) || empty($order_date)) {
                $_SESSION['error'] = 'Order number, supplier, and date are required.';
                redirect('/purchases/orders/edit?id=' . $id);
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
                'order_number' => $order_number,
                'order_date' => $order_date,
                'expected_delivery' => $expected_delivery ?: null,
                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount,
                'status' => $status,
                'notes' => $notes
            ]);

            $this->model->saveItems($id, $items);
            $_SESSION['success'] = 'Purchase order updated.';
            redirect('/purchases/orders');
        }

        $title = 'Edit Purchase Order';
        $suppliers = $this->supplierModel->all();
        $products = $this->productModel->all();
        $taxRate = \tax_rate();
        return view('purchases/order_form', compact('title', 'order', 'suppliers', 'products', 'taxRate'));
    }

    public function delete() {
        $this->requireAuth();
        $id = (int)($_POST['id'] ?? 0);
        $order = $this->model->find($id);
        if (!$order) { $_SESSION['error'] = 'Order not found.'; redirect('/purchases/orders'); }
        $this->model->delete($id);
        $_SESSION['success'] = 'Purchase order deleted.';
        redirect('/purchases/orders');
    }

    public function convert() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $order = $this->model->findWithItems($id);
        if (!$order) { $_SESSION['error'] = 'Order not found.'; redirect('/purchases/orders'); }

        $bill_id = $this->billModel->create([
            'business_id' => 1,
            'supplier_id' => $order['supplier_id'],
            'bill_number' => $this->billModel->nextNumber(),
            'bill_date' => date('Y-m-d'),
            'due_date' => $order['expected_delivery'],
            'subtotal' => $order['subtotal'],
            'tax_amount' => $order['tax_amount'],
            'discount_amount' => $order['discount_amount'],
            'total_amount' => $order['total_amount'],
            'paid_amount' => 0,
            'status' => 'unpaid',
            'notes' => $order['notes']
        ]);

        $items = [];
        foreach ($order['items'] as $item) {
            $items[] = [
                'product_id' => $item['product_id'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_pct' => $item['discount_pct'],
                'tax_rate' => $item['tax_rate'],
                'amount' => $item['amount']
            ];
        }
        $this->billModel->saveItems($bill_id, $items);

        $this->model->update($id, ['status' => 'ordered']);
        $_SESSION['success'] = 'Purchase order converted to bill.';
        redirect('/purchases/bills');
    }
}
