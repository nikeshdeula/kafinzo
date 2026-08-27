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
        return (int)($_SESSION['business_id'] ?? 1);
    }

    protected function currentUser()
    {
        return [
            'id'   => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? 'User',
        ];
    }
}
