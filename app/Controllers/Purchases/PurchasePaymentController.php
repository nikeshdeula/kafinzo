<?php
namespace App\Controllers\Purchases;

use App\Controllers\BaseController;
use App\Models\Supplier;
use App\Models\PurchasePayment;
use App\Models\PurchaseBill;
use App\Models\PurchaseOrder;

class PurchasePaymentController extends BaseController {
    private PurchasePayment $model;
    private Supplier $supplierModel;
    private PurchaseBill $billModel;
    private PurchaseOrder $orderModel;

    public function __construct() {
        parent::__construct();
        $this->model = new PurchasePayment();
        $this->supplierModel = new Supplier();
        $this->billModel = new PurchaseBill();
        $this->orderModel = new PurchaseOrder();
    }

    public function index() {
        $this->requireAuth();
        $payments = $this->model->all($this->businessId());
        $title = 'Supplier Payments';
        return view('purchases/payments', compact('payments', 'title'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bill_id = !empty($_POST['bill_id']) ? (int)$_POST['bill_id'] : null;
            $order_id = !empty($_POST['order_id']) ? (int)$_POST['order_id'] : null;
            $supplier_id = (int)($_POST['supplier_id'] ?? 0);
            $payment_number = trim($_POST['payment_number'] ?? '');
            $payment_date = trim($_POST['payment_date'] ?? '');
            $amount = (float)($_POST['amount'] ?? 0);
            $payment_method = trim($_POST['payment_method'] ?? 'cash');
            $reference = trim($_POST['reference'] ?? '');
            $notes = trim($_POST['notes'] ?? '');

            if (empty($payment_number) || empty($payment_date) || empty($supplier_id) || $amount <= 0) {
                $_SESSION['error'] = 'Payment number, date, supplier, and amount are required.';
                redirect('/purchases/payments/create');
            }

            $this->model->create([
                'business_id' => $this->businessId(),
                'bill_id' => $bill_id,
                'order_id' => $order_id,
                'supplier_id' => $supplier_id,
                'payment_number' => $payment_number,
                'payment_date' => $payment_date,
                'amount' => $amount,
                'payment_method' => $payment_method,
                'reference' => $reference,
                'notes' => $notes
            ]);

            if ($bill_id) {
                $bill = $this->billModel->find($bill_id);
                if ($bill) {
                    $new_paid = $bill['paid_amount'] + $amount;
                    $status = 'paid';
                    if ($new_paid < $bill['total_amount']) {
                        $status = 'partial';
                    }
                    $this->billModel->update($bill_id, [
                        'supplier_id' => $bill['supplier_id'],
                        'bill_number' => $bill['bill_number'],
                        'bill_date' => $bill['bill_date'],
                        'due_date' => $bill['due_date'],
                        'subtotal' => $bill['subtotal'],
                        'tax_amount' => $bill['tax_amount'],
                        'discount_amount' => $bill['discount_amount'],
                        'total_amount' => $bill['total_amount'],
                        'paid_amount' => $new_paid,
                        'status' => $status,
                        'notes' => $bill['notes']
                    ]);
                }
            }

            $_SESSION['success'] = "Payment '{$payment_number}' recorded successfully.";
            redirect('/purchases/payments');
        }

        $title = 'Record Supplier Payment';
        $payment_number = $this->model->nextNumber();
        $suppliers = $this->supplierModel->all();
        $bills = $this->billModel->all();
        $orders = $this->orderModel->all();
        return view('purchases/payment_form', compact('title', 'payment_number', 'suppliers', 'bills', 'orders'));
    }
}
