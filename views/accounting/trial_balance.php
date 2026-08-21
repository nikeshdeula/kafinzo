<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-bar-chart-steps text-primary me-2"></i>Trial Balance</h4>
        <p>View the trial balance grouped by account type.</p>
    </div>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<?php
$typeLabels = [
    'asset'     => ['label' => 'Assets',      'icon' => 'bi-bank',              'color' => '#4361ee', 'bg' => '#eef0fd'],
    'liability' => ['label' => 'Liabilities', 'icon' => 'bi-exclamation-circle','color' => '#ef233c', 'bg' => '#fdedf0'],
    'equity'    => ['label' => 'Equity',       'icon' => 'bi-person-fill',       'color' => '#7209b7', 'bg' => '#f3e8fd'],
    'revenue'   => ['label' => 'Revenue',      'icon' => 'bi-graph-up-arrow',    'color' => '#06d6a0', 'bg' => '#e6fdf7'],
    'expense'   => ['label' => 'Expenses',     'icon' => 'bi-wallet2',           'color' => '#fb8500', 'bg' => '#fff3e6'],
];
?>

<?php foreach ($grouped as $typeKey => $group): ?>
    <?php if (empty($group['accounts'])) continue; ?>
    <?php $meta = $typeLabels[$typeKey]; ?>
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2 py-3" style="background:<?= $meta['bg'] ?>;border-bottom:2px solid <?= $meta['color'] ?>22;">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:<?= $meta['color'] ?>22;">
            <i class="bi <?= $meta['icon'] ?>" style="color:<?= $meta['color'] ?>;font-size:1.1rem;"></i>
        </div>
        <span class="fw-700" style="color:<?= $meta['color'] ?>;font-weight:700;font-size:1rem;"><?= $meta['label'] ?></span>
        <span class="badge ms-auto" style="background:<?= $meta['color'] ?>"><?= count($group['accounts']) ?> accounts</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:100px">Code</th>
                    <th>Account Name</th>
                    <th class="text-end">Debit Balance (NPR)</th>
                    <th class="text-end">Credit Balance (NPR)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $groupDebitTotal = 0;
                $groupCreditTotal = 0;
                foreach ($group['accounts'] as $acc):
                    $debitCol  = max(0, (float)$acc['total_debit'] - (float)$acc['total_credit']);
                    $creditCol = max(0, (float)$acc['total_credit'] - (float)$acc['total_debit']);
                    $groupDebitTotal  += $debitCol;
                    $groupCreditTotal += $creditCol;
                ?>
                <tr>
                    <td><code class="text-primary fw-bold"><?= htmlspecialchars($acc['code']) ?></code></td>
                    <td><?= htmlspecialchars($acc['name']) ?></td>
                    <td class="text-end fw-500"><?= $debitCol > 0 ? 'NPR ' . number_format($debitCol, 2) : '—' ?></td>
                    <td class="text-end fw-500"><?= $creditCol > 0 ? 'NPR ' . number_format($creditCol, 2) : '—' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="fw-700 bg-light">
                    <td colspan="2" class="text-end">Total <?= $meta['label'] ?>:</td>
                    <td class="text-end">NPR <?= number_format($groupDebitTotal, 2) ?></td>
                    <td class="text-end">NPR <?= number_format($groupCreditTotal, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php endforeach; ?>

<div class="card">
    <div class="card-body bg-primary text-white">
        <div class="row g-3">
            <?php
            $viewGrandDebit = 0;
            $viewGrandCredit = 0;
            foreach ($grouped as $g) {
                foreach ($g['accounts'] as $acc) {
                    $d = max(0, (float)$acc['total_debit'] - (float)$acc['total_credit']);
                    $c = max(0, (float)$acc['total_credit'] - (float)$acc['total_debit']);
                    $viewGrandDebit  += $d;
                    $viewGrandCredit += $c;
                }
            }
            ?>
            <div class="col-md-6">
                <h6 class="text-uppercase mb-1 opacity-75">Grand Total Debit</h6>
                <h3 class="fw-700 mb-0">NPR <?= number_format($viewGrandDebit, 2) ?></h3>
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="text-uppercase mb-1 opacity-75">Grand Total Credit</h6>
                <h3 class="fw-700 mb-0">NPR <?= number_format($viewGrandCredit, 2) ?></h3>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . 'views/layouts/app.php';
?>
