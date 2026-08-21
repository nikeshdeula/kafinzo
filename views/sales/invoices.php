<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-receipt text-primary me-2"></i>Invoices</h4>
        <p>Create and manage sales invoices for your customers.</p>
    </div>
    <a href="/sales/invoices/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New Invoice</a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($invoices)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-receipt fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No invoices yet. <a href="/sales/invoices/create">Create your first invoice.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Invoice #</th><th>Customer</th><th>Date</th><th>Due Date</th>
                <th class="text-end">Total</th><th class="text-end">Paid</th>
                <th>Status</th><th style="width:180px">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($invoices as $inv): ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($inv['invoice_number']) ?></td>
                <td><?= htmlspecialchars($inv['customer_name']) ?></td>
                <td><?= htmlspecialchars($inv['invoice_date']) ?></td>
                <td><?= htmlspecialchars($inv['due_date'] ?? '—') ?></td>
                <td class="text-end">NPR <?= number_format($inv['total_amount'], 2) ?></td>
                <td class="text-end">NPR <?= number_format($inv['paid_amount'], 2) ?></td>
                <td>
                    <?php
                    $status = $inv['status'];
                    $badgeClass = 'bg-secondary-subtle text-secondary';
                    if ($status === 'paid') $badgeClass = 'bg-success-subtle text-success';
                    elseif ($status === 'unpaid') $badgeClass = 'bg-warning-subtle text-warning';
                    elseif ($status === 'partial') $badgeClass = 'bg-info-subtle text-info';
                    elseif ($status === 'overdue') $badgeClass = 'bg-danger-subtle text-danger';
                    elseif ($status === 'draft') $badgeClass = 'bg-secondary-subtle text-secondary';
                    elseif ($status === 'cancelled') $badgeClass = 'bg-dark-subtle text-dark';
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/sales/invoices/edit?id=<?= $inv['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <a href="/sales/invoices/delete?id=<?= $inv['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this invoice?')"><i class="bi bi-trash"></i></a>
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
