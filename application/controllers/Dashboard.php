<?php
/**
 * application/controllers/Dashboard.php | 2026-09-21
 * Protected dashboard route for authenticated users and the one-time password prompt.
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
            'flash' => $this->session->flashdata('flash'),
            'validation_errors' => $this->session->flashdata('password_validation_errors') ?: array(),
            'show_first_login_password_prompt' => (bool) $this->session->userdata('user_password_prompt_pending')
        );

        $this->load->view('dashboard/index.php', $data);
    }
}
