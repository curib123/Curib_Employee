<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('users/index.php', array(
            'current_user' => $this->current_user,
            'users' => $this->User_model->get_management_users((int) $this->session->userdata('user_id')),
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

    public function edit($id)
    {
        $user = $this->User_model->find_by_id((int) $id);

        if (!$user)
        {
            $this->set_flash('danger', 'User not found.');
            redirect('users');
            return;
        }

        $this->load->view('users/edit.php', array(
            'current_user' => $this->current_user,
            'user' => $user,
            'flash' => $this->session->flashdata('flash'),
            'validation_errors' => $this->session->flashdata('user_validation_errors') ?: array()
        ));
    }

    public function update($id)
    {
        $this->require_post();
        $id = (int) $id;

        if (!$this->User_model->find_by_id($id))
        {
            $this->set_flash('danger', 'User not found.');
            redirect('users');
            return;
        }

        $this->form_validation->set_rules('firstname', 'First name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('lastname', 'Last name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('birthday', 'Birthday', 'trim|required');
        $this->form_validation->set_rules('address', 'Address', 'trim|required|min_length[5]|max_length[255]');
        $this->form_validation->set_rules('contactno', 'Contact number', 'trim|required|max_length[20]');

        if ($this->form_validation->run() === FALSE)
        {
            $this->session->set_flashdata('user_validation_errors', $this->form_validation->error_array());
            redirect('users/edit/' . $id);
            return;
        }

        $updated = $this->User_model->update_profile($id, array(
            'firstname' => trim((string) $this->input->post('firstname', TRUE)),
            'lastname' => trim((string) $this->input->post('lastname', TRUE)),
            'birthday' => trim((string) $this->input->post('birthday', TRUE)),
            'address' => trim((string) $this->input->post('address', TRUE)),
            'contactno' => trim((string) $this->input->post('contactno', TRUE))
        ));

        $this->set_flash($updated ? 'success' : 'danger', $updated ? 'User updated successfully.' : 'The user could not be updated.');
        redirect('users');
    }
}
