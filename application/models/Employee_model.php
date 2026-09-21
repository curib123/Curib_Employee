<?php
/**
 * application/models/Employee_model.php | 2026-09-21
 * Query Builder data access for the "employee" table.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_model extends CI_Model
{
    private $table = 'employee';

    public function get_all()
    {
        return $this->db
            ->select('Id, firstname, lastname, birthday, address, contactno')
            ->from($this->table)
            ->order_by('lastname', 'ASC')
            ->order_by('firstname', 'ASC')
            ->get()
            ->result_array();
    }

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

    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db
            ->where('Id', (int) $id)
            ->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db
            ->where('Id', (int) $id)
            ->delete($this->table);
    }
}
