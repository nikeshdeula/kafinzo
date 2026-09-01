<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-file-earmark-text text-primary me-2"></i>Quotations</h4>
        <p>Create and manage quotations for customers.</p>
    </div>
    <a href="/sales/quotations/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New Quotation</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($quotations)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-file-earmark-text fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No quotations yet. <a href="/sales/quotations/create">Create your first quotation.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Quotation #</th><th>Customer</th><th>Branch</th><th>Address</th><th>Date</th><th>Valid Until</th>
                <th class="text-end">Total</th><th>Status</th><th style="width:220px">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($quotations as $q): ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($q['quotation_number']) ?></td>
                <td><?= htmlspecialchars($q['customer_name']) ?></td>
                <td><?= htmlspecialchars($q['customer_branch'] ?? '—') ?></td>
                <td><?= htmlspecialchars($q['customer_address'] ?? '—') ?></td>
                <td><?= htmlspecialchars($q['quotation_date']) ?></td>
                <td><?= htmlspecialchars($q['valid_until'] ?? '—') ?></td>
                <td class="text-end">NPR <?= number_format($q['total_amount'], 2) ?></td>
                <td>
                    <?php
                    $status = $q['status'];
                    $badgeClass = 'bg-secondary-subtle text-secondary';
                    if ($status === 'accepted') $badgeClass = 'bg-success-subtle text-success';
                    elseif ($status === 'sent') $badgeClass = 'bg-info-subtle text-info';
                    elseif ($status === 'rejected') $badgeClass = 'bg-danger-subtle text-danger';
                    elseif ($status === 'expired') $badgeClass = 'bg-warning-subtle text-warning';
                    elseif ($status === 'converted') $badgeClass = 'bg-primary-subtle text-primary';
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/sales/quotations/edit?id=<?= $q['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <a href="/sales/quotations/convert?id=<?= $q['id'] ?>" class="btn btn-sm btn-outline-success" title="Convert to Invoice" onclick="return confirm('Convert this quotation to an invoice?')"><i class="bi bi-receipt"></i></a>
                        <form method="POST" action="/sales/quotations/delete" style="display:inline" onsubmit="return confirm('Delete this quotation?')">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="id" value="<?= $q['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
