<?php
namespace App\Controllers\Sales;
use App\Controllers\BaseController;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Product;

class QuotationController extends BaseController {
    private Quotation $quotationModel;
    private Customer $customerModel;
    private Product $productModel;

    public function __construct() {
        parent::__construct();
        $this->quotationModel = new Quotation();
        $this->customerModel = new Customer();
        $this->productModel = new Product();
    }

    public function index() {
        $this->requireAuth();
        $quotations = $this->quotationModel->all();
        $title = 'Quotations';
        return view('sales/quotations', compact('quotations', 'title'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerId = (int)($_POST['customer_id'] ?? 0);
            $quotationNumber = trim($_POST['quotation_number'] ?? '');
            $quotationDate = $_POST['quotation_date'] ?? date('Y-m-d');
            $validUntil = $_POST['valid_until'] ?? null;
            $subtotal = (float)($_POST['subtotal'] ?? 0);
            $taxAmount = (float)($_POST['tax_amount'] ?? 0);
            $discountAmount = (float)($_POST['discount_amount'] ?? 0);
            $totalAmount = (float)($_POST['total_amount'] ?? 0);
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');
            $items = $_POST['items'] ?? [];

            if (empty($customerId) || empty($quotationNumber)) {
                $_SESSION['error'] = 'Customer and quotation number are required.';
                redirect('/sales/quotations/create');
            }

            $quotationId = $this->quotationModel->create([
                'business_id' => $this->businessId(),
                'customer_id' => $customerId,
                'quotation_number' => $quotationNumber,
                'quotation_date' => $quotationDate,
                'valid_until' => $validUntil,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => $status,
                'notes' => $notes,
            ]);

            if (!empty($items)) {
                $pdo = Database::getInstance()->getConnection();
                $itemSql = "INSERT INTO quotation_items (quotation_id,product_id,description,quantity,unit_price,discount_pct,tax_rate,amount) VALUES (:qid,:pid,:desc,:qty,:price,:disc,:tax,:amt)";
                $istmt = $pdo->prepare($itemSql);
                foreach ($items as $item) {
                    $istmt->execute([
                        'qid' => $quotationId,
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

            $_SESSION['success'] = "Quotation #{$quotationNumber} created successfully.";
            redirect('/sales/quotations');
        }

        $quotationNumber = $this->quotationModel->nextNumber();
        $customers = $this->customerModel->all();
        $products = $this->productModel->all();
        $title = 'New Quotation';
        $taxRate = \tax_rate();
        return view('sales/quotation_form', compact('title', 'quotationNumber', 'customers', 'products', 'taxRate'));
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $quotation = $this->quotationModel->find($id);
        if (!$quotation) { $_SESSION['error'] = 'Quotation not found.'; redirect('/sales/quotations'); }

        $quotation['items'] = $this->quotationModel->items($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerId = (int)($_POST['customer_id'] ?? 0);
            $quotationNumber = trim($_POST['quotation_number'] ?? '');
            $quotationDate = $_POST['quotation_date'] ?? date('Y-m-d');
            $validUntil = $_POST['valid_until'] ?? null;
            $subtotal = (float)($_POST['subtotal'] ?? 0);
            $taxAmount = (float)($_POST['tax_amount'] ?? 0);
            $discountAmount = (float)($_POST['discount_amount'] ?? 0);
            $totalAmount = (float)($_POST['total_amount'] ?? 0);
            $status = trim($_POST['status'] ?? 'draft');
            $notes = trim($_POST['notes'] ?? '');
            $items = $_POST['items'] ?? [];

            if (empty($customerId) || empty($quotationNumber)) {
                $_SESSION['error'] = 'Customer and quotation number are required.';
                redirect('/sales/quotations/edit?id=' . $id);
            }

            $this->quotationModel->update($id, [
                'customer_id' => $customerId,
                'quotation_number' => $quotationNumber,
                'quotation_date' => $quotationDate,
                'valid_until' => $validUntil,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => $status,
                'notes' => $notes,
            ]);

            $pdo = Database::getInstance()->getConnection();
            $pdo->prepare("DELETE FROM quotation_items WHERE quotation_id=:qid")->execute(['qid'=>$id]);

            if (!empty($items)) {
                $itemSql = "INSERT INTO quotation_items (quotation_id,product_id,description,quantity,unit_price,discount_pct,tax_rate,amount) VALUES (:qid,:pid,:desc,:qty,:price,:disc,:tax,:amt)";
                $istmt = $pdo->prepare($itemSql);
                foreach ($items as $item) {
                    $istmt->execute([
                        'qid' => $id,
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

            $_SESSION['success'] = 'Quotation updated successfully.';
            redirect('/sales/quotations');
        }

        $customers = $this->customerModel->all();
        $products = $this->productModel->all();
        $title = 'Edit Quotation';
        $taxRate = \tax_rate();
        return view('sales/quotation_form', compact('title', 'quotation', 'customers', 'products', 'taxRate'));
    }

    public function delete() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $quotation = $this->quotationModel->find($id);
        if (!$quotation) { $_SESSION['error'] = 'Quotation not found.'; redirect('/sales/quotations'); }
        $this->quotationModel->delete($id);
        $_SESSION['success'] = 'Quotation deleted successfully.';
        redirect('/sales/quotations');
    }

    public function convert() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $quotation = $this->quotationModel->find($id);
        if (!$quotation) { $_SESSION['error'] = 'Quotation not found.'; redirect('/sales/quotations'); }
        if ($quotation['status'] === 'converted') { $_SESSION['error'] = 'Quotation already converted to invoice.'; redirect('/sales/quotations'); }

        $items = $this->quotationModel->items($id);
        $invoiceModel = new Invoice();
        $invoiceNumber = $invoiceModel->nextNumber();

            $validUntil = $quotation['valid_until'] ?: date('Y-m-d', strtotime('+30 days'));
            $dueDate = date('Y-m-d', strtotime($validUntil));

            $invoiceId = $invoiceModel->create([
                'business_id' => $this->businessId(),
                'customer_id' => $quotation['customer_id'],
                'invoice_number' => $invoiceNumber,
                'invoice_date' => date('Y-m-d'),
                'due_date' => $dueDate,
                'subtotal' => $quotation['subtotal'],
                'tax_amount' => $quotation['tax_amount'],
                'discount_amount' => $quotation['discount_amount'],
                'total_amount' => $quotation['total_amount'],
                'paid_amount' => 0,
                'status' => 'unpaid',
                'notes' => $quotation['notes'],
            ]);

        if (!empty($items)) {
            $pdo = Database::getInstance()->getConnection();
            $itemSql = "INSERT INTO invoice_items (invoice_id,product_id,description,quantity,unit_price,discount_pct,tax_rate,amount) VALUES (:iid,:pid,:desc,:qty,:price,:disc,:tax,:amt)";
            $istmt = $pdo->prepare($itemSql);
            foreach ($items as $item) {
                $istmt->execute([
                    'iid' => $invoiceId,
                    'pid' => $item['product_id'] ?: null,
                    'desc' => $item['description'],
                    'qty' => $item['quantity'],
                    'price' => $item['unit_price'],
                    'disc' => $item['discount_pct'],
                    'tax' => $item['tax_rate'],
                    'amt' => $item['amount'],
                ]);
            }
        }

        $this->quotationModel->update($id, ['status' => 'converted']);
        $_SESSION['success'] = "Quotation converted to Invoice #{$invoiceNumber}.";
        redirect('/sales/invoices');
    }
}
