<?php
/**
 * application/models/Employee_model.php | 2026-09-21
 * Query Builder data access for the "employee" table.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_model extends CI_Model
{
    private $table = 'employee';


    // Retrieve a paginated list of employees with optional search, age range, address filters, and sorting
    public function get_page($search, $age_range, $address, $sort, $direction, $page, $per_page)
    {
        $this->apply_filters($search, $age_range, $address);

        $sort_columns = array(
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'birthday' => 'birthday',
            'address' => 'address',
            'contactno' => 'contactno'
        );
        $sort_column = isset($sort_columns[$sort]) ? $sort_columns[$sort] : 'lastname';
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $offset = max(0, ((int) $page - 1) * (int) $per_page);

        return $this->db
            ->select('Id, firstname, lastname, birthday, address, contactno')
            ->from($this->table)
            ->order_by($sort_column, $direction)
            ->order_by('firstname', 'ASC')
            ->limit((int) $per_page, $offset)
            ->get()
            ->result_array();
    }

    // Count the total number of employees matching the optional search, age range, and address filters
    public function count_filtered($search, $age_range, $address)
    {
        $this->apply_filters($search, $age_range, $address);
        return $this->db->count_all_results($this->table);
    }

    // Retrieve a single employee record by its ID
    public function find($id)
    {
        return $this->db
            ->select('Id, firstname, lastname, birthday, address, contactno')
            ->from($this->table)
            ->where('Id', (int) $id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    // Retrieve all employee records for reporting purposes
    public function get_report()
    {
        return $this->db->select('Id, firstname, lastname, birthday, address, contactno')
            ->from($this->table)
            ->order_by('lastname', 'ASC')
            ->order_by('firstname', 'ASC')
            ->get()
            ->result_array();
    }

    // Count the total number of employee records in the table
    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    // Private helper method to apply search, age range, and address filters to the query
    private function apply_filters($search, $age_range, $address)
    {
        $search = trim((string) $search);

        if ($search !== '')
        {
            $this->db->group_start()
                ->like('firstname', $search)
                ->or_like('lastname', $search)
                ->or_like('address', $search)
                ->or_like('contactno', $search)
                ->group_end();
        }

        if ($address !== '')
        {
            $this->db->like('address', $address);
        }

        $age_ranges = array(
            'under_18' => array(0, 17),
            '18_30' => array(18, 30),
            '31_40' => array(31, 40),
            '41_50' => array(41, 50),
            '51_plus' => array(51, 100000000000)
        );

        if (isset($age_ranges[$age_range]))
        {
            $range = $age_ranges[$age_range];
            $this->db->where('birthday >', date('Y-m-d', strtotime('-' . ($range[1] + 1) . ' years')));
            $this->db->where('birthday <=', date('Y-m-d', strtotime('-' . $range[0] . ' years')));
        }
    }

    // Insert a new employee record into the table
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    // Update an existing employee record by its ID
    public function update($id, $data)
    {
        return $this->db
            ->where('Id', (int) $id)
            ->update($this->table, $data);
    }

    // Delete an employee record by its ID
    public function delete($id)
    {
        return $this->db
            ->where('Id', (int) $id)
            ->delete($this->table);
    }
}
