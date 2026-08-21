<?php
namespace App\Controllers\Inventory;
use App\Controllers\BaseController;
use App\Models\Product;

class ProductController extends BaseController {
    private Product $model;
    public function __construct() { parent::__construct(); $this->model = new Product(); }

    public function index() {
        $this->requireAuth();
        $products = $this->model->all();
        $lowStock = $this->model->lowStock();
        $title = 'Products';
        return view('inventory/products', compact('products', 'lowStock', 'title'));
    }

    public function create() {
        $this->requireAuth();
        $isModal = ($_GET['modal'] ?? '') === '1';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                if ($isModal) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Product name is required.']);
                    return;
                }
                $_SESSION['error'] = 'Product name is required.';
                redirect('/inventory/products/create');
            }
            $productId = $this->model->create($_POST);
            if ($isModal) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'id' => $productId, 'name' => $name]);
                return;
            }
            $_SESSION['success'] = "Product '{$name}' added successfully.";
            redirect('/inventory/products');
        }
        $categories = $this->model->categories();
        $units = $this->model->units();
        $title = 'Add Product';
        return view('inventory/product_form', compact('title', 'categories', 'units'));
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->model->find($id);
        if (!$product) { $_SESSION['error'] = 'Product not found.'; redirect('/inventory/products'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) { $_SESSION['error'] = 'Product name is required.'; redirect('/inventory/products/edit?id=' . $id); }
            $this->model->update($id, $_POST);
            $_SESSION['success'] = "Product '{$name}' updated successfully.";
            redirect('/inventory/products');
        }

        $categories = $this->model->categories();
        $units = $this->model->units();
        $title = 'Edit Product';
        return view('inventory/product_form', compact('title', 'product', 'categories', 'units'));
    }

    public function delete() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->model->find($id);
        if (!$product) { $_SESSION['error'] = 'Product not found.'; redirect('/inventory/products'); }
        $this->model->delete($id);
        $_SESSION['success'] = "Product '{$product['name']}' deleted successfully.";
        redirect('/inventory/products');
    }
}
