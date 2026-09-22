<?php
/**
 * application/controllers/Auth.php | 2026-09-21
 * Registration, login, generated-password handoff, first-login password flow, and logout.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    private $max_login_attempts = 10;
    private $login_window_seconds = 900;
    private $login_lock_seconds = 300;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Auth_attempt_model');
        $this->load->library('form_validation');

        $this->output
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
            ->set_header('Pragma: no-cache');
    }

    // This method displays the login form to the user.
    public function login()
    {
        if ($this->session->userdata('logged_in'))
        {
            redirect('dashboard');
            return;
        }

        $data = array(
            'flash' => $this->session->flashdata('flash'),
            'old_email' => $this->session->flashdata('old_email') ?: ''
        );

        $this->load->view('auth/login.php', $data);
    }

    // This method processes the login form submission, handling validation, authentication, and session management.
    public function login_submit()
    {
        $this->require_post();

        if ($this->session->userdata('logged_in'))
        {
            redirect('dashboard');
            return;
        }

        $this->form_validation->set_rules(
            'email',
            'Email',
            'trim|required|valid_email|max_length[190]'
        );
        $this->form_validation->set_rules(
            'password',
            'Password',
            'required|max_length[72]'
        );

        $email = strtolower(trim((string) $this->input->post('email', TRUE)));
        $password = (string) $this->input->post('password', FALSE);

        if ($this->form_validation->run() === FALSE)
        {
            $this->session->set_flashdata('old_email', $email);
            $this->set_flash('danger', 'Enter a valid email address and password.');
            redirect('login');
            return;
        }

        $identifier_hash = hash('sha256', $email);
        $ip_address = (string) $this->input->ip_address();
        $now = time();

        $this->Auth_attempt_model->cleanup_stale($now - 86400);
        $attempt_state = $this->Auth_attempt_model->get_state($identifier_hash, $ip_address);

        if ($attempt_state && (int) $attempt_state['locked_until'] > $now)
        {
            $remaining_seconds = (int) $attempt_state['locked_until'] - $now;
            $remaining_minutes = max(1, (int) ceil($remaining_seconds / 60));

            $this->session->set_flashdata('old_email', $email);
            $this->set_flash(
                'warning',
                'Too many failed sign-in attempts. Try again in about ' . $remaining_minutes . ' minute' .
                ($remaining_minutes === 1 ? '.' : 's.')
            );
            redirect('login');
            return;
        }

        $user = $this->User_model->find_by_email($email);

        if (!$user || !password_verify($password, $user['password']))
        {
            $this->Auth_attempt_model->record_failure(
                $identifier_hash,
                $ip_address,
                $this->max_login_attempts,
                $this->login_window_seconds,
                $this->login_lock_seconds
            );

            $this->session->set_flashdata('old_email', $email);
            $this->set_flash('danger', 'The email or password is incorrect.');
            redirect('login');
            return;
        }

        $this->Auth_attempt_model->clear($identifier_hash, $ip_address);

        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT))
        {
            $rehash = password_hash($password, PASSWORD_DEFAULT);

            if ($rehash !== FALSE)
            {
                $this->User_model->update_password_hash((int) $user['Id'], $rehash);
            }
        }

       

        $this->start_authenticated_session($user);

        $return_to = $this->normalize_redirect_target($this->session->userdata('redirect_after_login'));
        $this->session->unset_userdata('redirect_after_login');

        if ((int) $user['must_change_password'] !== 1)
        {
            $this->set_flash('success', 'Welcome back, ' . $user['firstname'] . '.' );
        }

        if ($return_to !== '')
        {
            redirect($return_to);
            return;
        }

        redirect('dashboard');
    }

    // This method processes the logout request, destroying the session and redirecting to the login page.
    public function register()
    {
        if ($this->session->userdata('logged_in'))
        {
            redirect('dashboard');
            return;
        }

        $data = array(
            'flash' => $this->session->flashdata('flash'),
            'validation_errors' => $this->session->flashdata('validation_errors') ?: array(),
            'old_input' => $this->session->flashdata('old_input') ?: array(),
            'today' => date('Y-m-d')
        );

        $this->load->view('auth/register.php', $data);
    }

    // This method handles the registration form submission, including validation, profile picture upload, password generation, and user creation.
    public function register_submit()
    {
        $this->require_post();

        if ($this->session->userdata('logged_in'))
        {
            redirect('dashboard');
            return;
        }

        $this->set_registration_rules();
        $payload = $this->registration_payload();

        if ($this->form_validation->run() === FALSE)
        {
            $this->session->set_flashdata('validation_errors', $this->form_validation->error_array());
            $this->session->set_flashdata('old_input', $payload);
            redirect('register');
            return;
        }

        $profile_picture = $this->upload_registration_picture();

        if ($profile_picture === FALSE)
        {
            $this->session->set_flashdata('old_input', $payload);
            redirect('register');
            return;
        }

        $plain_password = $this->generate_password(16);
        $password_hash = password_hash($plain_password, PASSWORD_DEFAULT);

        if ($password_hash === FALSE)
        {
            if ($profile_picture)
            {
                @unlink(FCPATH . $profile_picture);
            }
            $this->session->set_flashdata('old_input', $payload);
            $this->set_flash('danger', 'A secure password could not be generated. Please try again.');
            redirect('register');
            return;
        }

        $payload['password'] = $password_hash;
        $payload['must_change_password'] = 1;
        $payload['profile_picture'] = $profile_picture;

        if (!$this->User_model->insert($payload))
        {
            if ($profile_picture)
            {
                @unlink(FCPATH . $profile_picture);
            }
            unset($payload['password'], $payload['must_change_password'], $payload['profile_picture']);
            $this->session->set_flashdata('old_input', $payload);
            $this->set_flash(
                'danger',
                'Registration could not be completed. Check your information and try again.'
            );
            redirect('register');
            return;
        }

        /*
         * Only the email is carried to the next request. The plaintext generated
         * password is rendered directly and never written to ci_sessions.
         */
        $this->session->set_flashdata('old_email', $payload['email']);

        $this->load->view('auth/registration_password.php', array(
            'generated_password' => $plain_password
        ));
    }

   
   // This method generates a secure random password of the specified length, ensuring it contains uppercase, lowercase, numeric, and special characters.
    public function change_password()
    {
        $this->require_post();
        $this->require_authenticated_user();

        if (!$this->session->userdata('user_password_prompt_pending'))
        {
            redirect('dashboard');
            return;
        }

        $this->form_validation->set_rules(
            'current_password',
            'Current password',
            'required|max_length[72]'
        );
        $this->form_validation->set_rules(
            'new_password',
            'New password',
            'required|min_length[12]|max_length[72]|callback_strong_password'
        );
        $this->form_validation->set_rules(
            'confirm_password',
            'Confirm password',
            'required|matches[new_password]'
        );

        if ($this->form_validation->run() === FALSE)
        {
            $this->session->set_flashdata(
                'password_validation_errors',
                $this->form_validation->error_array()
            );
            redirect('dashboard');
            return;
        }

        $user_id = (int) $this->session->userdata('user_id');
        $user = $this->User_model->find_by_id($user_id);
        $current_password = (string) $this->input->post('current_password', FALSE);
        $new_password = (string) $this->input->post('new_password', FALSE);

        if (!$user)
        {
            $this->session->sess_destroy();
            redirect('login');
            return;
        }

        if (!password_verify($current_password, $user['password']))
        {
            $this->session->set_flashdata('password_validation_errors', array('current_password' => 'Your current password is incorrect.'));
            redirect('dashboard');
            return;
        }

        if (password_verify($new_password, $user['password']))
        {
            $this->session->set_flashdata(
                'password_validation_errors',
                array('new_password' => 'Your new password must be different from your current password.')
            );
            redirect('dashboard');
            return;
        }

        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        if ($password_hash === FALSE)
        {
            $this->set_flash('danger', 'Your password could not be secured. Please try again.');
            redirect('dashboard');
            return;
        }

        if ($this->User_model->update_password_and_clear_prompt($user_id, $password_hash))
        {
            $this->session->set_userdata('user_password_prompt_pending', FALSE);
            $this->session->sess_regenerate(TRUE);
            $this->set_flash('success', 'Your password was changed successfully.');
        }
        else
        {
            $this->set_flash('danger', 'Your password could not be changed. Please try again.');
        }

        redirect('dashboard');
    }


    // This method allows users to skip the first-login password change prompt, clearing the prompt state in the database and session.
    public function skip_password_change()
    {
        $this->require_post();
        $this->require_authenticated_user();

        if (!$this->session->userdata('user_password_prompt_pending'))
        {
            redirect('dashboard');
            return;
        }

        $user_id = (int) $this->session->userdata('user_id');

        if ($this->User_model->clear_password_prompt($user_id))
        {
            $this->session->set_userdata('user_password_prompt_pending', FALSE);
            $this->set_flash(
                'info',
                'Password change skipped. This first-login prompt will not appear again.'
            );
        }
        else
        {
            $this->set_flash('danger', 'The password prompt could not be dismissed. Please try again.');
        }

        redirect('dashboard');
    }

    // This method processes the logout request, destroying the session and redirecting to the login page.
    public function logout()
    {
        $this->require_post();

        $this->session->sess_destroy();
        redirect('login');
    }
    // This method ensures that the current request is a POST request, returning a 405 error if it is not.
    public function valid_name($name)
    {
        $name = trim((string) $name);

        if (!preg_match("/^[\p{L}\p{M}][\p{L}\p{M} .'-]{0,99}$/u", $name))
        {
            $this->form_validation->set_message(
                'valid_name',
                'The {field} field may contain letters, spaces, apostrophes, periods, and hyphens only.'
            );
            return FALSE;
        }

        return TRUE;
    }
   // This method checks if the provided birthday is a valid date, ensuring it is not in the future and is on or after January 1, 1900.
    public function valid_birthday($birthday)
    {
        $birthday = trim((string) $birthday);
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $birthday);
        $errors = DateTimeImmutable::getLastErrors();

        if (!$date || ($errors !== FALSE && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)))
        {
            $this->form_validation->set_message('valid_birthday', 'The {field} field must be a valid date.');
            return FALSE;
        }

        $today = new DateTimeImmutable('today');
        $minimum = new DateTimeImmutable('1900-01-01');

        if ($date > $today)
        {
            $this->form_validation->set_message('valid_birthday', 'The {field} field cannot be in the future.');
            return FALSE;
        }

        if ($date < $minimum)
        {
            $this->form_validation->set_message(
                'valid_birthday',
                'The {field} field must be on or after January 1, 1900.'
            );
            return FALSE;
        }

        return TRUE;
    }

    // This method checks if the provided contact number is valid, ensuring it contains only allowed characters and has a digit count between 7 and 15.
    public function valid_contactno($contactno)
    {
        $contactno = trim((string) $contactno);

        if (!preg_match('/^[0-9+()\-\s]{7,20}$/', $contactno))
        {
            $this->form_validation->set_message(
                'valid_contactno',
                'The {field} field contains unsupported characters.'
            );
            return FALSE;
        }

        $digit_count = strlen(preg_replace('/\D+/', '', $contactno));

        if ($digit_count < 7 || $digit_count > 15)
        {
            $this->form_validation->set_message(
                'valid_contactno',
                'The {field} field must contain between 7 and 15 digits.'
            );
            return FALSE;
        }

        return TRUE;
    }

    // This method checks if the provided email address is unique in the system, returning a validation error if it already exists.
    public function unique_email($email)
    {
        if ($this->User_model->email_exists($email))
        {
            $this->form_validation->set_message('unique_email', 'That email address is already registered.');
            return FALSE;
        }

        return TRUE;
    }
   
    // This method generates a secure random password of the specified length, ensuring it contains uppercase, lowercase, numeric, and special characters.
    public function strong_password($password)
    {
        $password = (string) $password;

        if (
            preg_match('/\s/', $password) ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^A-Za-z0-9]/', $password)
        )
        {
            $this->form_validation->set_message(
                'strong_password',
                'The {field} field must use uppercase, lowercase, a number, a symbol, and no spaces.'
            );
            return FALSE;
        }

        return TRUE;
    }
   // This method ensures that the current request is a POST request, returning a 405 error if it is not.
    private function set_registration_rules()
    {
        $this->form_validation->set_rules(
            'firstname',
            'First name',
            'trim|required|max_length[100]|callback_valid_name'
        );
        $this->form_validation->set_rules(
            'lastname',
            'Last name',
            'trim|required|max_length[100]|callback_valid_name'
        );
        $this->form_validation->set_rules(
            'birthday',
            'Birthday',
            'trim|required|callback_valid_birthday'
        );
        $this->form_validation->set_rules(
            'address',
            'Address',
            'trim|required|min_length[5]|max_length[255]'
        );
        $this->form_validation->set_rules(
            'contactno',
            'Contact number',
            'trim|required|max_length[20]|callback_valid_contactno'
        );
        $this->form_validation->set_rules(
            'email',
            'Email',
            'trim|required|valid_email|max_length[190]|callback_unique_email'
        );
    }
    // This method ensures that the current request is a POST request, returning a 405 error if it is not.
    private function registration_payload()
    {
        return array(
            'firstname' => trim((string) $this->input->post('firstname', TRUE)),
            'lastname' => trim((string) $this->input->post('lastname', TRUE)),
            'birthday' => trim((string) $this->input->post('birthday', TRUE)),
            'address' => trim((string) $this->input->post('address', TRUE)),
            'contactno' => trim((string) $this->input->post('contactno', TRUE)),
            'email' => strtolower(trim((string) $this->input->post('email', TRUE)))
        );
    }

    // This method ensures that the current request is a POST request, returning a 405 error if it is not.
    private function upload_registration_picture()
    {
        if (!isset($_FILES['profile_picture']) || (int) $_FILES['profile_picture']['error'] === UPLOAD_ERR_NO_FILE)
        {
            return NULL;
        }

        if (!$this->is_valid_profile_image($_FILES['profile_picture']))
        {
            $this->set_flash('danger', $this->profile_picture_error($_FILES['profile_picture']));
            return FALSE;
        }

        $upload_path = FCPATH . 'uploads/profile/';

        if (!is_dir($upload_path) && !mkdir($upload_path, 0750, TRUE))
        {
            $this->set_flash('danger', 'The profile picture folder is not writable.');
            return FALSE;
        }

        $this->load->library('upload', array(
            'upload_path' => $upload_path,
            'allowed_types' => '*',
            'max_size' => 2048,
            'max_width' => 2000,
            'max_height' => 2000,
            'detect_mime' => FALSE,
            'encrypt_name' => TRUE,
            'remove_spaces' => TRUE
        ));

        if (!$this->upload->do_upload('profile_picture'))
        {
            $this->set_flash('danger', 'The profile picture could not be saved. Please try again with a JPG, PNG, or WebP image up to 2 MB.');
            return FALSE;
        }

        return 'uploads/profile/' . $this->upload->data('file_name');
    }

    // This method checks if the uploaded profile picture is valid, ensuring it meets size, dimension, and MIME type requirements.
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

    // This method generates a secure random password of the specified length, ensuring it contains uppercase, lowercase, numeric, and special characters.
    private function generate_password($length)
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%*?';
        $password = '';
        $max = strlen($alphabet) - 1;

        do
        {
            $password = '';

            for ($i = 0; $i < $length; $i++)
            {
                $password .= $alphabet[random_int(0, $max)];
            }
        }
        while (
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[!@#$%*?]/', $password)
        );

        return $password;
    }

    // This method starts an authenticated session for the user, storing their information in the session and regenerating the session ID for security.
    private function start_authenticated_session($user)
    {

       $calculatedAge = $this->calculate_age($user['birthday']);

        $this->session->sess_regenerate(TRUE);

        $this->session->set_userdata(array(
            'logged_in' => TRUE,
            'user_id' => (int) $user['Id'],
            'user_firstname' => (string) $user['firstname'],
            'user_lastname' => (string) $user['lastname'],
            'user_email' => (string) $user['email'],
            'user_address' => (string) $user['address'],
            'user_contactno' => (string) $user['contactno'],
            'user_birthday' => (string) $user['birthday'],
            'user_age' => (string) $calculatedAge,
            'user_password_prompt_pending' => ((int) $user['must_change_password'] === 1)
            ,'user_profile_picture' => (string) (isset($user['profile_picture']) ? $user['profile_picture'] : '')
        ));
    }

    // This method ensures that the current user is authenticated, redirecting to the login page if they are not.
    private function set_flash($type, $message)
    {
        $allowed_types = array('success', 'danger', 'warning', 'info');

        $this->session->set_flashdata('flash', array(
            'type' => in_array($type, $allowed_types, TRUE) ? $type : 'info',
            'message' => (string) $message
        ));
    }

    // This method ensures that the current user is authenticated, redirecting to the login page if they are not.
    private function require_authenticated_user()
    {
        if (!$this->session->userdata('logged_in'))
        {
            $this->session->set_userdata('redirect_after_login', 'dashboard');
            redirect('login');
            exit;
        }
    }

    private function normalize_redirect_target($return_to)
    {
        $return_to = trim((string) $return_to);

        if ($return_to === '')
        {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $return_to))
        {
            $target_host = parse_url($return_to, PHP_URL_HOST);
            $base_host = parse_url(base_url(), PHP_URL_HOST);

            if (!is_string($base_host) || !is_string($target_host) || strtolower($base_host) !== strtolower($target_host))
            {
                return '';
            }

            $path = parse_url($return_to, PHP_URL_PATH);
            $base_path = trim((string) parse_url(base_url(), PHP_URL_PATH), '/');
            $normalized_path = $path === false || $path === null || $path === '' ? '/' : $path;

            if ($base_path !== '')
            {
                $base_prefix = '/' . $base_path;
                if (strpos($normalized_path, $base_prefix) === 0)
                {
                    $normalized_path = substr($normalized_path, strlen($base_prefix));
                }
            }

            $normalized_path = '/' . ltrim((string) $normalized_path, '/');
            $relative_path = $normalized_path === '/' ? 'dashboard' : ltrim($normalized_path, '/');
            $query = parse_url($return_to, PHP_URL_QUERY);

            return $query !== '' && $query !== null ? $relative_path . '?' . $query : $relative_path;
        }

        if (strpos($return_to, '://') !== FALSE || strpos($return_to, '//') === 0)
        {
            return '';
        }

        $return_to = ltrim($return_to, '/');

        return $return_to !== '' ? $return_to : '';
    }

    // This method ensures that the current request is a POST request, returning a 405 error if it is not.
    private function require_post()
    {
        if (strtoupper($this->input->method()) !== 'POST')
        {
            show_error('Method Not Allowed', 405);
        }
    }

    // This method calculates the age of a user based on their birthday. It returns the age in years or 0 if the birthday is invalid.
    private function calculate_age($birthday){

         try
        {
            $birthday = new DateTimeImmutable($birthday);
            return $birthday->diff(new DateTimeImmutable('today'))->y;

        }
        catch (Exception $exception)
        {
            return 00000;
        }

    }

    
}
