<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\Expense;

class ExpenseController extends BaseController {
    private Expense $model;
    public function __construct() { parent::__construct(); $this->model = new Expense(); }

    public function index() {
        $this->requireAuth();
        $expenses = $this->model->all();
        $categories = $this->model->categories();
        $total = $this->model->totalThisMonth();
        $title = 'Expenses';
        return view('expenses/index', compact('expenses', 'categories', 'total', 'title'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['expense_date']) || empty($_POST['amount'])) {
                $_SESSION['error'] = 'Date and amount are required.';
                redirect('/expenses/create');
            }
            $this->model->create($_POST);
            $_SESSION['success'] = 'Expense recorded successfully.';
            redirect('/expenses');
        }
        $categories = $this->model->categories();
        $title = 'Record Expense';
        return view('expenses/form', compact('title', 'categories'));
    }

    public function categories() {
        $this->requireAuth();
        $categories = $this->model->categories();
        $title = 'Expense Categories';
        return view('expenses/categories', compact('categories', 'title'));
    }
}
