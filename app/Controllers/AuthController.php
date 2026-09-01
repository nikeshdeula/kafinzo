<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class AuthController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function loginForm()
    {
        if (isset($_SESSION['user_id'])) {
            redirect('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please enter both email and password.';
            redirect('/login');
        }

        // Rate limiting: max 5 attempts per minute per email
        $rateKey = 'login_attempts_' . md5($email);
        $attempts = $_SESSION[$rateKey] ?? ['count' => 0, 'first' => time()];
        if (time() - $attempts['first'] > 60) {
            $attempts = ['count' => 0, 'first' => time()];
        }
        if ($attempts['count'] >= 5) {
            $_SESSION['error'] = 'Too many login attempts. Please wait a minute.';
            redirect('/login');
        }
        $attempts['count']++;
        $_SESSION[$rateKey] = $attempts;

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                $_SESSION['error'] = 'Account is not active.';
                redirect('/login');
            }

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];

            // Get user's business_id
            $stmt = $db->prepare('SELECT business_id FROM business_users WHERE user_id = :uid LIMIT 1');
            $stmt->execute(['uid' => $user['id']]);
            $bu = $stmt->fetch();
            if (!$bu) {
                $_SESSION['error'] = 'No business linked to this account.';
                redirect('/login');
            }
            $_SESSION['business_id'] = (int)$bu['business_id'];

            redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Invalid credentials.';
            redirect('/login');
        }
    }

    public function registerForm()
    {
        if (isset($_SESSION['user_id'])) {
            redirect('/dashboard');
        }
        return view('auth/register');
    }

    public function register()
    {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mobileNumber = trim($_POST['mobile_number'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($fullName) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please fill all required fields.';
            redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email address.';
            redirect('/register');
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Password must be at least 8 characters long.';
            redirect('/register');
        }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $_SESSION['error'] = 'Password must contain uppercase, lowercase, and a number.';
            redirect('/register');
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match.';
            redirect('/register');
        }

        $db = Database::getInstance()->getConnection();
        
        // Check if email exists
        $stmt = $db->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Email already exists.';
            redirect('/register');
        }

        $db->beginTransaction();

        try {
            // Insert new user
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare('INSERT INTO users (full_name, email, mobile_number, password_hash) VALUES (:name, :email, :mobile, :pass)');
            $stmt->execute([
                'name' => $fullName,
                'email' => $email,
                'mobile' => $mobileNumber,
                'pass' => $passwordHash
            ]);
            $userId = (int)$db->lastInsertId();

            // Create a business for this user
            $stmt = $db->prepare('INSERT INTO businesses (name) VALUES (:name)');
            $stmt->execute(['name' => $fullName . "'s Business"]);
            $businessId = (int)$db->lastInsertId();

            // Link user to business
            $stmt = $db->prepare('INSERT INTO business_users (business_id, user_id) VALUES (:bid, :uid)');
            $stmt->execute(['bid' => $businessId, 'uid' => $userId]);

            // Create default business settings
            $stmt = $db->prepare('INSERT INTO business_settings (business_id, setting_key, setting_value) VALUES (:bid, :key, :val)');
            $stmt->execute(['bid' => $businessId, 'key' => 'tax_rate', 'val' => '13']);

            // Create default expense categories
            $defaultCategories = ['Rent','Salary','Electricity','Internet','Transportation','Marketing','Office Supplies','Maintenance','Travel','Bank Charges','Other'];
            $stmt = $db->prepare('INSERT INTO expense_categories (business_id, name) VALUES (:bid, :name)');
            foreach ($defaultCategories as $cat) {
                $stmt->execute(['bid' => $businessId, 'name' => $cat]);
            }

            // Create default bank account
            $stmt = $db->prepare('INSERT INTO bank_accounts (business_id, account_type, account_name, opening_balance, current_balance, is_default) VALUES (:bid, :type, :name, 0, 0, 1)');
            $stmt->execute(['bid' => $businessId, 'type' => 'cash', 'name' => 'Cash in Hand']);

            // Create default warehouse
            $stmt = $db->prepare('INSERT INTO warehouses (business_id, name, location, is_default) VALUES (:bid, :name, :loc, 1)');
            $stmt->execute(['bid' => $businessId, 'name' => 'Main Warehouse', 'loc' => 'Head Office']);

            // Create default units
            $units = [['Piece','pcs'],['Kilogram','kg'],['Gram','g'],['Litre','ltr'],['Metre','m'],['Box','box'],['Dozen','dz'],['Set','set'],['Hour','hr']];
            $stmt = $db->prepare('INSERT INTO units (business_id, name, abbreviation) VALUES (:bid, :name, :abbr)');
            foreach ($units as $u) {
                $stmt->execute(['bid' => $businessId, 'name' => $u[0], 'abbr' => $u[1]]);
            }

            // Create default product categories
            $pCats = ['General','Electronics','Clothing','Food & Beverage','Stationery'];
            $stmt = $db->prepare('INSERT INTO product_categories (business_id, name) VALUES (:bid, :name)');
            foreach ($pCats as $cat) {
                $stmt->execute(['bid' => $businessId, 'name' => $cat]);
            }

            $db->commit();

            $_SESSION['success'] = 'Registration successful. Please login.';
            redirect('/login');
        } catch (\PDOException $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Registration failed. Please try again.';
            redirect('/register');
        }
    }

    public function logout()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        redirect('/login');
    }
}
