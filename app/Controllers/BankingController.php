<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\BankAccount;

class BankingController extends BaseController {
    private BankAccount $model;
    public function __construct() { parent::__construct(); $this->model = new BankAccount(); }

    public function index() {
        $this->requireAuth();
        $accounts = $this->model->all();
        $totalBalance = $this->model->totalBalance();
        $title = 'Bank & Cash Accounts';
        return view('banking/accounts', compact('accounts', 'totalBalance', 'title'));
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['account_name'])) { $_SESSION['error'] = 'Account name is required.'; redirect('/banking/create'); }
            $this->model->create($_POST);
            $_SESSION['success'] = 'Account added successfully.';
            redirect('/banking/accounts');
        }
        $title = 'Add Bank / Cash Account';
        return view('banking/account_form', compact('title'));
    }

    public function transactions() {
        $this->requireAuth();
        $accounts = $this->model->all();
        $selectedId = (int)($_GET['account_id'] ?? ($accounts[0]['id'] ?? 0));
        $transactions = $selectedId ? $this->model->transactions($selectedId) : [];
        $selectedAccount = $selectedId ? $this->model->find($selectedId) : null;
        $title = 'Bank Transactions';
        return view('banking/transactions', compact('accounts', 'transactions', 'selectedAccount', 'title'));
    }

    public function reconciliation() {
        $this->requireAuth();
        $accounts = $this->model->all();
        $title = 'Bank Reconciliation';
        return view('banking/reconciliation', compact('accounts', 'title'));
    }
}
