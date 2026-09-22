<?php
/**
 * application/controllers/Employees.php | 2026-09-21
 * Protected Employee CRUD controller with strict server-side validation and age calculation.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Employees extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Employee_model');
        $this->load->library('form_validation');
    }

    // This method displays a paginated list of employees, allowing for filtering, sorting, and searching. It retrieves the necessary data from the Employee_model and prepares it for the view.
    public function index()
    {
        $search = trim((string) $this->input->get('search', TRUE));
        $age_range = (string) $this->input->get('age_range', TRUE);
        $sort = (string) $this->input->get('sort', TRUE);
        $direction = (string) $this->input->get('direction', TRUE);
        $per_page = min(50, max(5, (int) $this->input->get('per_page')));
        $page = max(1, (int) $this->input->get('page'));
        $total = $this->Employee_model->count_filtered($search, $age_range);
        $total_pages = max(1, (int) ceil($total / $per_page));
        $page = min($page, $total_pages);
        $employees = $this->Employee_model->get_page($search, $age_range, $sort, $direction, $page, $per_page);

        foreach ($employees as &$employee)
        {
            $employee['age'] = $this->calculate_age($employee['birthday']);
        }
        unset($employee);

        $data = array(
            'current_user' => $this->current_user,
            'employees' => $employees,
            'employee_total' => $total,
            'employee_search' => $search,
            'employee_age_range' => $age_range,
            'employee_sort' => $sort,
            'employee_direction' => strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC',
            'employee_page' => $page,
            'employee_per_page' => $per_page,
            'employee_total_pages' => $total_pages,
            'today' => date('Y-m-d'),
            'flash' => $this->session->flashdata('flash'),
            'validation_errors' => $this->session->flashdata('validation_errors') ?: array(),
            'old_input' => $this->session->flashdata('old_input') ?: array(),
            'validation_context' => $this->session->flashdata('validation_context') ?: array()
        );

        $this->load->view('employees/index.html', $data);
    }
   // This method handles the creation of a new employee record. It checks if the request is a POST request, validates the input data, and attempts to insert the new employee into the database. It sets appropriate flash messages based on the outcome and redirects back to the employees list.
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
            $this->set_flash('danger', 'The employee could not be added. Please try again.');
        }

        redirect('employees');
    }
   // This method handles the deletion of an employee record. It checks if the request is a POST request, validates the employee ID, and attempts to delete the employee from the database. It sets appropriate flash messages based on the outcome and redirects back to the employees list.
    public function update($id)
    {
        $this->require_post();

        $id = (int) $id;

        if ($id < 1 || !$this->Employee_model->find($id))
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
            $this->set_flash('danger', 'The employee could not be updated. Please try again.');
        }

        redirect('employees');
    }

    // This method deletes an employee record based on the provided ID, ensuring that the request is a POST request and that the employee exists before attempting deletion. It sets appropriate flash messages based on the outcome of the operation.
    public function delete($id)
    {
        $this->require_post();

        $id = (int) $id;

        if ($id < 1 || !$this->Employee_model->find($id))
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
            $this->set_flash('danger', 'The employee could not be deleted. Please try again.');
        }

        redirect('employees');
    }

    // This method checks if the provided name is valid, allowing letters, spaces, apostrophes, periods, and hyphens only.
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
   // This method sets the validation rules for employee data, ensuring that each field meets specific criteria for format and length.
    private function set_validation_rules()
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
    }
     // This method retrieves the employee data from the POST request, sanitizes it, and returns it as an associative array.
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
    // This method preserves the validation state by storing validation errors, old input, and context in flash data for the next request.
    private function preserve_validation_state($mode, $id, $payload)
    {
        $this->session->set_flashdata('validation_errors', $this->form_validation->error_array());
        $this->session->set_flashdata('old_input', $payload);
        $this->session->set_flashdata('validation_context', array(
            'mode' => $mode,
            'id' => $id
        ));
    }
    // This method calculates the age based on the provided birthday, returning 0 if the date is invalid.
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
}
