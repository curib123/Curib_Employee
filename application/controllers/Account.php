<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

    // This method displays the account management page with the current user's information and any flash messages.
    public function index()
    {
        $data = array(
            'current_user' => $this->current_user,
            'flash' => $this->session->flashdata('flash'),
            'validation_errors' => $this->session->flashdata('account_validation_errors') ?: array()
        );
        $this->load->view('account/index.php', $data);
    }

    // This method handles the profile update form submission.
    public function update_profile()
    {
        $this->require_post();
        $this->set_profile_rules();

        if ($this->form_validation->run() === FALSE)
        {
            $this->session->set_flashdata('account_validation_errors', $this->form_validation->error_array());
            redirect('account');
            return;
        }

        $user_id = (int) $this->session->userdata('user_id');
        $data = array(
            'firstname' => trim((string) $this->input->post('firstname', TRUE)),
            'lastname' => trim((string) $this->input->post('lastname', TRUE)),
            'birthday' => trim((string) $this->input->post('birthday', TRUE)),
            'address' => trim((string) $this->input->post('address', TRUE)),
            'contactno' => trim((string) $this->input->post('contactno', TRUE))
        );

        if (!$this->User_model->update_profile($user_id, $data))
        {
            $this->set_flash('danger', 'Your account could not be updated.');
            redirect('account');
            return;
        }

        $user = $this->User_model->find_by_id($user_id);
        $this->refresh_session($user);
        $this->set_flash('success', 'Account information updated successfully.');
        redirect('account');
    }

    // This method handles the password change form submission.
    public function change_password()
    {
        $this->require_post();
        $this->form_validation->set_rules('current_password', 'Current password', 'required|max_length[72]');
        $this->form_validation->set_rules('new_password', 'New password', 'required|min_length[12]|max_length[72]|callback_strong_password');
        $this->form_validation->set_rules('confirm_password', 'Confirm password', 'required|matches[new_password]');

        if ($this->form_validation->run() === FALSE)
        {
            $this->session->set_flashdata('account_validation_errors', $this->form_validation->error_array());
            redirect('account');
            return;
        }

        $user_id = (int) $this->session->userdata('user_id');
        $user = $this->User_model->find_by_id($user_id);
        $current_password = (string) $this->input->post('current_password', FALSE);
        $new_password = (string) $this->input->post('new_password', FALSE);

        if (!$user || !password_verify($current_password, $user['password']))
        {
            $this->session->set_flashdata('account_validation_errors', array('current_password' => 'Your current password is incorrect.'));
            redirect('account');
            return;
        }

        if (password_verify($new_password, $user['password']))
        {
            $this->session->set_flashdata('account_validation_errors', array('new_password' => 'Your new password must be different from your current password.'));
            redirect('account');
            return;
        }

        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        if ($hash === FALSE || !$this->User_model->update_password_and_clear_prompt($user_id, $hash))
        {
            $this->set_flash('danger', 'Your password could not be changed.');
            redirect('account');
            return;
        }

        $this->session->set_userdata('user_password_prompt_pending', FALSE);
        $this->session->sess_regenerate(TRUE);
        $this->set_flash('success', 'Password updated successfully.');
        redirect('account');
    }

    // This method handles the profile picture upload form submission.
    public function upload_picture()
    {
        $this->require_post();
        $upload_path = FCPATH . 'uploads/profile/';

        if (!is_dir($upload_path) && !mkdir($upload_path, 0750, TRUE))
        {
            $this->set_flash('danger', 'The profile picture folder is not writable.');
            redirect('account');
            return;
        }

        if (!isset($_FILES['profile_picture']) || !$this->is_valid_profile_image($_FILES['profile_picture']))
        {
            $this->set_flash('danger', isset($_FILES['profile_picture']) ? $this->profile_picture_error($_FILES['profile_picture']) : 'Choose a profile picture before uploading.');
            redirect('account');
            return;
        }

        $this->load->library('upload', array(
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size' => 2048,
            'max_width' => 2000,
            'max_height' => 2000,
            'detect_mime' => TRUE,
            'encrypt_name' => TRUE,
            'remove_spaces' => TRUE
        ));

        if (!$this->upload->do_upload('profile_picture'))
        {
            $this->set_flash('danger', 'The profile picture could not be saved. Please try again with a JPG, PNG, or WebP image up to 2 MB.');
            redirect('account');
            return;
        }

        $file = $this->upload->data();
        $path = 'uploads/profile/' . $file['file_name'];
        $user_id = (int) $this->session->userdata('user_id');
        $old_path = (string) $this->session->userdata('user_profile_picture');

        if (!$this->User_model->update_profile($user_id, array('profile_picture' => $path)))
        {
            @unlink(FCPATH . $path);
            $this->set_flash('danger', 'The profile picture could not be saved.');
            redirect('account');
            return;
        }

        if ($old_path && strpos($old_path, 'uploads/profile/') === 0)
        {
            @unlink(FCPATH . $old_path);
        }

        $this->session->set_userdata('user_profile_picture', $path);
        $this->set_flash('success', 'Profile picture updated successfully.');
        redirect('account');
    }

    // This method handles the profile picture removal form submission.
    private function is_valid_profile_image($file)
    {
        if ((int) $file['error'] !== UPLOAD_ERR_OK || (int) $file['size'] > 2097152)
        {
            return FALSE;
        }

        $image = @getimagesize($file['tmp_name']);
        $allowed_mimes = array('image/jpeg', 'image/png', 'image/webp');

        return $image !== FALSE
            && isset($image['mime'])
            && in_array($image['mime'], $allowed_mimes, TRUE)
            && (int) $image[0] <= 2000
            && (int) $image[1] <= 2000;
    }

    // This method returns a user-friendly error message based on the file upload error code.
    private function profile_picture_error($file)
    {
        $messages = array(
            UPLOAD_ERR_INI_SIZE => 'The profile picture is larger than the server upload limit.',
            UPLOAD_ERR_FORM_SIZE => 'The profile picture is larger than the allowed 2 MB limit.',
            UPLOAD_ERR_PARTIAL => 'The profile picture upload was interrupted. Please try again.',
            UPLOAD_ERR_NO_TMP_DIR => 'The server could not prepare the upload. Please contact support.',
            UPLOAD_ERR_CANT_WRITE => 'The server could not save the upload. Please contact support.',
            UPLOAD_ERR_EXTENSION => 'The server blocked the profile picture upload. Please try again.'
        );
        $error = isset($file['error']) ? (int) $file['error'] : UPLOAD_ERR_NO_FILE;
        return isset($messages[$error]) ? $messages[$error] : 'Use a valid JPG, PNG, or WebP image up to 2 MB and 2000 x 2000 pixels.';
    }

   
    // This method validates names to ensure they contain only allowed characters.
    public function valid_name($name)
    {
        if (!preg_match("/^[\\p{L}\\p{M}][\\p{L}\\p{M} .'-]{0,99}$/u", trim((string) $name)))
        {
            $this->form_validation->set_message('valid_name', 'The {field} field contains unsupported characters.');
            return FALSE;
        }
        return TRUE;
    }

    // This method validates birthdays to ensure they are valid dates not in the future.
    public function valid_birthday($birthday)
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', trim((string) $birthday));
        $errors = DateTimeImmutable::getLastErrors();
        if (!$date || ($errors !== FALSE && ($errors['warning_count'] || $errors['error_count'])) || $date > new DateTimeImmutable('today'))
        {
            $this->form_validation->set_message('valid_birthday', 'Enter a valid birthday that is not in the future.');
            return FALSE;
        }
        return TRUE;
    }

    // This method validates passwords to ensure they meet strength requirements.
    public function strong_password($password)
    {
        if (preg_match('/\\s/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password))
        {
            $this->form_validation->set_message('strong_password', 'Use uppercase, lowercase, a number, a symbol, and no spaces.');
            return FALSE;
        }
        return TRUE;
    }

    // This method sets the validation rules for profile updates.
    private function set_profile_rules()
    {
        $this->form_validation->set_rules('firstname', 'First name', 'trim|required|max_length[100]|callback_valid_name');
        $this->form_validation->set_rules('lastname', 'Last name', 'trim|required|max_length[100]|callback_valid_name');
        $this->form_validation->set_rules('birthday', 'Birthday', 'trim|required|callback_valid_birthday');
        $this->form_validation->set_rules('address', 'Address', 'trim|required|min_length[5]|max_length[255]');
        $this->form_validation->set_rules('contactno', 'Contact number', 'trim|required|max_length[20]');
    }

     // This method refreshes the session data with the updated user information.
    private function refresh_session($user)
    {
        $age = (new DateTimeImmutable($user['birthday']))->diff(new DateTimeImmutable('today'))->y;
        $this->session->set_userdata(array(
            'user_firstname' => $user['firstname'], 'user_lastname' => $user['lastname'],
            'user_address' => $user['address'], 'user_contactno' => $user['contactno'],
            'user_birthday' => $user['birthday'], 'user_age' => (string) $age,
            'user_profile_picture' => isset($user['profile_picture']) ? $user['profile_picture'] : ''
        ));
    }
}
