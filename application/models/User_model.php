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
            ->select('Id, firstname, lastname, birthday, address, contactno, email, password, must_change_password, profile_picture')
            ->from($this->table)
            ->where('email', strtolower(trim((string) $email)))
            ->limit(1)
            ->get()
            ->row_array();
    }
 
    public function get_all()
    {
        return $this->db
             ->select('Id, firstname, lastname, birthday, address, contactno, email, password, must_change_password')
            ->from($this->table)
            ->order_by('lastname', 'ASC')
            ->order_by('firstname', 'ASC')
            ->get()
            ->result_array();
    }

    public function get_registration_report($start, $end)
    {
        return $this->db->select('Id, firstname, lastname, email, birthday, address, contactno, created_at')
            ->from($this->table)
            ->where('created_at >=', $start)
            ->where('created_at <', $end)
            ->order_by('created_at', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_management_users($current_user_id = 0)
    {
        return $this->db->select('Id, firstname, lastname, email, birthday, address, contactno, created_at')
            ->from($this->table)
            ->order_by('CASE WHEN Id = ' . (int) $current_user_id . ' THEN 0 ELSE 1 END', '', FALSE)
            ->order_by('lastname', 'ASC')
            ->order_by('firstname', 'ASC')
            ->get()
            ->result_array();
    }

    public function delete_user($id)
    {
        return $this->db->where('Id', (int) $id)->delete($this->table);
    }

    public function get_monthly_registration_report($start, $end)
    {
        return $this->db->select("DATE_FORMAT(created_at, '%Y-%m') AS registration_month, COUNT(*) AS total", FALSE)
            ->from($this->table)
            ->where('created_at >=', $start)
            ->where('created_at <', $end)
            ->group_by("DATE_FORMAT(created_at, '%Y-%m')", FALSE)
            ->order_by('registration_month', 'ASC')
            ->get()
            ->result_array();
    }

    public function get_age_report()
    {
        return $this->db->query("SELECT age_range, total FROM (
            SELECT 'Under 18' AS age_range, SUM(TIMESTAMPDIFF(YEAR, birthday, CURDATE()) < 18) AS total, 1 AS sort_order FROM users
            UNION ALL SELECT '18-30', SUM(TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 18 AND 30), 2 FROM users
            UNION ALL SELECT '31-40', SUM(TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 31 AND 40), 3 FROM users
            UNION ALL SELECT '41-50', SUM(TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 41 AND 50), 4 FROM users
            UNION ALL SELECT '51+', SUM(TIMESTAMPDIFF(YEAR, birthday, CURDATE()) >= 51), 5 FROM users
        ) AS age_report ORDER BY sort_order")->result_array();
    }

    public function find_by_id($id)
    {
        return $this->db
            ->select('Id, firstname, lastname, birthday, address, contactno, email, password, must_change_password, profile_picture')
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

    public function update_password_hash($id, $password_hash)
    {
        return $this->db
            ->where('Id', (int) $id)
            ->update($this->table, array('password' => $password_hash));
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

    public function update_profile($id, $data)
    {
        return $this->db->where('Id', (int) $id)->update($this->table, $data);
    }

    public function count_registered()
    {
        return $this->db->count_all($this->table);
    }

    public function count_created_between($start, $end)
    {
        return $this->db->where('created_at >=', $start)
            ->where('created_at <', $end)
            ->count_all_results($this->table);
    }

    public function age_statistics()
    {
        return $this->db->select("AVG(TIMESTAMPDIFF(YEAR, birthday, CURDATE())) AS average_age, MIN(TIMESTAMPDIFF(YEAR, birthday, CURDATE())) AS youngest_age, MAX(TIMESTAMPDIFF(YEAR, birthday, CURDATE())) AS oldest_age", FALSE)
            ->from($this->table)
            ->get()
            ->row_array();
    }

    public function age_breakdown()
    {
        return $this->db->select("SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthday, CURDATE()) < 18 THEN 1 ELSE 0 END) AS under_18, SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 18 AND 30 THEN 1 ELSE 0 END) AS age_18_30, SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 31 AND 40 THEN 1 ELSE 0 END) AS age_31_40, SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 41 AND 50 THEN 1 ELSE 0 END) AS age_41_50, SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthday, CURDATE()) >= 51 THEN 1 ELSE 0 END) AS age_51_plus", FALSE)
            ->from($this->table)
            ->get()
            ->row_array();
    }

    public function address_statistics($limit = 5)
    {
        return $this->db->select('address, COUNT(*) AS total', FALSE)
            ->from($this->table)
            ->where('address !=', '')
            ->group_by('address')
            ->order_by('total', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result_array();
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
