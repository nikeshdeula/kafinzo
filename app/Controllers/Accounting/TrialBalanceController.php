<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\TrialBalance;

class TrialBalanceController extends BaseController
{
    private TrialBalance $trialBalance;

    public function __construct()
    {
        parent::__construct();
        $this->trialBalance = new TrialBalance();
    }

    public function index()
    {
        $this->requireAuth();

        $grouped = $this->trialBalance->getGroupedBalance();

        $grandTotalDebit  = 0;
        $grandTotalCredit = 0;

        foreach ($grouped as $type) {
            $grandTotalDebit  += $type['total_debit'];
            $grandTotalCredit += $type['total_credit'];
        }

        $title = 'Trial Balance';
        return view('accounting/trial_balance', compact('grouped', 'grandTotalDebit', 'grandTotalCredit', 'title'));
    }
}
