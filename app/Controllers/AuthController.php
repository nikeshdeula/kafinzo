<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class AuthController
{
    public function loginForm()
    {
        if (isset($_SESSION['user_id'])) {
            redirect('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please enter both email and password.';
            redirect('/login');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                $_SESSION['error'] = 'Account is not active.';
                redirect('/login');
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            
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

        // Insert new user
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT INTO users (full_name, email, mobile_number, password_hash) VALUES (:name, :email, :mobile, :pass)');
        
        try {
            $stmt->execute([
                'name' => $fullName,
                'email' => $email,
                'mobile' => $mobileNumber,
                'pass' => $passwordHash
            ]);
            $_SESSION['success'] = 'Registration successful. Please login.';
            redirect('/login');
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            redirect('/register');
        }
    }

    public function logout()
    {
        session_destroy();
        redirect('/login');
    }
}
