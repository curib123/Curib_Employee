<?php
/**
 * application/models/User_model.php | 2026-09-21
 * Query Builder data access for registered application users.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    private $table = 'users';

    public function find_by_email($email)
    {
        return $this->db
            ->select('Id, firstname, lastname, birthday, address, contactno, email, password, must_change_password')
            ->from($this->table)
            ->where('email', strtolower(trim((string) $email)))
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function find_by_id($id)
    {
        return $this->db
            ->select('Id, firstname, lastname, birthday, address, contactno, email, password, must_change_password')
            ->from($this->table)
            ->where('Id', (int) $id)
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

    public function update_password_and_clear_prompt($id, $password_hash)
    {
        return $this->db
            ->where('Id', (int) $id)
            ->update($this->table, array(
                'password' => $password_hash,
                'must_change_password' => 0
            ));
    }

    public function clear_password_prompt($id)
    {
        return $this->db
            ->where('Id', (int) $id)
            ->update($this->table, array(
                'must_change_password' => 0
            ));
    }
}
