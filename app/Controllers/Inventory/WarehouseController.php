<?php
namespace App\Controllers\Inventory;
use App\Controllers\BaseController;
use App\Models\Warehouse;

class WarehouseController extends BaseController {
    private Warehouse $model;
    public function __construct() { parent::__construct(); $this->model = new Warehouse(); }

    public function index() {
        $this->requireAuth();
        $warehouses = $this->model->all();
        $title = 'Warehouses';
        return view('inventory/warehouses', compact('warehouses', 'title'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = 'Warehouse name is required.';
                redirect('/inventory/warehouses/create');
            }
            $this->model->create($_POST);
            $_SESSION['success'] = "Warehouse '{$name}' created successfully.";
            redirect('/inventory/warehouses');
        }
        $title = 'Add Warehouse';
        return view('inventory/warehouse_form', compact('title'));
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $warehouse = $this->model->find($id);
        if (!$warehouse) {
            $_SESSION['error'] = 'Warehouse not found.';
            redirect('/inventory/warehouses');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = 'Warehouse name is required.';
                redirect('/inventory/warehouses/edit?id=' . $id);
            }
            $this->model->update(array_merge($_POST, ['id' => $id]));
            $_SESSION['success'] = "Warehouse '{$name}' updated successfully.";
            redirect('/inventory/warehouses');
        }

        $title = 'Edit Warehouse';
        return view('inventory/warehouse_form', compact('title', 'warehouse'));
    }

    public function delete() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $warehouse = $this->model->find($id);
        if ($warehouse) {
            $this->model->delete($id);
            $_SESSION['success'] = "Warehouse '{$warehouse['name']}' deleted successfully.";
        } else {
            $_SESSION['error'] = 'Warehouse not found.';
        }
        redirect('/inventory/warehouses');
    }
}
