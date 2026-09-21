<?php
/**
 * application/models/User_model.php | 2026-09-21
 * Query Builder data access for registered application users.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    private $table = 'user';

    public function find_by_email($email)
    {
        return $this->db
            ->select('Id, firstname, lastname, birthday, address, contactno, email, password')
            ->from($this->table)
            ->where('email', strtolower(trim((string) $email)))
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function email_exists($email)
    {
        return $this->db
            ->from($this->table)
            ->where('email', strtolower(trim((string) $email)))
            ->count_all_results() > 0;
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }
}
