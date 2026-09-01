<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function index()
    {
        return view('dashboard/index', [
            'title' => 'Dashboard',
            'userName' => $_SESSION['user_name'] ?? 'User'
        ]);
    }
}
