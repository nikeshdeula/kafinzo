<?php
namespace App\Controllers\Purchases;
use App\Controllers\BaseController;
use App\Models\Supplier;

class SupplierController extends BaseController {
    private Supplier $model;
    public function __construct() { parent::__construct(); $this->model = new Supplier(); }

    public function index() {
        $this->requireAuth();
        $suppliers = $this->model->all();
        $title = 'Suppliers';
        return view('purchases/suppliers', compact('suppliers', 'title'));
    }

    public function create() {
        $this->requireAuth();
        $isModal = ($_GET['modal'] ?? '') === '1';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                if ($isModal) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Supplier name is required.']);
                    return;
                }
                $_SESSION['error'] = 'Supplier name is required.';
                redirect('/purchases/suppliers/create');
            }
            $supplierId = $this->model->create($_POST);
            if ($isModal) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'id' => $supplierId, 'name' => $name]);
                return;
            }
            $_SESSION['success'] = "Supplier '{$name}' added successfully.";
            redirect('/purchases/suppliers');
        }
        $title = 'Add Supplier';
        return view('purchases/supplier_form', compact('title'));
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $supplier = $this->model->find($id);
        if (!$supplier) { $_SESSION['error'] = 'Supplier not found.'; redirect('/purchases/suppliers'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, $_POST);
            $_SESSION['success'] = 'Supplier updated.';
            redirect('/purchases/suppliers');
        }
        $title = 'Edit Supplier';
        return view('purchases/supplier_form', compact('title', 'supplier'));
    }
}
