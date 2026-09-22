<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index()
    {
        $this->load->view('users/index.php', array(
            'current_user' => $this->current_user,
            'users' => $this->User_model->get_management_users(),
            'flash' => $this->session->flashdata('flash')
        ));
    }

    public function delete($id)
    {
        $this->require_post();
        $id = (int) $id;
        if ($id === (int) $this->session->userdata('user_id'))
        {
            $this->set_flash('danger', 'You cannot delete your own signed-in account.');
            redirect('users');
            return;
        }
        if ($id < 1 || !$this->User_model->find_by_id($id) || !$this->User_model->delete_user($id))
        {
            $this->set_flash('danger', 'The user could not be deleted.');
        }
        else
        {
            $this->set_flash('success', 'User deleted successfully.');
        }
        redirect('users');
    }
}
