<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Account;
use App\Models\LedgerEntry;

class LedgerController extends BaseController
{
    private LedgerEntry $ledger;
    private Account $account;

    public function __construct()
    {
        parent::__construct();
        $this->ledger  = new LedgerEntry();
        $this->account = new Account();
    }

    public function index()
    {
        $this->requireAuth();

        $accounts = $this->account->getForSelect();
        $selectedAccountId = isset($_GET['account_id']) ? (int)$_GET['account_id'] : ($accounts[0]['id'] ?? 0);
        $from = $_GET['from'] ?? '';
        $to   = $_GET['to'] ?? '';

        $entries = [];
        $accountName = '';

        if ($selectedAccountId > 0) {
            $account = $this->account->findById($selectedAccountId);
            $accountName = $account ? $account['name'] : '';

            if (!empty($from) && !empty($to)) {
                $entries = $this->ledger->getByDateRange($selectedAccountId, $from, $to);
            } else {
                $entries = $this->ledger->getByAccount($selectedAccountId);
            }
        }

        $title = 'General Ledger';
        return view('accounting/general_ledger', compact(
            'accounts', 'entries', 'selectedAccountId', 'accountName', 'from', 'to', 'title'
        ));
    }
}
