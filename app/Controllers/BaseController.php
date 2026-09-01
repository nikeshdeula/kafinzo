<?php

namespace App\Controllers;

class BaseController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        // Validate CSRF on all POST/DELETE/PATCH/PUT requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
                http_response_code(403);
                die('Invalid CSRF token.');
            }
        }
        if (file_exists(BASE_PATH . 'app/Core/helpers.php')) {
            require_once BASE_PATH . 'app/Core/helpers.php';
        }
        if (file_exists(BASE_PATH . 'app/Core/excel_export.php')) {
            require_once BASE_PATH . 'app/Core/excel_export.php';
        }
        if (file_exists(BASE_PATH . 'app/Core/nepali_date.php')) {
            require_once BASE_PATH . 'app/Core/nepali_date.php';
        }
    }

    protected function requireCsrf()
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('Invalid CSRF token.');
        }
    }

    protected function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
        if (!isset($_SESSION['business_id'])) {
            redirect('/login');
        }
    }

    protected function businessId(): int
    {
        $bid = (int)($_SESSION['business_id'] ?? 0);
        if ($bid === 0) {
            redirect('/login');
        }
        return $bid;
    }

    protected function currentUser()
    {
        return [
            'id'   => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? 'User',
        ];
    }

    protected function businessInfo(): array
    {
        static $cache = null;
        if ($cache !== null) return $cache;
        $db = \App\Core\Database::getInstance()->getConnection();
        $bid = $this->businessId();
        $s = $db->prepare("SELECT * FROM businesses WHERE id=:bid LIMIT 1");
        $s->execute(['bid' => $bid]);
        $cache = $s->fetch() ?: [];
        return $cache;
    }
}
