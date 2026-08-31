<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\BusinessSetting;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class SettingsController extends BaseController {
    private BusinessSetting $bs;
    private User $userModel;
    private Role $roleModel;
    private Permission $permModel;

    public function __construct() {
        parent::__construct();
        $this->bs = new BusinessSetting();
        $this->userModel = new User();
        $this->roleModel = new Role();
        $this->permModel = new Permission();
    }

    public function business() {
        $this->requireAuth();
        $pageTitle = 'Business Settings';
        $pageDesc = 'Manage your business information and settings.';
        $module = 'settings';
        $activeTab = 'business';

        $db = Database::getInstance()->getConnection();
        $bid = $this->businessId();
        $s = $db->prepare("SELECT * FROM businesses WHERE id=:bid LIMIT 1");
        $s->execute(['bid' => $bid]);
        $business = $s->fetch() ?: [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'n' => trim($_POST['name'] ?? ''),
                'a' => trim($_POST['address'] ?? ''),
                'p' => trim($_POST['phone'] ?? ''),
                'e' => trim($_POST['email'] ?? ''),
                'pan' => trim($_POST['pan'] ?? ''),
                'vat' => trim($_POST['vat'] ?? ''),
            ];
            if ($business) {
                $s = $db->prepare("UPDATE businesses SET name=:n,address=:a,phone=:p,email=:e,pan_number=:pan,vat_number=:vat WHERE id=:id");
                $s->execute(array_merge($data, ['id' => $bid]));
            } else {
                $s = $db->prepare("INSERT INTO businesses (id,name,address,phone,email,pan_number,vat_number) VALUES (:id,:n,:a,:p,:e,:pan,:vat)");
                $s->execute(array_merge($data, ['id' => $bid]));
            }
            $_SESSION['success'] = 'Business settings updated successfully.';
            redirect('/settings/business');
        }

        return view('settings/business', compact('pageTitle', 'pageDesc', 'module', 'activeTab', 'business'));
    }

    public function users() {
        $this->requireAuth();
        $pageTitle = 'User Management';
        $pageDesc = 'Manage system users and roles.';
        $module = 'settings';
        $activeTab = 'users';
        $users = $this->userModel->all();
        $roles = $this->roleModel->all();
        return view('settings/users', compact('pageTitle', 'pageDesc', 'module', 'activeTab', 'users', 'roles'));
    }

    public function createUser() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            if (empty($name) || empty($email)) {
                $_SESSION['error'] = 'Name and email are required.';
            } elseif (empty($_POST['password'])) {
                $_SESSION['error'] = 'Password is required.';
            } elseif (strlen($_POST['password']) < 6) {
                $_SESSION['error'] = 'Password must be at least 6 characters.';
            } else {
                $this->userModel->create($_POST);
                $_SESSION['success'] = "User '{$name}' added successfully.";
            }
        }
        redirect('/settings/users');
    }

    public function editUser() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) { $_SESSION['error'] = 'Invalid user.'; redirect('/settings/users'); }
            $u = $this->userModel->find($id);
            if (!$u) { $_SESSION['error'] = 'User not found.'; redirect('/settings/users'); }
            $name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            if (empty($name) || empty($email)) {
                $_SESSION['error'] = 'Name and email are required.';
            } else {
                $this->userModel->update($id, $_POST);
                $_SESSION['success'] = 'User updated.';
            }
        }
        redirect('/settings/users');
    }

    public function deleteUser() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { $_SESSION['error'] = 'Invalid user.'; redirect('/settings/users'); }
        $this->userModel->delete($id);
        $_SESSION['success'] = 'User deleted.';
        redirect('/settings/users');
    }

    public function roles() {
        $this->requireAuth();
        $pageTitle = 'Roles & Permissions';
        $pageDesc = 'Manage roles and assign permissions.';
        $module = 'settings';
        $activeTab = 'roles';

        $allPermissions = $this->permModel->all();
        $roles = $this->roleModel->all();
        $rolePermissions = [];
        foreach ($roles as $r) {
            $rolePermissions[$r['id']] = $this->roleModel->permissions((int)$r['id']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $perms = array_map('intval', $_POST['permissions'] ?? []);
            if (empty($name)) { $_SESSION['error'] = 'Role name is required.'; redirect('/settings/roles'); }
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                $this->roleModel->update($id, $_POST);
                $this->roleModel->setPermissions($id, $perms);
                $_SESSION['success'] = 'Role updated.';
            } else {
                $rid = $this->roleModel->create($_POST);
                $this->roleModel->setPermissions($rid, $perms);
                $_SESSION['success'] = "Role '{$name}' created.";
            }
            redirect('/settings/roles');
        }

        return view('settings/roles', compact('pageTitle', 'pageDesc', 'module', 'activeTab', 'roles', 'allPermissions', 'rolePermissions'));
    }

    public function deleteRole() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { $_SESSION['error'] = 'Invalid role.'; redirect('/settings/roles'); }
        $this->roleModel->delete($id);
        $_SESSION['success'] = 'Role deleted.';
        redirect('/settings/roles');
    }

    public function tax() {
        $this->requireAuth();
        $pageTitle = 'Tax Settings';
        $pageDesc = 'Configure PAN, VAT, and tax rates.';
        $module = 'settings';
        $activeTab = 'tax';
        $bid = $this->businessId();

        $tax_name = $this->bs->get('tax_name', $bid, 'VAT');
        $tax_rate = $this->bs->get('tax_rate', $bid, '13');
        $pan_format = $this->bs->get('pan_format', $bid, 'XXXXXXXXX');
        $vat_format = $this->bs->get('vat_format', $bid, 'XXXXXXXXX');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->bs->set('tax_name', trim($_POST['tax_name'] ?? 'VAT'), $bid);
            $this->bs->set('tax_rate', trim($_POST['tax_rate'] ?? '13'), $bid);
            $this->bs->set('pan_format', trim($_POST['pan_format'] ?? 'XXXXXXXXX'), $bid);
            $this->bs->set('vat_format', trim($_POST['vat_format'] ?? 'XXXXXXXXX'), $bid);
            $_SESSION['success'] = 'Tax settings updated successfully.';
            redirect('/settings/tax');
        }

        return view('settings/tax', compact('pageTitle', 'pageDesc', 'module', 'activeTab', 'tax_name', 'tax_rate', 'pan_format', 'vat_format'));
    }
}
