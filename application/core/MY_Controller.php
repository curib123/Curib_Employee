<?php
/**
 * application/core/MY_Controller.php | 2026-09-21
 * Base controller for authenticated application routes.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $current_user = array();


    public function __construct()
    {
        parent::__construct();

        $this->output
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
            ->set_header('Pragma: no-cache');

        if (!$this->session->userdata('logged_in'))
        {
            $this->session->set_flashdata('flash', array(
                'type' => 'warning',
                'message' => 'Please sign in to continue.'
            ));

            redirect('login');
            exit;
        }

        $this->current_user = array(
            'id' => (int) $this->session->userdata('user_id'),
            'firstname' => (string) $this->session->userdata('user_firstname'),
            'lastname' => (string) $this->session->userdata('user_lastname'),
            'email' => (string) $this->session->userdata('user_email'),
            'contactno' => (string) $this->session->userdata('user_contactno'),
            'address' => (string) $this->session->userdata('user_address'),
            'birthday' => (string) $this->session->userdata('user_birthday'),
            'age' => (string) $this->session->userdata('user_age')
            ,'profile_picture' => (string) $this->session->userdata('user_profile_picture')
        );

     
    }

    protected function require_post()
    {
        if (strtoupper($this->input->method()) !== 'POST')
        {
            show_error('Method Not Allowed', 405);
        }
    }

    protected function set_flash($type, $message)
    {
        $allowed_types = array('success', 'danger', 'warning', 'info');

        $this->session->set_flashdata('flash', array(
            'type' => in_array($type, $allowed_types, TRUE) ? $type : 'info',
            'message' => (string) $message
        ));
    }
}
