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
        $user_search = trim((string) $this->input->get('search', TRUE));
        $user_age_range = (string) $this->input->get('age_range', TRUE);
        $user_sort = (string) $this->input->get('sort', TRUE);
        $user_direction = (string) $this->input->get('direction', TRUE);
        $total = $this->User_model->count_management_users($user_search, $user_age_range);
        $total_pages = max(1, (int) ceil($total / $per_page));
        $page = min($page, $total_pages);

        $this->load->view('users/index.php', array(
            'current_user' => $this->current_user,
            'users' => $this->User_model->get_management_users_page((int) $this->session->userdata('user_id'), $page, $per_page, $user_search, $user_age_range, $user_sort, $user_direction),
            'user_total' => $total,
            'user_page' => $page,
            'user_per_page' => $per_page,
            'user_total_pages' => $total_pages,
            'user_search' => $user_search,
            'user_age_range' => $user_age_range,
            'user_sort' => $user_sort,
            'user_direction' => strtoupper($user_direction) === 'DESC' ? 'DESC' : 'ASC',
            'flash' => $this->session->flashdata('flash')
        ));
    }

    public function store()
    {
        $this->require_post();

        $this->form_validation->set_rules('firstname', 'First name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('lastname', 'Last name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('birthday', 'Birthday', 'trim|required');
        $this->form_validation->set_rules('address', 'Address', 'trim|required|min_length[5]|max_length[255]');
        $this->form_validation->set_rules('contactno', 'Contact number', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('email', 'Email address', 'trim|required|valid_email|max_length[190]|is_unique[users.email]');

        if ($this->form_validation->run() === FALSE)
        {
            $this->session->set_flashdata('flash', array(
                'type' => 'danger',
                'message' => 'Please check the form and try again.'
            ));
            redirect('users');
            return;
        }

        $plain_password = $this->generate_password(16);
        $payload = array(
            'firstname' => trim((string) $this->input->post('firstname', TRUE)),
            'lastname' => trim((string) $this->input->post('lastname', TRUE)),
            'birthday' => trim((string) $this->input->post('birthday', TRUE)),
            'address' => trim((string) $this->input->post('address', TRUE)),
            'contactno' => trim((string) $this->input->post('contactno', TRUE)),
            'email' => strtolower(trim((string) $this->input->post('email', TRUE))),
            'password' => password_hash($plain_password, PASSWORD_DEFAULT),
            'must_change_password' => 1,
            'profile_picture' => ''
        );

        $user_id = $this->User_model->insert($payload);

        if (!$user_id)
        {
            $this->set_flash('danger', 'The user could not be created.');
            redirect('users');
            return;
        }

        $this->session->set_flashdata('flash', array(
            'type' => 'success',
            'message' => 'User created successfully. The temporary password is available in the user information view.',
            'temporary_password' => $plain_password,
            'user_id' => (int) $user_id
        ));
        redirect('users');
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

    public function reset_password($id)
    {
        $this->require_post();
        $id = (int) $id;
        $user = $this->User_model->find_by_id($id);

        if (!$user)
        {
            $this->set_flash('danger', 'User not found.');
            redirect('users');
            return;
        }

        if (!(int) $user['must_change_password'])
        {
            $this->set_flash('warning', 'This user has already changed the temporary password.');
            redirect('users');
            return;
        }

        $plain_password = $this->generate_password(16);

        if (!$this->User_model->update_password_hash($id, password_hash($plain_password, PASSWORD_DEFAULT)))
        {
            $this->set_flash('danger', 'The temporary password could not be regenerated.');
            redirect('users');
            return;
        }

        $this->session->set_flashdata('flash', array(
            'type' => 'success',
            'message' => 'A new temporary password was generated. It is available in the user information view.',
            'temporary_password' => $plain_password,
            'user_id' => $id
        ));
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
        $this->form_validation->set_rules('email', 'Email address', 'trim|required|valid_email|max_length[190]|callback_email_available[' . $id . ']');

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
            'contactno' => trim((string) $this->input->post('contactno', TRUE)),
            'email' => strtolower(trim((string) $this->input->post('email', TRUE)))
        ));

        $this->set_flash($updated ? 'success' : 'danger', $updated ? 'User updated successfully.' : 'The user could not be updated.');
        redirect('users');
    }

    public function email_available($email, $user_id)
    {
        $user = $this->User_model->find_by_email($email);

        if (!$user)
        {
            return TRUE;
        }

        return (int) $user['Id'] === (int) $user_id;
    }

    private function generate_password($length)
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%*?';
        $password = '';
        $max = strlen($alphabet) - 1;

        do {
            $password = '';
            for ($i = 0; $i < $length; $i++)
            {
                $password .= $alphabet[random_int(0, $max)];
            }
        }
        while (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[!@#$%*?]/', $password));

        return $password;
    }
}
