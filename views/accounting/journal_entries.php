<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-journal-text text-primary me-2"></i>Journal Entries</h4>
        <p>View and manage manual journal entries.</p>
    </div>
    <a href="/accounting/journal-entries/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Journal Entry
    </a>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($journals)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-journal-x fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No journal entries yet. <a href="/accounting/journal-entries/create">Create your first entry.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Journal #</th>
                    <th>Description</th>
                    <th>Reference</th>
                    <th class="text-end">Total Debit (NPR)</th>
                    <th class="text-end">Total Credit (NPR)</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($journals as $j): ?>
                <tr>
                    <td><?= nepali_date('d M Y', $j['entry_date']) ?></td>
                    <td><code class="fw-bold text-primary"><?= htmlspecialchars($j['journal_number']) ?></code></td>
                    <td><?= htmlspecialchars($j['description']) ?></td>
                    <td><?= htmlspecialchars($j['reference'] ?? '—') ?></td>
                    <td class="text-end fw-500">NPR <?= number_format($j['total_debit'], 2) ?></td>
                    <td class="text-end fw-500">NPR <?= number_format($j['total_credit'], 2) ?></td>
                    <td class="text-center">
                        <a href="/accounting/journal-entries/edit?id=<?= $j['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="/accounting/journal-entries/delete" style="display:inline" onsubmit="return confirm('Delete this journal entry?')">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="id" value="<?= $j['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . 'views/layouts/app.php';
?>
