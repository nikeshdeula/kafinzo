<?php

namespace App\Controllers;

class ApiController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function nepaliDate()
    {
        $this->requireAuth();
        header('Content-Type: application/json');
        $date = $_GET['date'] ?? date('Y-m-d');
        $bs = \ad_to_bs($date);
        if (!$bs) {
            echo json_encode(['success' => false, 'date' => $date]);
            return;
        }
        $formatted = \format_bs_date($bs, 'd M Y');
        echo json_encode(['success' => true, 'date' => $formatted]);
    }
}
