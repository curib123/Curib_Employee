<?php
/**
 * application/controllers/Dashboard.php | 2026-09-21
 * Protected dashboard route for authenticated users.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Employee_model');
    }

    public function index()
    {
        $data = array(
            'current_user' => $this->current_user,
            'employee_count' => $this->Employee_model->count_all(),
            'flash' => $this->session->flashdata('flash')
        );

        $this->load->view('dashboard/index.php', $data);
    }
}
