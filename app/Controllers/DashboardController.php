<?php

namespace App\Controllers;

class DashboardController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
        
        return view('dashboard/index', [
            'title' => 'Dashboard',
            'userName' => $_SESSION['user_name'] ?? 'User'
        ]);
    }
}
