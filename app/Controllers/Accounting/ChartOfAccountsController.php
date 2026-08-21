<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Account;

class ChartOfAccountsController extends BaseController
{
    private Account $accountModel;

    public function __construct()
    {
        parent::__construct();
        $this->accountModel = new Account();
    }

    public function index()
    {
        $this->requireAuth();

        $accounts = $this->accountModel->getAllGroupedByType();

        $title = 'Chart of Accounts';
        return view('accounting/chart_of_accounts', compact('accounts', 'title'));
    }

    public function create()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }

        $title = 'Add Account';
        return view('accounting/account_form', compact('title'));
    }

    private function store()
    {
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? '';

        if (empty($code) || empty($name) || empty($type)) {
            $_SESSION['error'] = 'Code, Name, and Type are required.';
            redirect('/accounting/chart-of-accounts/create');
        }

        if ($this->accountModel->codeExists($code)) {
            $_SESSION['error'] = "Account code '{$code}' already exists.";
            redirect('/accounting/chart-of-accounts/create');
        }

        $this->accountModel->create([
            'code'            => $code,
            'name'            => $name,
            'type'            => $type,
            'sub_type'        => $_POST['sub_type'] ?? null,
            'description'     => $_POST['description'] ?? null,
            'opening_balance' => $_POST['opening_balance'] ?? 0,
        ]);

        $_SESSION['success'] = "Account '{$name}' created successfully.";
        redirect('/accounting/chart-of-accounts');
    }
}
