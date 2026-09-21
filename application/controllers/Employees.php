<?php
/**
 * application/controllers/Employees.php | 2026-09-21
 * Employee CRUD controller with validation, CSRF-aware forms, and age calculation.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Employees extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Employee_model');
        $this->load->library('form_validation');
    }

    /**
     * Show the employee list and all Bootstrap CRUD modals.
     */
    public function index()
    {
        $employees = $this->Employee_model->get_all();

        foreach ($employees as &$employee)
        {
            $employee['age'] = $this->calculate_age($employee['birthday']);
        }
        unset($employee);

        $data = array(
            'employees' => $employees,
            'today' => date('Y-m-d'),
            'flash' => $this->session->flashdata('flash'),
            'validation_errors' => $this->session->flashdata('validation_errors') ?: array(),
            'old_input' => $this->session->flashdata('old_input') ?: array(),
            'validation_context' => $this->session->flashdata('validation_context') ?: array()
        );

        $this->load->view('employees/index.html', $data);
    }

    /**
     * Create a new employee.
     */
    public function store()
    {
        $this->require_post();
        $this->set_validation_rules();

        $payload = $this->employee_payload();

        if ($this->form_validation->run() === FALSE)
        {
            $this->preserve_validation_state('create', NULL, $payload);
            redirect('employees');
            return;
        }

        if ($this->Employee_model->insert($payload))
        {
            $this->set_flash('success', 'Employee added successfully.');
        }
        else
        {
            $this->set_flash('danger', 'The employee could not be added.');
        }

        redirect('employees');
    }

    /**
     * Update an existing employee.
     */
    public function update($id)
    {
        $this->require_post();

        $id = (int) $id;
        $employee = $this->Employee_model->find($id);

        if (!$employee)
        {
            $this->set_flash('danger', 'Employee not found.');
            redirect('employees');
            return;
        }

        $this->set_validation_rules();
        $payload = $this->employee_payload();

        if ($this->form_validation->run() === FALSE)
        {
            $this->preserve_validation_state('update', $id, $payload);
            redirect('employees');
            return;
        }

        if ($this->Employee_model->update($id, $payload))
        {
            $this->set_flash('success', 'Employee updated successfully.');
        }
        else
        {
            $this->set_flash('danger', 'The employee could not be updated.');
        }

        redirect('employees');
    }

    /**
     * Delete an employee after modal confirmation.
     */
    public function delete($id)
    {
        $this->require_post();

        $id = (int) $id;
        $employee = $this->Employee_model->find($id);

        if (!$employee)
        {
            $this->set_flash('danger', 'Employee not found.');
            redirect('employees');
            return;
        }

        if ($this->Employee_model->delete($id))
        {
            $this->set_flash('success', 'Employee deleted successfully.');
        }
        else
        {
            $this->set_flash('danger', 'The employee could not be deleted.');
        }

        redirect('employees');
    }

    /**
     * Server-side birthday validation.
     */
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

    /**
     * Contact number accepts digits plus common phone separators only.
     */
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

    /**
     * Configure reusable server-side validation rules.
     */
    private function set_validation_rules()
    {
        $this->form_validation->set_rules(
            'firstname',
            'First name',
            'trim|required|max_length[100]'
        );
        $this->form_validation->set_rules(
            'lastname',
            'Last name',
            'trim|required|max_length[100]'
        );
        $this->form_validation->set_rules(
            'birthday',
            'Birthday',
            'trim|required|callback_valid_birthday'
        );
        $this->form_validation->set_rules(
            'address',
            'Address',
            'trim|required|max_length[255]'
        );
        $this->form_validation->set_rules(
            'contactno',
            'Contact number',
            'trim|required|max_length[20]|callback_valid_contactno'
        );
    }

    /**
     * Read only the expected fields from POST.
     * CI input filtering plus Query Builder parameter escaping protects database writes.
     */
    private function employee_payload()
    {
        return array(
            'firstname' => trim((string) $this->input->post('firstname', TRUE)),
            'lastname' => trim((string) $this->input->post('lastname', TRUE)),
            'birthday' => trim((string) $this->input->post('birthday', TRUE)),
            'address' => trim((string) $this->input->post('address', TRUE)),
            'contactno' => trim((string) $this->input->post('contactno', TRUE))
        );
    }

    /**
     * Keep submitted data and validation errors across the redirect.
     */
    private function preserve_validation_state($mode, $id, $payload)
    {
        $this->session->set_flashdata('validation_errors', $this->form_validation->error_array());
        $this->session->set_flashdata('old_input', $payload);
        $this->session->set_flashdata('validation_context', array(
            'mode' => $mode,
            'id' => $id
        ));
    }

    /**
     * Save a one-time Bootstrap alert-modal message.
     */
    private function set_flash($type, $message)
    {
        $this->session->set_flashdata('flash', array(
            'type' => $type,
            'message' => $message
        ));
    }

    /**
     * Derive age from birthday so age never becomes stale in the database.
     */
    private function calculate_age($birthday)
    {
        try
        {
            $birth_date = new DateTimeImmutable($birthday);
            return $birth_date->diff(new DateTimeImmutable('today'))->y;
        }
        catch (Exception $exception)
        {
            return 0;
        }
    }

    /**
     * CRUD mutations must never accept GET requests.
     */
    private function require_post()
    {
        if (strtoupper($this->input->method()) !== 'POST')
        {
            show_error('Method Not Allowed', 405);
        }
    }
}
