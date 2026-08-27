<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\LedgerEntry;

class JournalController extends BaseController
{
    private JournalEntry $journal;
    private LedgerEntry $ledger;
    private Account $account;

    public function __construct()
    {
        parent::__construct();
        $this->journal = new JournalEntry();
        $this->ledger  = new LedgerEntry();
        $this->account = new Account();
    }

    public function index()
    {
        $this->requireAuth();

        $journals = $this->journal->all();

        foreach ($journals as &$j) {
            $entries = $this->ledger->getByJournal($j['id']);
            $j['total_debit']  = array_sum(array_column($entries, 'debit'));
            $j['total_credit'] = array_sum(array_column($entries, 'credit'));
        }

        $title = 'Journal Entries';
        return view('accounting/journal_entries', compact('journals', 'title'));
    }

    public function create()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }

        $journalNumber = $this->journal->nextNumber();
        $accounts = $this->account->getForSelect();
        $title = 'New Journal Entry';
        return view('accounting/journal_form', compact('journalNumber', 'accounts', 'title'));
    }

    private function store(): void
    {
        $entryDate    = $_POST['entry_date'] ?? date('Y-m-d');
        $description  = trim($_POST['description'] ?? '');
        $reference    = trim($_POST['reference'] ?? '');
        $accountIds   = $_POST['account_id'] ?? [];
        $debits       = $_POST['debit'] ?? [];
        $credits      = $_POST['credit'] ?? [];
        $descriptions = $_POST['line_description'] ?? [];

        if (empty($description)) {
            $_SESSION['error'] = 'Description is required.';
            redirect('/accounting/journal-entries/create');
        }

        $rows = [];
        $totalDebit  = 0;
        $totalCredit = 0;

        for ($i = 0; $i < count($accountIds); $i++) {
            $aid   = (int)($accountIds[$i] ?? 0);
            $debit  = (float)($debits[$i] ?? 0);
            $credit = (float)($credits[$i] ?? 0);
            $desc   = trim($descriptions[$i] ?? '');

            if ($aid <= 0 || ($debit <= 0 && $credit <= 0)) {
                continue;
            }

            if ($debit > 0 && $credit > 0) {
                $_SESSION['error'] = 'A line cannot have both debit and credit.';
                redirect('/accounting/journal-entries/create');
            }

            $rows[] = [
                'account_id' => $aid,
                'debit'      => $debit,
                'credit'     => $credit,
                'description' => $desc,
            ];

            $totalDebit  += $debit;
            $totalCredit += $credit;
        }

        if (empty($rows)) {
            $_SESSION['error'] = 'At least one debit/credit line is required.';
            redirect('/accounting/journal-entries/create');
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            $_SESSION['error'] = "Journal entry must balance. Total Debit: NPR " . number_format($totalDebit, 2) . ", Total Credit: NPR " . number_format($totalCredit, 2) . ".";
            redirect('/accounting/journal-entries/create');
        }

        $journalNumber = $this->journal->nextNumber();
        $journalId = $this->journal->create([
            'business_id'   => $this->businessId(),
            'journal_number' => $journalNumber,
            'entry_date'     => $entryDate,
            'description'    => $description,
            'reference'      => $reference ?: null,
        ]);

        foreach ($rows as $row) {
            $this->ledger->create([
                'business_id'      => $this->businessId(),
                'journal_entry_id' => $journalId,
                'account_id'       => $row['account_id'],
                'description'      => $row['description'],
                'debit'            => $row['debit'],
                'credit'           => $row['credit'],
            ]);
        }

        $_SESSION['success'] = "Journal entry {$journalNumber} created successfully.";
        redirect('/accounting/journal-entries');
    }

    public function edit()
    {
        $this->requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        $journal = $this->journal->find($id);

        if (!$journal) {
            $_SESSION['error'] = 'Journal entry not found.';
            redirect('/accounting/journal-entries');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->update($id);
        }

        $ledgerRows = $this->ledger->getByJournal($id);
        $accounts   = $this->account->getForSelect();
        $title      = 'Edit Journal Entry';
        $journalNumber = $journal['journal_number'];
        return view('accounting/journal_form', compact('journal', 'ledgerRows', 'accounts', 'title', 'journalNumber'));
    }

    private function update(int $id): void
    {
        $entryDate   = $_POST['entry_date'] ?? date('Y-m-d');
        $description = trim($_POST['description'] ?? '');
        $reference   = trim($_POST['reference'] ?? '');
        $accountIds  = $_POST['account_id'] ?? [];
        $debits      = $_POST['debit'] ?? [];
        $credits     = $_POST['credit'] ?? [];
        $descriptions = $_POST['line_description'] ?? [];

        if (empty($description)) {
            $_SESSION['error'] = 'Description is required.';
            redirect('/accounting/journal-entries/edit?id=' . $id);
        }

        $rows = [];
        $totalDebit  = 0;
        $totalCredit = 0;

        for ($i = 0; $i < count($accountIds); $i++) {
            $aid   = (int)($accountIds[$i] ?? 0);
            $debit  = (float)($debits[$i] ?? 0);
            $credit = (float)($credits[$i] ?? 0);
            $desc   = trim($descriptions[$i] ?? '');

            if ($aid <= 0 || ($debit <= 0 && $credit <= 0)) {
                continue;
            }

            if ($debit > 0 && $credit > 0) {
                $_SESSION['error'] = 'A line cannot have both debit and credit.';
                redirect('/accounting/journal-entries/edit?id=' . $id);
            }

            $rows[] = [
                'account_id' => $aid,
                'debit'      => $debit,
                'credit'     => $credit,
                'description' => $desc,
            ];

            $totalDebit  += $debit;
            $totalCredit += $credit;
        }

        if (empty($rows)) {
            $_SESSION['error'] = 'At least one debit/credit line is required.';
            redirect('/accounting/journal-entries/edit?id=' . $id);
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            $_SESSION['error'] = "Journal entry must balance. Total Debit: NPR " . number_format($totalDebit, 2) . ", Total Credit: NPR " . number_format($totalCredit, 2) . ".";
            redirect('/accounting/journal-entries/edit?id=' . $id);
        }

        $this->journal->update($id, [
            'entry_date'  => $entryDate,
            'description' => $description,
            'reference'   => $reference ?: null,
        ]);

        $this->ledger->deleteByJournal($id);

        foreach ($rows as $row) {
            $this->ledger->create([
                'business_id'      => $this->businessId(),
                'journal_entry_id' => $id,
                'account_id'       => $row['account_id'],
                'description'      => $row['description'],
                'debit'            => $row['debit'],
                'credit'           => $row['credit'],
            ]);
        }

        $_SESSION['success'] = 'Journal entry updated successfully.';
        redirect('/accounting/journal-entries');
    }

    public function delete(): void
    {
        $this->requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        $journal = $this->journal->find($id);

        if (!$journal) {
            $_SESSION['error'] = 'Journal entry not found.';
            redirect('/accounting/journal-entries');
        }

        $this->ledger->deleteByJournal($id);
        $this->journal->delete($id);

        $_SESSION['success'] = 'Journal entry deleted successfully.';
        redirect('/accounting/journal-entries');
    }
}
