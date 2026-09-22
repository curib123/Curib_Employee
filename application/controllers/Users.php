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

    // This method displays a paginated list of users for management purposes. It retrieves the current page and per-page settings from GET parameters, calculates the total number of users, and fetches the corresponding user data from the User_model. The data is then passed to the 'users/index.php' view for rendering.
    public function index()
    {
        $per_page = min(50, max(5, (int) $this->input->get('per_page')));
        $page = max(1, (int) $this->input->get('page'));
        $total = $this->User_model->count_management_users();
        $total_pages = max(1, (int) ceil($total / $per_page));
        $page = min($page, $total_pages);

        $this->load->view('users/index.php', array(
            'current_user' => $this->current_user,
            'users' => $this->User_model->get_management_users_page((int) $this->session->userdata('user_id'), $page, $per_page),
            'user_total' => $total,
            'user_page' => $page,
            'user_per_page' => $per_page,
            'user_total_pages' => $total_pages,
            'flash' => $this->session->flashdata('flash')
        ));
    }

    // This method handles the deletion of a user account. It requires a POST request and checks if the user ID to be deleted is valid and not the currently signed-in user's ID. If the deletion is successful, it sets a success flash message; otherwise, it sets an error flash message. Finally, it redirects back to the users management page.
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

    //  This method displays the edit form for a specific user. It retrieves the user data based on the provided ID and checks if the user exists. If the user is found, it loads the 'users/edit.php' view with the current user data, the user to be edited, any flash messages, and validation errors (if any). If the user is not found, it sets an error flash message and redirects back to the users management page.
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

    // This method handles the update of a user's profile information. It requires a POST request and validates the input fields (first name, last name, birthday, address, and contact number). If validation fails, it sets validation errors in flash data and redirects back to the edit form. If validation passes, it updates the user's profile using the User_model and sets a success or error flash message based on the outcome. Finally, it redirects back to the users management page.
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
