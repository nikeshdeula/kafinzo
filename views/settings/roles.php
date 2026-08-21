<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-shield-fill-check text-primary me-2"></i>Roles & Permissions</h4>
        <p>Manage roles and assign permissions.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal" onclick="openRoleModal()">
        <i class="bi bi-plus-lg me-1"></i> Add Role
    </button>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card mb-4">
    <div class="card-body p-0">
        <?php if (empty($roles)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-shield fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No roles defined. Create one.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Role Name</th><th>Description</th><th>Permissions</th><th style="width:160px">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($roles as $r): ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['description'] ?? '—') ?></td>
                <td>
                    <?php
                    $perms = $rolePermissions[$r['id']] ?? [];
                    foreach ($perms as $p): ?>
                        <span class="badge bg-light text-dark border me-1 mb-1"><?= htmlspecialchars($p['name']) ?></span>
                    <?php endforeach; ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick='openRoleModal(<?= json_encode($r) ?>, <?= json_encode(array_column($perms, 'id')) ?>)'><i class="bi bi-pencil"></i></button>
                    <a href="/settings/roles/delete?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this role?')"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="roleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="roleForm" method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="roleModalLabel">Add Role</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="roleId">
            <div class="mb-3">
                <label class="form-label fw-600">Role Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" id="roleName" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Description</label>
                <textarea name="description" class="form-control" rows="2" id="roleDesc"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Permissions</label>
                <div class="row g-2">
                <?php foreach ($allPermissions as $p): ?>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $p['id'] ?>" id="perm_<?= $p['id'] ?>">
                        <label class="form-check-label" for="perm_<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></label>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openRoleModal(role, permIds) {
    var form = document.getElementById('roleForm');
    var label = document.getElementById('roleModalLabel');
    if (role && role.id) {
        label.textContent = 'Edit Role';
        document.getElementById('roleId').value = role.id;
        document.getElementById('roleName').value = role.name || '';
        document.getElementById('roleDesc').value = role.description || '';
        form.action = '/settings/roles';
        document.querySelectorAll('#roleForm input[name="permissions[]"]').forEach(function(cb) {
            cb.checked = permIds.includes(parseInt(cb.value));
        });
    } else {
        label.textContent = 'Add Role';
        form.reset();
        document.getElementById('roleId').value = '';
        form.action = '/settings/roles';
        document.querySelectorAll('#roleForm input[name="permissions[]"]').forEach(function(cb) { cb.checked = false; });
    }
}
</script>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
