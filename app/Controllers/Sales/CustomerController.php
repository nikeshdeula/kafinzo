<?php
namespace App\Controllers\Sales;
use App\Controllers\BaseController;
use App\Models\Customer;

class CustomerController extends BaseController {
    private Customer $model;
    public function __construct() { parent::__construct(); $this->model = new Customer(); }

    public function index() {
        $this->requireAuth();
        $customers = $this->model->all();
        $title = 'Customers';
        return view('sales/customers', compact('customers', 'title'));
    }

    public function create() {
        $this->requireAuth();
        $isModal = ($_GET['modal'] ?? '') === '1';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                if ($isModal) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Customer name is required.']);
                    return;
                }
                $_SESSION['error'] = 'Customer name is required.';
                redirect('/sales/customers/create');
            }
            $customerId = $this->model->create($_POST);
            if ($isModal) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'id' => $customerId, 'name' => $name]);
                return;
            }
            $_SESSION['success'] = "Customer '{$name}' added successfully.";
            redirect('/sales/customers');
        }
        $title = 'Add Customer';
        return view('sales/customer_form', compact('title'));
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $customer = $this->model->find($id);
        if (!$customer) { $_SESSION['error'] = 'Customer not found.'; redirect('/sales/customers'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, $_POST);
            $_SESSION['success'] = 'Customer updated.';
            redirect('/sales/customers');
        }
        $title = 'Edit Customer';
        return view('sales/customer_form', compact('title', 'customer'));
    }
}
