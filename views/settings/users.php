<?php ob_start(); ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-people-fill text-primary me-2"></i>User Management</h4>
        <p>Manage system users and roles.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openUserModal()">
        <i class="bi bi-plus-lg me-1"></i> Add User
    </button>
</div>

<?php include BASE_PATH . 'views/layouts/alerts.php'; ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($users)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-people fs-1 mb-3 d-block" style="opacity:.3"></i>
            <p class="fw-500">No users found. <a href="#" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openUserModal()">Add your first user.</a></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Name</th><th>Email</th><th>Mobile</th><th>Role</th><th>Status</th><th style="width:120px">Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($u['full_name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['mobile_number'] ?? '—') ?></td>
                <td><?= htmlspecialchars($u['role_names'] ?? '—') ?></td>
                <td>
                    <?php if ($u['status']==='active'): ?>
                    <span class="badge bg-success-subtle text-success">Active</span>
                    <?php elseif ($u['status']==='suspended'): ?>
                    <span class="badge bg-danger-subtle text-danger">Suspended</span>
                    <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick='openUserModal(<?= json_encode($u) ?>)'><i class="bi bi-pencil"></i></button>
                    <a href="/settings/users/delete?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?')"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="userForm" method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalLabel">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="userId">
            <div class="mb-3">
                <label class="form-label fw-600">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" id="userName" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" id="userEmail" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Mobile</label>
                <input type="text" name="mobile_number" class="form-control" id="userMobile">
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Role</label>
                <select name="role_id" class="form-select" id="userRole">
                    <option value="">— Select Role —</option>
                    <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600" id="passLabel">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" id="userPassword">
                <div class="form-text" id="passHelp">Leave blank to keep current password when editing.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Status</label>
                <select name="status" class="form-select" id="userStatus">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>
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
function openUserModal(u) {
    var form = document.getElementById('userForm');
    var label = document.getElementById('userModalLabel');
    var passHelp = document.getElementById('passHelp');
    var passLabel = document.getElementById('passLabel');
    var passReq = document.getElementById('userPassword');
    if (u && u.id) {
        label.textContent = 'Edit User';
        document.getElementById('userId').value = u.id;
        document.getElementById('userName').value = u.full_name || '';
        document.getElementById('userEmail').value = u.email || '';
        document.getElementById('userMobile').value = u.mobile_number || '';
        document.getElementById('userRole').value = (u.role_ids ? u.role_ids.split(',')[0] : '');
        document.getElementById('userPassword').value = '';
        document.getElementById('userStatus').value = u.status || 'active';
        form.action = '/settings/users/edit';
        passHelp.style.display = '';
        passLabel.innerHTML = 'Password';
        passReq.required = false;
    } else {
        label.textContent = 'Add User';
        form.reset();
        document.getElementById('userId').value = '';
        form.action = '/settings/users/create';
        passHelp.style.display = 'none';
        passLabel.innerHTML = 'Password <span class="text-danger">*</span>';
        passReq.required = true;
    }
}
</script>

<?php $content = ob_get_clean(); require BASE_PATH . 'views/layouts/app.php'; ?>
