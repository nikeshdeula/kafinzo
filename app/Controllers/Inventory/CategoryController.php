<?php
namespace App\Controllers\Inventory;
use App\Controllers\BaseController;
use App\Models\Category;

class CategoryController extends BaseController {
    private Category $model;
    public function __construct() { parent::__construct(); $this->model = new Category(); }

    public function index() {
        $this->requireAuth();
        $categories = $this->model->all();
        $title = 'Product Categories';
        return view('inventory/categories', compact('categories', 'title'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = 'Category name is required.';
                redirect('/inventory/categories/create');
            }
            $this->model->create($_POST);
            $_SESSION['success'] = "Category '{$name}' created successfully.";
            redirect('/inventory/categories');
        }
        $title = 'Add Category';
        return view('inventory/category_form', compact('title'));
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $category = $this->model->find($id);
        if (!$category) {
            $_SESSION['error'] = 'Category not found.';
            redirect('/inventory/categories');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = 'Category name is required.';
                redirect('/inventory/categories/edit?id=' . $id);
            }
            $this->model->update(array_merge($_POST, ['id' => $id]));
            $_SESSION['success'] = "Category '{$name}' updated successfully.";
            redirect('/inventory/categories');
        }

        $title = 'Edit Category';
        return view('inventory/category_form', compact('title', 'category'));
    }

    public function delete() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $category = $this->model->find($id);
        if ($category) {
            $this->model->delete($id);
            $_SESSION['success'] = "Category '{$category['name']}' deleted successfully.";
        } else {
            $_SESSION['error'] = 'Category not found.';
        }
        redirect('/inventory/categories');
    }
}
