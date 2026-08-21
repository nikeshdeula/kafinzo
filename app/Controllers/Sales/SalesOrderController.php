<?php
namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Product;

class SalesOrderController extends BaseController {
    private SalesOrder $model;
    private Customer $customerModel;
    private Product $productModel;

    public function __construct() {
        parent::__construct();
        $this->model = new SalesOrder();
        $this->customerModel = new Customer();
        $this->productModel = new Product();
    }

    public function index() {
        $this->requireAuth();
        $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
        $status = $_GET['status'] ?? null;
        $orders = $this->model->all(1, $customer_id, $status);
        $customers = $this->customerModel->all();
        $title = 'Sales Orders';
        return view('sales/orders', compact('orders', 'customers', 'customer_id', 'status', 'title'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_number = trim($_POST['order_number'] ?? '');
            $customer_id = (int)($_POST['customer_id'] ?? 0);
            $order_date = trim($_POST['order_date'] ?? '');
            $expected_delivery = trim($_POST['expected_delivery'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');

            if (empty($order_number) || empty($customer_id) || empty($order_date)) {
                $_SESSION['error'] = 'Order number, customer, and date are required.';
                redirect('/sales/orders/create');
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
                'customer_id' => $customer_id,
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
            $_SESSION['success'] = "Sales order '{$order_number}' created successfully.";
            redirect('/sales/orders');
        }

        $title = 'Add Sales Order';
        $order_number = $this->model->nextNumber();
        $customers = $this->customerModel->all();
        $products = $this->productModel->all();
        $taxRate = \tax_rate();
        return view('sales/order_form', compact('title', 'order_number', 'customers', 'products', 'taxRate'));
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $order = $this->model->findWithItems($id);
        if (!$order) { $_SESSION['error'] = 'Order not found.'; redirect('/sales/orders'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_number = trim($_POST['order_number'] ?? '');
            $customer_id = (int)($_POST['customer_id'] ?? 0);
            $order_date = trim($_POST['order_date'] ?? '');
            $expected_delivery = trim($_POST['expected_delivery'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');

            if (empty($order_number) || empty($customer_id) || empty($order_date)) {
                $_SESSION['error'] = 'Order number, customer, and date are required.';
                redirect('/sales/orders/edit?id=' . $id);
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
            $_SESSION['success'] = 'Sales order updated.';
            redirect('/sales/orders');
        }

        $title = 'Edit Sales Order';
        $customers = $this->customerModel->all();
        $products = $this->productModel->all();
        $taxRate = \tax_rate();
        return view('sales/order_form', compact('title', 'order', 'customers', 'products', 'taxRate'));
    }

    public function delete() {
        $this->requireAuth();
        $id = (int)($_POST['id'] ?? 0);
        $order = $this->model->find($id);
        if (!$order) { $_SESSION['error'] = 'Order not found.'; redirect('/sales/orders'); }
        $this->model->delete($id);
        $_SESSION['success'] = 'Sales order deleted.';
        redirect('/sales/orders');
    }
}
