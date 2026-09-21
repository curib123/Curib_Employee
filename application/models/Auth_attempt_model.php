<?php
/**
 * application/models/Auth_attempt_model.php | 2026-09-21
 * Persistent login-throttling data access without storing plaintext email addresses.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_attempt_model extends CI_Model
{
    private $table = 'login_attempts';

    public function get_state($identifier_hash, $ip_address)
    {
        return $this->db
            ->select('identifier_hash, ip_address, attempt_count, last_attempt_at, locked_until')
            ->from($this->table)
            ->where('identifier_hash', $identifier_hash)
            ->where('ip_address', $ip_address)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function record_failure($identifier_hash, $ip_address, $max_attempts, $window_seconds, $lock_seconds)
    {
        $now = time();
        $state = $this->get_state($identifier_hash, $ip_address);
        $attempt_count = 1;

        if ($state && ($now - (int) $state['last_attempt_at']) <= $window_seconds)
        {
            $attempt_count = (int) $state['attempt_count'] + 1;
        }

        $locked_until = $attempt_count >= $max_attempts ? $now + $lock_seconds : 0;

        $data = array(
            'attempt_count' => $locked_until > 0 ? 0 : $attempt_count,
            'last_attempt_at' => $now,
            'locked_until' => $locked_until
        );

        if ($state)
        {
            return $this->db
                ->where('identifier_hash', $identifier_hash)
                ->where('ip_address', $ip_address)
                ->update($this->table, $data);
        }

        $data['identifier_hash'] = $identifier_hash;
        $data['ip_address'] = $ip_address;

        return $this->db->insert($this->table, $data);
    }

    public function clear($identifier_hash, $ip_address)
    {
        return $this->db
            ->where('identifier_hash', $identifier_hash)
            ->where('ip_address', $ip_address)
            ->delete($this->table);
    }

    public function cleanup_stale($cutoff_timestamp)
    {
        return $this->db
            ->where('last_attempt_at <', (int) $cutoff_timestamp)
            ->where('locked_until <', time())
            ->delete($this->table);
    }
}
