<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-diagram-3-fill text-primary me-2"></i>Chart of Accounts</h4>
        <p>Manage your accounting structure — Assets, Liabilities, Equity, Revenue, and Expenses.</p>
    </div>
    <a href="/accounting/chart-of-accounts/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Account
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
$typeLabels = [
    'asset'     => ['label' => 'Assets',      'icon' => 'bi-bank',              'color' => '#4361ee', 'bg' => '#eef0fd'],
    'liability' => ['label' => 'Liabilities', 'icon' => 'bi-exclamation-circle','color' => '#ef233c', 'bg' => '#fdedf0'],
    'equity'    => ['label' => 'Equity',       'icon' => 'bi-person-fill',       'color' => '#7209b7', 'bg' => '#f3e8fd'],
    'revenue'   => ['label' => 'Revenue',      'icon' => 'bi-graph-up-arrow',    'color' => '#06d6a0', 'bg' => '#e6fdf7'],
    'expense'   => ['label' => 'Expenses',     'icon' => 'bi-wallet2',           'color' => '#fb8500', 'bg' => '#fff3e6'],
];

foreach ($accounts as $type => $rows):
    if (empty($rows)) continue;
    $meta = $typeLabels[$type];
?>
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2 py-3" style="background:<?= $meta['bg'] ?>;border-bottom:2px solid <?= $meta['color'] ?>22;">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:<?= $meta['color'] ?>22;">
            <i class="bi <?= $meta['icon'] ?>" style="color:<?= $meta['color'] ?>;font-size:1.1rem;"></i>
        </div>
        <span class="fw-700" style="color:<?= $meta['color'] ?>;font-weight:700;font-size:1rem;"><?= $meta['label'] ?></span>
        <span class="badge ms-auto" style="background:<?= $meta['color'] ?>"><?= count($rows) ?> accounts</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:100px">Code</th>
                    <th>Account Name</th>
                    <th>Sub Type</th>
                    <th class="text-end">Opening Balance</th>
                    <th style="width:80px">Status</th>
                    <th style="width:80px">System</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $account): ?>
                <tr>
                    <td><code class="text-primary fw-bold"><?= htmlspecialchars($account['code']) ?></code></td>
                    <td>
                        <?php if ($account['sub_type'] === 'group'): ?>
                            <strong><?= htmlspecialchars($account['name']) ?></strong>
                        <?php else: ?>
                            <span class="ms-3"><?= htmlspecialchars($account['name']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><span class="text-muted small"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $account['sub_type'] ?? ''))) ?></span></td>
                    <td class="text-end">
                        <span class="fw-500">NPR <?= number_format($account['opening_balance'], 2) ?></span>
                    </td>
                    <td>
                        <?php if ($account['is_active']): ?>
                            <span class="badge bg-success-subtle text-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($account['is_system']): ?>
                            <span class="badge bg-info-subtle text-info" title="Cannot be deleted"><i class="bi bi-lock-fill"></i> System</span>
                        <?php else: ?>
                            <span class="text-muted small">Custom</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>

<?php
$content = ob_get_clean();
require BASE_PATH . 'views/layouts/app.php';
?>
