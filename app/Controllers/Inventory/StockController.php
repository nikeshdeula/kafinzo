<?php
namespace App\Controllers\Inventory;
use App\Controllers\BaseController;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;

class StockController extends BaseController {
    private StockMovement $model;
    private Product $productModel;
    private Warehouse $warehouseModel;
    public function __construct() {
        parent::__construct();
        $this->model = new StockMovement();
        $this->productModel = new Product();
        $this->warehouseModel = new Warehouse();
    }

    public function index() {
        $this->requireAuth();
        $filters = [
            'product_id'    => $_GET['product_id'] ?? '',
            'reference_type' => $_GET['reference_type'] ?? '',
            'from_date'      => $_GET['from_date'] ?? '',
            'to_date'        => $_GET['to_date'] ?? '',
        ];
        $movements = $this->model->all($this->businessId(), array_filter($filters));
        $products = $this->productModel->all();
        $warehouses = $this->warehouseModel->all();
        $title = 'Stock Movements';
        return view('inventory/stock_movement', compact('movements', 'products', 'warehouses', 'filters', 'title'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = (int)($_POST['product_id'] ?? 0);
            $qty = (float)($_POST['quantity_change'] ?? 0);
            if (!$productId) {
                $_SESSION['error'] = 'Please select a product.';
                redirect('/inventory/stock-movement/create');
            }
            if ($qty === 0.0) {
                $_SESSION['error'] = 'Quantity change cannot be zero.';
                redirect('/inventory/stock-movement/create');
            }
            $this->model->create($_POST);
            $_SESSION['success'] = 'Stock movement recorded successfully.';
            redirect('/inventory/stock-movement');
        }
        $products = $this->productModel->all();
        $warehouses = $this->warehouseModel->all();
        $title = 'Record Stock Movement';
        return view('inventory/stock_form', compact('title', 'products', 'warehouses'));
    }
}
