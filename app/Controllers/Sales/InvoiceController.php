<?php
namespace App\Controllers\Sales;
use App\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;

class InvoiceController extends BaseController {
    private Invoice $invoiceModel;
    private Customer $customerModel;
    private Product $productModel;

    public function __construct() {
        parent::__construct();
        $this->invoiceModel = new Invoice();
        $this->customerModel = new Customer();
        $this->productModel = new Product();
    }

    public function index() {
        $this->requireAuth();
        $invoices = $this->invoiceModel->all();
        $title = 'Invoices';
        return view('sales/invoices', compact('invoices', 'title'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerId = (int)($_POST['customer_id'] ?? 0);
            $invoiceNumber = trim($_POST['invoice_number'] ?? '');
            $invoiceDate = $_POST['invoice_date'] ?? date('Y-m-d');
            $dueDate = $_POST['due_date'] ?? null;
            $subtotal = (float)($_POST['subtotal'] ?? 0);
            $taxAmount = (float)($_POST['tax_amount'] ?? 0);
            $discountAmount = (float)($_POST['discount_amount'] ?? 0);
            $totalAmount = (float)($_POST['total_amount'] ?? 0);
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');
            $items = $_POST['items'] ?? [];

            if (empty($customerId) || empty($invoiceNumber)) {
                $_SESSION['error'] = 'Customer and invoice number are required.';
                redirect('/sales/invoices/create');
            }

            $invoiceId = $this->invoiceModel->create([
                'business_id' => 1,
                'customer_id' => $customerId,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => $status,
                'notes' => $notes,
            ]);

            if (!empty($items)) {
                $stmt = $this->invoiceModel;
                $db = $this->invoiceModel;
                $pdo = Database::getInstance()->getConnection();
                $itemSql = "INSERT INTO invoice_items (invoice_id,product_id,description,quantity,unit_price,discount_pct,tax_rate,amount) VALUES (:iid,:pid,:desc,:qty,:price,:disc,:tax,:amt)";
                $istmt = $pdo->prepare($itemSql);
                foreach ($items as $item) {
                    $istmt->execute([
                        'iid' => $invoiceId,
                        'pid' => $item['product_id'] ?: null,
                        'desc' => $item['description'] ?? '',
                        'qty' => $item['quantity'] ?? 1,
                        'price' => $item['unit_price'] ?? 0,
                        'disc' => $item['discount_pct'] ?? 0,
                        'tax' => ($item['tax_rate'] ?? 0) ?: \tax_rate(),
                        'amt' => $item['amount'] ?? 0,
                    ]);
                }
            }

            $_SESSION['success'] = "Invoice #{$invoiceNumber} created successfully.";
            redirect('/sales/invoices');
        }

        $invoiceNumber = $this->invoiceModel->nextNumber();
        $customers = $this->customerModel->all();
        $products = $this->productModel->all();
        $title = 'New Invoice';
        $taxRate = \tax_rate();
        return view('sales/invoice_form', compact('title', 'invoiceNumber', 'customers', 'products', 'taxRate'));
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $invoice = $this->invoiceModel->find($id);
        if (!$invoice) { $_SESSION['error'] = 'Invoice not found.'; redirect('/sales/invoices'); }

        $invoice['items'] = $this->invoiceModel->items($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerId = (int)($_POST['customer_id'] ?? 0);
            $invoiceNumber = trim($_POST['invoice_number'] ?? '');
            $invoiceDate = $_POST['invoice_date'] ?? date('Y-m-d');
            $dueDate = $_POST['due_date'] ?? null;
            $subtotal = (float)($_POST['subtotal'] ?? 0);
            $taxAmount = (float)($_POST['tax_amount'] ?? 0);
            $discountAmount = (float)($_POST['discount_amount'] ?? 0);
            $totalAmount = (float)($_POST['total_amount'] ?? 0);
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');
            $items = $_POST['items'] ?? [];

            if (empty($customerId) || empty($invoiceNumber)) {
                $_SESSION['error'] = 'Customer and invoice number are required.';
                redirect('/sales/invoices/edit?id=' . $id);
            }

            $this->invoiceModel->update($id, [
                'customer_id' => $customerId,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $invoice['paid_amount'],
                'status' => $status,
                'notes' => $notes,
            ]);

            $pdo = Database::getInstance()->getConnection();
            $pdo->prepare("DELETE FROM invoice_items WHERE invoice_id=:iid")->execute(['iid'=>$id]);

            if (!empty($items)) {
                $itemSql = "INSERT INTO invoice_items (invoice_id,product_id,description,quantity,unit_price,discount_pct,tax_rate,amount) VALUES (:iid,:pid,:desc,:qty,:price,:disc,:tax,:amt)";
                $istmt = $pdo->prepare($itemSql);
                foreach ($items as $item) {
                    $istmt->execute([
                        'iid' => $id,
                        'pid' => $item['product_id'] ?: null,
                        'desc' => $item['description'] ?? '',
                        'qty' => $item['quantity'] ?? 1,
                        'price' => $item['unit_price'] ?? 0,
                        'disc' => $item['discount_pct'] ?? 0,
                        'tax' => ($item['tax_rate'] ?? 0) ?: \tax_rate(),
                        'amt' => $item['amount'] ?? 0,
                    ]);
                }
            }

            $_SESSION['success'] = 'Invoice updated successfully.';
            redirect('/sales/invoices');
        }

        $customers = $this->customerModel->all();
        $products = $this->productModel->all();
        $title = 'Edit Invoice';
        $taxRate = \tax_rate();
        return view('sales/invoice_form', compact('title', 'invoice', 'customers', 'products', 'taxRate'));
    }

    public function delete() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $invoice = $this->invoiceModel->find($id);
        if (!$invoice) { $_SESSION['error'] = 'Invoice not found.'; redirect('/sales/invoices'); }
        $this->invoiceModel->delete($id);
        $_SESSION['success'] = 'Invoice deleted successfully.';
        redirect('/sales/invoices');
    }
}
