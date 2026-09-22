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

    public function index()
    {
        $start = $this->date_input('start_date', date('Y-m-01'));
        $end = $this->date_input('end_date', date('Y-m-d'));
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

    public function export()
    {
        $report = (string) $this->input->get('report', TRUE);
        $format = strtolower((string) $this->input->get('format', TRUE));
        $start = $this->date_input('start_date', date('Y-m-01'));
        $end = $this->date_input('end_date', date('Y-m-d'));
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
        $format = in_array($format, array('csv', 'xls', 'pdf','docx'), TRUE) ? $format : 'csv';
        $filename = strtolower(str_replace(' ', '-', $report_data['title'])) . '-' . date('Ymd') . '.' . $format;
        if ($format === 'csv')
        {
            $this->download_csv($filename, $report_data);
        }
        elseif ($format === 'xls')
        {
            $this->download_xls($filename, $report_data);
        }
        elseif ($format === 'docx')
        {
            $this->download_docx($filename, $report_data);
        }
        else
        {
            $this->download_pdf($filename, $report_data);
        }
    }

    private function registration_rows($start, $end)
    {
        $rows = array();
        foreach ($this->User_model->get_registration_report($start, $end) as $user)
        {
            $rows[] = array($user['Id'], $user['firstname'], $user['lastname'], $user['email'], $user['birthday'], $user['address'], $user['contactno'], $user['created_at']);
        }
        return $rows;
    }

    private function monthly_rows($start, $end)
    {
        $rows = array();
        foreach ($this->User_model->get_monthly_registration_report($start, $end) as $item)
        {
            $rows[] = array($item['registration_month'], $item['total']);
        }
        return $rows;
    }

    private function age_rows()
    {
        $rows = array();
        foreach ($this->User_model->get_age_report() as $item)
        {
            $rows[] = array($item['age_range'], $item['total']);
        }
        return $rows;
    }

    private function employee_rows()
    {
        $rows = array();
        foreach ($this->Employee_model->get_report() as $employee)
        {
            $rows[] = array($employee['Id'], $employee['firstname'], $employee['lastname'], $employee['birthday'], $employee['address'], $employee['contactno']);
        }
        return $rows;
    }

    private function download_csv($filename, $data)
    {
        $this->output->set_content_type('text/csv')->set_header('Content-Disposition: attachment; filename="' . $filename . '"');
        $handle = fopen('php://output', 'w');
        fputcsv($handle, $data['headers']);
        foreach ($data['rows'] as $row) { fputcsv($handle, $row); }
        fclose($handle);
    }

    private function download_xls($filename, $data)
    {
        $html = '<table><tr><th>' . implode('</th><th>', array_map('html_escape', $data['headers'])) . '</th></tr>';
        foreach ($data['rows'] as $row) { $html .= '<tr><td>' . implode('</td><td>', array_map('html_escape', $row)) . '</td></tr>'; }
        $html .= '</table>';
        $this->output->set_content_type('application/vnd.ms-excel')->set_header('Content-Disposition: attachment; filename="' . $filename . '"')->set_output($html);
    }

    private function download_pdf($filename, $data)
    {
        $lines = array($data['title'], '');
        $lines[] = implode(' | ', $data['headers']);
        foreach ($data['rows'] as $row) { $lines[] = implode(' | ', $row); }
        $pdf = $this->make_text_pdf($lines);
        $this->output->set_content_type('application/pdf')->set_header('Content-Disposition: attachment; filename="' . $filename . '"')->set_output($pdf);
    }

    private function make_text_pdf($lines)
    {
        $stream = "BT\n/F1 9 Tf\n40 800 Td\n";
        foreach (array_slice($lines, 0, 55) as $index => $line)
        {
            if ($index > 0) { $stream .= "0 -14 Td\n"; }
            $text = substr(str_replace(array('\\', '(', ')'), array('\\\\', '\\(', '\\)'), preg_replace('/[^ -~]/', '', (string) $line)), 0, 115);
            $stream .= '(' . $text . ') Tj\n';
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

    private function date_input($key, $fallback)
    {
        $value = trim((string) $this->input->get($key, TRUE));
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date && DateTimeImmutable::getLastErrors() === FALSE ? $date->format('Y-m-d') : $fallback;
    }
}
