<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Employee_model');
    }

    // This method displays the reports page, showing registration counts, monthly registration data, and age statistics for users. It retrieves the necessary data from the User_model and prepares it for the view.
    public function index()
    {
        list($start, $end) = $this->date_range();
        $end_exclusive = date('Y-m-d 00:00:00', strtotime($end . ' +1 day'));
        $data = array(
            'current_user' => $this->current_user,
            'start_date' => $start,
            'end_date' => $end,
            'registration_count' => count($this->User_model->get_registration_report($start . ' 00:00:00', $end_exclusive)),
            'monthly_report' => $this->User_model->get_monthly_registration_report($start . ' 00:00:00', $end_exclusive),
            'age_report' => $this->User_model->get_age_report(),
            'flash' => $this->session->flashdata('flash')
        );
        $this->load->view('reports/index.php', $data);
    }
 
    // This method handles the export of various reports in different formats (CSV, XLS, PDF). It retrieves the requested report data based on the specified date range and prepares it for download in the chosen format. The method supports registration reports, monthly registration reports, age statistics reports, and employee reports.
    public function export()
    {
        $report = (string) $this->input->get('report', TRUE);
        $format = strtolower((string) $this->input->get('format', TRUE));
        list($start, $end) = $this->date_range();
        $end_exclusive = date('Y-m-d 00:00:00', strtotime($end . ' +1 day'));
        $reports = array(
            'registrations' => array('title' => 'Registration Report', 'headers' => array('ID', 'First name', 'Last name', 'Email', 'Birthday', 'Address', 'Contact', 'Registered at'), 'rows' => $this->registration_rows($start, $end_exclusive)),
            'monthly' => array('title' => 'Monthly Registration Report', 'headers' => array('Month', 'Registrations'), 'rows' => $this->monthly_rows($start, $end_exclusive)),
            'age' => array('title' => 'Age Statistics Report', 'headers' => array('Age range', 'Users'), 'rows' => $this->age_rows()),
            'employees' => array('title' => 'Employee Report', 'headers' => array('ID', 'First name', 'Last name', 'Birthday', 'Address', 'Contact'), 'rows' => $this->employee_rows())
        );
        if (!isset($reports[$report]))
        {
            show_error('Unknown report.', 400);
            return;
        }
        $report_data = $reports[$report];
        $format = in_array($format, array('csv', 'xls', 'pdf'), TRUE) ? $format : 'csv';
        $filename = strtolower(str_replace(' ', '-', $report_data['title'])) . '-' . date('Ymd') . '.' . $format;
        if ($format === 'csv')
        {
            $this->download_csv($filename, $report_data);
        }
        elseif ($format === 'xls')
        {
            $this->download_xls($filename, $report_data);
        }
        else
        {
            $this->download_pdf($filename, $report_data);
        }
    }

    // This method retrieves registration report data from the User_model for a specified date range and formats it into an array of rows suitable for export. Each row contains user details such as ID, first name, last name, email, birthday, address, contact number, and registration date.
    private function registration_rows($start, $end)
    {
        $rows = array();
        foreach ($this->User_model->get_registration_report($start, $end) as $user)
        {
            $rows[] = array($user['Id'], $user['firstname'], $user['lastname'], $user['email'], $user['birthday'], $user['address'], $user['contactno'], $user['created_at']);
        }
        return $rows;
    }

    // This method retrieves monthly registration report data from the User_model for a specified date range and formats it into an array of rows suitable for export. Each row contains the registration month and the total number of registrations for that month.
    private function monthly_rows($start, $end)
    {
        $rows = array();
        foreach ($this->User_model->get_monthly_registration_report($start, $end) as $item)
        {
            $rows[] = array($item['registration_month'], $item['total']);
        }
        return $rows;
    }

    // This method retrieves age statistics report data from the User_model and formats it into an array of rows suitable for export. Each row contains an age range and the total number of users in that range.
    private function age_rows()
    {
        $rows = array();
        foreach ($this->User_model->get_age_report() as $item)
        {
            $rows[] = array($item['age_range'], $item['total']);
        }
        return $rows;
    }

    // This method retrieves employee report data from the Employee_model and formats it into an array of rows suitable for export. Each row contains employee details such as ID, first name, last name, birthday, address, and contact number.
    private function employee_rows()
    {
        $rows = array();
        foreach ($this->Employee_model->get_report() as $employee)
        {
            $rows[] = array($employee['Id'], $employee['firstname'], $employee['lastname'], $employee['birthday'], $employee['address'], $employee['contactno']);
        }
        return $rows;
    }

    // This method handles the download of a CSV file containing the specified report data. It sets the appropriate headers for CSV content and outputs the data in CSV format, including headers and rows.
    private function download_csv($filename, $data)
    {
        $this->output->set_content_type('text/csv')->set_header('Content-Disposition: attachment; filename="' . $filename . '"');
        $handle = fopen('php://output', 'w');
        fputcsv($handle, $data['headers']);
        foreach ($data['rows'] as $row) { fputcsv($handle, $row); }
        fclose($handle);
    }

    // This method handles the download of an XLS file containing the specified report data. It generates an HTML table representation of the data and sets the appropriate headers for Excel content, allowing the user to download the report in XLS format.
    private function download_xls($filename, $data)
    {
        $html = '<table><tr><th>' . implode('</th><th>', array_map('html_escape', $data['headers'])) . '</th></tr>';
        foreach ($data['rows'] as $row) { $html .= '<tr><td>' . implode('</td><td>', array_map('html_escape', $row)) . '</td></tr>'; }
        $html .= '</table>';
        $this->output->set_content_type('application/vnd.ms-excel')->set_header('Content-Disposition: attachment; filename="' . $filename . '"')->set_output($html);
    }

    // This method handles the download of a PDF file containing the specified report data. It generates a simple text-based PDF representation of the data and sets the appropriate headers for PDF content, allowing the user to download the report in PDF format.
    private function download_pdf($filename, $data)
    {
        $lines = array($data['title'], '');
        $lines[] = implode(' | ', $data['headers']);
        foreach ($data['rows'] as $row) { $lines[] = implode(' | ', $row); }
        $pdf = $this->make_text_pdf($lines);
        $this->output->set_content_type('application/pdf')->set_header('Content-Disposition: attachment; filename="' . $filename . '"')->set_output($pdf);
    }

    // This method generates a simple text-based PDF stream from an array of lines. It constructs the PDF structure, including the catalog, pages, and font resources, and returns the complete PDF content as a string.
    private function make_text_pdf($lines)
    {
        $stream = "BT\n/F1 9 Tf\n40 800 Td\n";
        foreach (array_slice($lines, 0, 55) as $index => $line)
        {
            if ($index > 0) { $stream .= "0 -14 Td\n"; }
            $text = substr(str_replace(array('\\', '(', ')'), array('\\\\', '\\(', '\\)'), preg_replace('/[^ -~]/', '', (string) $line)), 0, 115);
            $stream .= '(' . $text . ") Tj\n";
        }
        $stream .= "ET";
        $objects = array('', '<< /Type /Catalog /Pages 2 0 R >>', '<< /Type /Pages /Kids [3 0 R] /Count 1 >>', '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 842] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>', '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream", '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>');
        $pdf = "%PDF-1.4\n";
        $offsets = array(0);
        for ($i = 1; $i < count($objects); $i++) { $offsets[$i] = strlen($pdf); $pdf .= $i . " 0 obj\n" . $objects[$i] . "\nendobj\n"; }
        $xref = strlen($pdf); $pdf .= "xref\n0 " . count($objects) . "\n0000000000 65535 f \n";
        for ($i = 1; $i < count($objects); $i++) { $pdf .= sprintf('%010d 00000 n \n', $offsets[$i]); }
        return $pdf . "trailer\n<< /Size " . count($objects) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";
    }

    // This method retrieves and validates a date input from the GET parameters, returning it in 'Y-m-d' format or falling back to a default value if the input is invalid or missing.
    private function date_input($key, $fallback)
    {
        $value = trim((string) $this->input->get($key, TRUE));
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date && DateTimeImmutable::getLastErrors() === FALSE ? $date->format('Y-m-d') : $fallback;
    }

    // This method determines the date range for reports based on GET parameters, ensuring that the start date is not after the end date. It returns an array containing the validated start and end dates in 'Y-m-d' format.
    private function date_range()
    {
        $default_start = date('Y-m-01');
        $default_end = date('Y-m-d');
        $start = $this->date_input('start_date', $default_start);
        $end = $this->date_input('end_date', $default_end);

        if ($start > $end)
        {
            $end = $start;
        }

        return array($start, $end);
    }
}
