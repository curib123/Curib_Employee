<?php
/**
 * application/controllers/Auth.php | 2026-09-21
 * Registration, generated-password login, first-login password prompt, logout, and sessions.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

    public function login()
    {
        if ($this->session->userdata('logged_in'))
        {
            redirect('dashboard');
            return;
        }

        $prefill_email = $this->session->flashdata('old_email');

        if (!$prefill_email)
        {
            $prefill_email = strtolower(trim((string) $this->input->get('email', TRUE)));
        }

        $data = array(
            'flash' => $this->session->flashdata('flash'),
            'old_email' => $prefill_email ?: ''
        );

        $this->load->view('auth/login.php', $data);
    }

    public function login_submit()
    {
        $this->require_post();

        if ($this->session->userdata('logged_in'))
        {
            redirect('dashboard');
            return;
        }

        $lock_until = (int) $this->session->userdata('login_lock_until');

        if ($lock_until > time())
        {
            $seconds = $lock_until - time();
            $this->set_flash('warning', 'Too many failed login attempts. Try again in ' . $seconds . ' seconds.');
            redirect('login');
            return;
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[190]');
        $this->form_validation->set_rules('password', 'Password', 'required|max_length[255]');

        $email = strtolower(trim((string) $this->input->post('email', TRUE)));
        $password = (string) $this->input->post('password', FALSE);

        if ($this->form_validation->run() === FALSE)
        {
            $this->session->set_flashdata('old_email', $email);
            $this->set_flash('danger', 'Enter a valid email address and password.');
            redirect('login');
            return;
        }

        $user = $this->User_model->find_by_email($email);

        if (!$user || !password_verify($password, $user['password']))
        {
            $this->record_failed_login();
            $this->session->set_flashdata('old_email', $email);
            $this->set_flash('danger', 'The email or password is incorrect.');
            redirect('login');
            return;
        }

        $this->clear_login_attempts();
        $this->start_authenticated_session($user);

        if ((int) $user['must_change_password'] !== 1)
        {
            $this->set_flash('success', 'Welcome back, ' . $user['firstname'] . '.');
        }

        redirect('dashboard');
    }

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

        $plain_password = $this->generate_password(14);
        $payload['password'] = password_hash($plain_password, PASSWORD_DEFAULT);
        $payload['must_change_password'] = 1;

        if (!$this->User_model->insert($payload))
        {
            unset($payload['password'], $payload['must_change_password']);
            $this->session->set_flashdata('old_input', $payload);
            $this->set_flash('danger', 'Registration could not be completed. Please try again.');
            redirect('register');
            return;
        }

        /*
         * Do not place the generated plaintext password in session flashdata.
         * Sessions are database-backed, so doing that would temporarily store
         * the plaintext password in ci_sessions. Render it directly instead.
         */
        $this->output
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
            ->set_header('Pragma: no-cache')
            ->set_header('Expires: 0');

        $this->load->view('auth/registration_password.php', array(
            'generated_password' => $plain_password,
            'registration_email' => $payload['email']
        ));
    }

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
        $new_password = (string) $this->input->post('new_password', FALSE);

        if (!$user)
        {
            $this->session->sess_destroy();
            redirect('login');
            return;
        }

        if (password_verify($new_password, $user['password']))
        {
            $this->session->set_flashdata(
                'password_validation_errors',
                array('new_password' => 'Your new password must be different from your generated password.')
            );
            redirect('dashboard');
            return;
        }

        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        if ($this->User_model->update_password_and_clear_prompt($user_id, $password_hash))
        {
            $this->session->set_userdata('user_password_prompt_pending', FALSE);
            $this->set_flash('success', 'Your password was changed successfully.');
        }
        else
        {
            $this->set_flash('danger', 'Your password could not be changed. Please try again.');
        }

        redirect('dashboard');
    }

    public function skip_password_change()
    {
        $this->require_post();
        $this->require_authenticated_user();

        $user_id = (int) $this->session->userdata('user_id');

        if ($this->User_model->clear_password_prompt($user_id))
        {
            $this->session->set_userdata('user_password_prompt_pending', FALSE);
            $this->set_flash(
                'info',
                'Password change skipped. The first-login prompt will not appear again.'
            );
        }
        else
        {
            $this->set_flash('danger', 'The password prompt could not be dismissed. Please try again.');
        }

        redirect('dashboard');
    }

    public function logout()
    {
        $this->require_post();

        $this->session->sess_destroy();
        redirect('login');
    }

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
            $this->form_validation->set_message('valid_birthday', 'The {field} field must be on or after January 1, 1900.');
            return FALSE;
        }

        return TRUE;
    }

    public function valid_contactno($contactno)
    {
        if (!preg_match('/^[0-9+()\-\s]{7,20}$/', (string) $contactno))
        {
            $this->form_validation->set_message(
                'valid_contactno',
                'The {field} field must contain 7 to 20 valid phone characters.'
            );
            return FALSE;
        }

        return TRUE;
    }

    public function unique_email($email)
    {
        if ($this->User_model->email_exists($email))
        {
            $this->form_validation->set_message('unique_email', 'That email address is already registered.');
            return FALSE;
        }

        return TRUE;
    }

    public function strong_password($password)
    {
        $password = (string) $password;

        if (
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^A-Za-z0-9]/', $password)
        )
        {
            $this->form_validation->set_message(
                'strong_password',
                'The {field} field must contain uppercase, lowercase, number, and symbol characters.'
            );
            return FALSE;
        }

        return TRUE;
    }

    private function set_registration_rules()
    {
        $this->form_validation->set_rules('firstname', 'First name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('lastname', 'Last name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('birthday', 'Birthday', 'trim|required|callback_valid_birthday');
        $this->form_validation->set_rules('address', 'Address', 'trim|required|max_length[255]');
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

    private function start_authenticated_session($user)
    {
        $this->session->sess_regenerate(TRUE);

        $this->session->set_userdata(array(
            'logged_in' => TRUE,
            'user_id' => (int) $user['Id'],
            'user_firstname' => $user['firstname'],
            'user_lastname' => $user['lastname'],
            'user_email' => $user['email'],
            'user_password_prompt_pending' => ((int) $user['must_change_password'] === 1)
        ));
    }

    private function record_failed_login()
    {
        $attempts = (int) $this->session->userdata('login_attempts') + 1;
        $this->session->set_userdata('login_attempts', $attempts);

        if ($attempts >= 5)
        {
            $this->session->set_userdata('login_lock_until', time() + 60);
            $this->session->set_userdata('login_attempts', 0);
        }
    }

    private function clear_login_attempts()
    {
        $this->session->unset_userdata(array('login_attempts', 'login_lock_until'));
    }

    private function set_flash($type, $message)
    {
        $this->session->set_flashdata('flash', array(
            'type' => $type,
            'message' => $message
        ));
    }

    private function require_authenticated_user()
    {
        if (!$this->session->userdata('logged_in'))
        {
            redirect('login');
            exit;
        }
    }

    private function require_post()
    {
        if (strtoupper($this->input->method()) !== 'POST')
        {
            show_error('Method Not Allowed', 405);
        }
    }
}
