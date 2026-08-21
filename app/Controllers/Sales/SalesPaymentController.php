<?php
namespace App\Controllers\Sales;
use App\Controllers\BaseController;
use App\Models\SalesPayment;
use App\Models\Invoice;
use App\Models\Customer;

class SalesPaymentController extends BaseController {
    private SalesPayment $paymentModel;
    private Invoice $invoiceModel;
    private Customer $customerModel;

    public function __construct() {
        parent::__construct();
        $this->paymentModel = new SalesPayment();
        $this->invoiceModel = new Invoice();
        $this->customerModel = new Customer();
    }

    public function index() {
        $this->requireAuth();
        $payments = $this->paymentModel->all();
        $title = 'Payments Received';
        return view('sales/payments', compact('payments', 'title'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoiceId = (int)($_POST['invoice_id'] ?? 0);
            $customerId = (int)($_POST['customer_id'] ?? 0);
            $paymentDate = $_POST['payment_date'] ?? date('Y-m-d');
            $paymentMethod = trim($_POST['payment_method'] ?? 'cash');
            $referenceNumber = trim($_POST['reference_number'] ?? '');
            $amount = (float)($_POST['amount'] ?? 0);
            $notes = trim($_POST['notes'] ?? '');

            if (empty($invoiceId) || empty($customerId) || $amount <= 0) {
                $_SESSION['error'] = 'Invoice, customer, and valid amount are required.';
                redirect('/sales/payments/create');
            }

            $invoice = $this->invoiceModel->find($invoiceId);
            if (!$invoice) {
                $_SESSION['error'] = 'Invoice not found.';
                redirect('/sales/payments/create');
            }

            $this->paymentModel->create([
                'business_id' => 1,
                'invoice_id' => $invoiceId,
                'customer_id' => $customerId,
                'payment_date' => $paymentDate,
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'amount' => $amount,
                'notes' => $notes,
            ]);

            $newPaid = $invoice['paid_amount'] + $amount;
            if ($newPaid >= $invoice['total_amount']) {
                $status = 'paid';
            } elseif ($newPaid > 0) {
                $status = 'partial';
            } else {
                $status = $invoice['status'];
            }

            $this->invoiceModel->update($invoiceId, [
                'customer_id' => $invoice['customer_id'],
                'invoice_number' => $invoice['invoice_number'],
                'invoice_date' => $invoice['invoice_date'],
                'due_date' => $invoice['due_date'],
                'subtotal' => $invoice['subtotal'],
                'tax_amount' => $invoice['tax_amount'],
                'discount_amount' => $invoice['discount_amount'],
                'total_amount' => $invoice['total_amount'],
                'paid_amount' => $newPaid,
                'status' => $status,
                'notes' => $invoice['notes'],
            ]);

            $_SESSION['success'] = 'Payment recorded successfully.';
            redirect('/sales/payments');
        }

        $customers = $this->customerModel->all();
        $invoices = $this->invoiceModel->all();
        $title = 'Record Payment';
        return view('sales/payment_form', compact('title', 'customers', 'invoices'));
    }

    public function view() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $payment = $this->paymentModel->find($id);
        if (!$payment) { $_SESSION['error'] = 'Payment not found.'; redirect('/sales/payments'); }
        $title = 'Payment Details';
        return view('sales/payment_view', compact('title', 'payment'));
    }
}
