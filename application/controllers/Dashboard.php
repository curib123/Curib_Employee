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
        $this->load->model('User_model');
    }
     
    // This method displays the main dashboard for authenticated users. It gathers various statistics and data related to employees and registered users, including counts, age statistics, address statistics, and monthly registration reports. The data is then passed to the 'dashboard/index.php' view for rendering.
    public function index()
    {
        $year_start = date('Y-01-01 00:00:00');
        $next_year_start = date('Y-01-01 00:00:00', strtotime('+1 year'));

        $data = array(
            'current_user' => $this->current_user,
            'employee_count' => $this->Employee_model->count_all(),
            'registered_user_count' => $this->User_model->count_registered(),
            'new_users_today' => $this->User_model->count_created_between(date('Y-m-d 00:00:00'), date('Y-m-d 00:00:00', strtotime('+1 day'))),
            'new_users_month' => $this->User_model->count_created_between(date('Y-m-01 00:00:00'), date('Y-m-01 00:00:00', strtotime('+1 month'))),
            'age_statistics' => $this->User_model->age_statistics(),
            'age_breakdown' => $this->User_model->age_breakdown(),
            'address_statistics' => $this->User_model->address_statistics(),
            'monthly_registration_statistics' => $this->User_model->get_monthly_registration_report($year_start, $next_year_start),
            'flash' => $this->session->flashdata('flash'),
            'validation_errors' => $this->session->flashdata('password_validation_errors') ?: array(),
            'show_first_login_password_prompt' => (bool) $this->session->userdata('user_password_prompt_pending')
        );

        $this->load->view('dashboard/index.php', $data);
    }
}
