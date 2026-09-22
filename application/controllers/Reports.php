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
        $format = in_array($format, array('csv', 'xls', 'xlsx', 'pdf'), TRUE) ? $format : 'csv';
        $format = $format === 'xls' ? 'xlsx' : $format;
        $filename = strtolower(str_replace(' ', '-', $report_data['title'])) . '-' . date('Ymd') . '.' . $format;

        if ($format === 'csv')
        {
            $this->download_csv($filename, $report_data);
        }
        elseif ($format === 'xlsx')
        {
            $this->download_xlsx($filename, $report_data);
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

    /**
     * Generate a standards-compliant UTF-8 CSV with PhpSpreadsheet.
     */
    private function download_csv($filename, $data)
    {
        if (!$this->ensure_export_class('PhpOffice\\PhpSpreadsheet\\Spreadsheet'))
        {
            return;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->write_plain_table($sheet, $data['headers'], $this->sanitize_csv_rows($data['rows']), 1);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->setUseBOM(TRUE);

        $this->stream_spreadsheet_writer($writer, $filename, 'text/csv; charset=UTF-8');
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Generate a real styled XLSX workbook with freeze panes, filters and print settings.
     */
    private function download_xlsx($filename, $data)
    {
        if (!$this->ensure_export_class('PhpOffice\\PhpSpreadsheet\\Spreadsheet'))
        {
            return;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Curib Employee')
            ->setTitle($data['title'])
            ->setSubject('Curib Employee report export');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($this->safe_sheet_title($data['title']));

        $column_count = max(1, count($data['headers']));
        $last_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column_count);
        $last_row = max(5, 4 + count($data['rows']));

        $sheet->mergeCells('A1:' . $last_column . '1');
        $sheet->setCellValueExplicit('A1', $data['title'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->mergeCells('A2:' . $last_column . '2');
        $sheet->setCellValueExplicit(
            'A2',
            'Generated ' . date('M d, Y g:i A') . ' • ' . count($data['rows']) . ' record' . (count($data['rows']) === 1 ? '' : 's'),
            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
        );

        $this->write_plain_table($sheet, $data['headers'], $data['rows'], 4);

        $sheet->getStyle('A1:' . $last_column . '1')->applyFromArray(array(
            'font' => array('bold' => TRUE, 'size' => 18, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('rgb' => '1D4ED8')),
            'alignment' => array('vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
        ));
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->getStyle('A2:' . $last_column . '2')->applyFromArray(array(
            'font' => array('color' => array('rgb' => '475569'), 'italic' => TRUE),
            'fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('rgb' => 'F8FAFC'))
        ));

        $sheet->getStyle('A4:' . $last_column . '4')->applyFromArray(array(
            'font' => array('bold' => TRUE, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('rgb' => '2563EB')),
            'alignment' => array('vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER),
            'borders' => array('bottom' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => array('rgb' => '1D4ED8')))
        ));
        $sheet->getRowDimension(4)->setRowHeight(24);

        if (!empty($data['rows']))
        {
            $sheet->getStyle('A5:' . $last_column . $last_row)->applyFromArray(array(
                'borders' => array(
                    'bottom' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR, 'color' => array('rgb' => 'E2E8F0'))
                ),
                'alignment' => array('vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP, 'wrapText' => TRUE)
            ));
        }

        for ($column_index = 1; $column_index <= $column_count; $column_index++)
        {
            $column_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column_index);
            $max_length = isset($data['headers'][$column_index - 1]) ? strlen((string) $data['headers'][$column_index - 1]) : 10;

            foreach ($data['rows'] as $row)
            {
                $value = isset($row[$column_index - 1]) ? (string) $row[$column_index - 1] : '';
                $max_length = max($max_length, function_exists('mb_strlen') ? mb_strlen($value) : strlen($value));
            }

            $sheet->getColumnDimension($column_letter)->setWidth(min(38, max(12, $max_length + 2)));
        }

        $sheet->freezePane('A5');
        $sheet->setAutoFilter('A4:' . $last_column . $last_row);
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 4);
        $sheet->getPageMargins()->setTop(0.45)->setRight(0.35)->setBottom(0.45)->setLeft(0.35);
        $sheet->getPageSetup()->setHorizontalCentered(TRUE);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $this->stream_spreadsheet_writer(
            $writer,
            $filename,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Render a responsive, multi-page PDF table using Dompdf.
     */
    private function download_pdf($filename, $data)
    {
        if (!$this->ensure_export_class('Dompdf\\Dompdf'))
        {
            return;
        }

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', FALSE);
        $options->set('isHtml5ParserEnabled', TRUE);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($this->build_pdf_html($data), 'UTF-8');
        $dompdf->setPaper('A4', count($data['headers']) > 5 ? 'landscape' : 'portrait');
        $dompdf->render();

        $this->output
            ->set_content_type('application/pdf')
            ->set_header('Content-Disposition: attachment; filename="' . $filename . '"')
            ->set_header('X-Content-Type-Options: nosniff')
            ->set_output($dompdf->output());
    }

    /**
     * Write headers and rows as explicit strings so spreadsheet formulas cannot be injected.
     */
    private function write_plain_table($sheet, $headers, $rows, $header_row)
    {
        foreach (array_values($headers) as $column_index => $header)
        {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column_index + 1) . $header_row;
            $sheet->setCellValueExplicit($cell, (string) $header, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        }

        foreach (array_values($rows) as $row_index => $row)
        {
            foreach (array_values($row) as $column_index => $value)
            {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column_index + 1) . ($header_row + $row_index + 1);
                $sheet->setCellValueExplicit($cell, (string) $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
        }
    }

    /**
     * Prevent spreadsheet formula execution when CSV is opened in Excel or similar software.
     */
    private function sanitize_csv_rows($rows)
    {
        $safe_rows = array();

        foreach ($rows as $row)
        {
            $safe_row = array();

            foreach ($row as $value)
            {
                $value = (string) $value;

                if ($value !== '' && preg_match('/^[=+\\-@]/', $value))
                {
                    $value = "'" . $value;
                }

                $safe_row[] = $value;
            }

            $safe_rows[] = $safe_row;
        }

        return $safe_rows;
    }

    private function stream_spreadsheet_writer($writer, $filename, $content_type)
    {
        ob_start();
        $writer->save('php://output');
        $binary = ob_get_clean();

        $this->output
            ->set_content_type($content_type)
            ->set_header('Content-Disposition: attachment; filename="' . $filename . '"')
            ->set_header('X-Content-Type-Options: nosniff')
            ->set_output($binary);
    }

    private function safe_sheet_title($title)
    {
        $title = preg_replace('/[\\\\\/?*\[\]:]+/', ' ', (string) $title);
        $title = trim($title);
        return substr($title !== '' ? $title : 'Report', 0, 31);
    }

    private function build_pdf_html($data)
    {
        $escape = function ($value)
        {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        };

        $header_html = '';
        foreach ($data['headers'] as $header)
        {
            $header_html .= '<th>' . $escape($header) . '</th>';
        }

        $rows_html = '';
        if (empty($data['rows']))
        {
            $rows_html = '<tr><td class="empty" colspan="' . max(1, count($data['headers'])) . '">No records available.</td></tr>';
        }
        else
        {
            foreach ($data['rows'] as $row)
            {
                $rows_html .= '<tr>';
                foreach ($row as $value)
                {
                    $rows_html .= '<td>' . $escape($value) . '</td>';
                }
                $rows_html .= '</tr>';
            }
        }

        return '<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page { margin: 28px 30px 34px; }
    * { box-sizing: border-box; }
    body { margin: 0; color: #0f172a; font-family: DejaVu Sans, sans-serif; font-size: 9px; }
    .report-header { margin-bottom: 18px; padding: 16px 18px; border-radius: 8px; background: #1d4ed8; color: #fff; }
    .report-header h1 { margin: 0 0 5px; font-size: 18px; font-weight: 700; }
    .report-meta { color: #dbeafe; font-size: 8.5px; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    thead { display: table-header-group; }
    tr { page-break-inside: avoid; }
    th { padding: 8px 7px; border: 1px solid #1d4ed8; background: #2563eb; color: #fff; font-size: 8px; text-align: left; text-transform: uppercase; }
    td { padding: 7px; border: 1px solid #e2e8f0; color: #334155; vertical-align: top; word-wrap: break-word; }
    tbody tr:nth-child(even) td { background: #f8fafc; }
    .empty { padding: 22px; color: #64748b; text-align: center; }
    .footer-note { margin-top: 10px; color: #94a3b8; font-size: 7.5px; text-align: right; }
</style>
</head>
<body>
    <div class="report-header">
        <h1>' . $escape($data['title']) . '</h1>
        <div class="report-meta">Generated ' . $escape(date('M d, Y g:i A')) . ' • ' . count($data['rows']) . ' record' . (count($data['rows']) === 1 ? '' : 's') . '</div>
    </div>
    <table>
        <thead><tr>' . $header_html . '</tr></thead>
        <tbody>' . $rows_html . '</tbody>
    </table>
    <div class="footer-note">Curib Employee • Report export</div>
</body>
</html>';
    }

    /**
     * Load Composer only when an export needs it, then fail with a useful message if dependencies are missing.
     */
    private function ensure_export_class($class)
    {
        if (class_exists($class))
        {
            return TRUE;
        }

        $autoload = FCPATH . 'vendor/autoload.php';
        if (is_file($autoload))
        {
            require_once $autoload;
        }

        if (!class_exists($class))
        {
            show_error(
                'Export dependencies are not installed. Run "composer install" in the project root, then try the export again.',
                500,
                'Export library unavailable'
            );
            return FALSE;
        }

        return TRUE;
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
