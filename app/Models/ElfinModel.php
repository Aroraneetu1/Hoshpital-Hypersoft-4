<?php

namespace App\Models;

use CodeIgniter\Model;

class ElfinModel extends Model
{
    protected $db;
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username', 'password', 'status', 'is_delete', 'email', 'new_email',
        'created', 'activated', 'last_ip', 'last_login', 'banned', 'ban_reason'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // User login
    public function login($username = '', $password = '', $role = 1)
    {
        $session = session();

        $query = $this->db->table($this->table)
            ->where('username', $username)
            ->where('password', sha1($password))
            ->where('status', 1)
            ->where('is_delete', 0)
            ->get();

        if ($query->getNumRows() > 0) {
            $info = $query->getRow();
            $session->set('user_session', (array)$info);

            // Update last login details
            $this->db->table($this->table)
                ->where('id', $info->id)
                ->update([
                    'last_ip' => service('request')->getIPAddress(),
                    'last_login' => time(),
                ]);
        } else {
            $session->setFlashdata('error_msg', 'Invalid username or password.');
        }
    }

    // Check if email is available
    public function is_email_available($email)
    {
        $query = $this->db->table($this->table)
            ->select('1', false)
            ->groupStart()
                ->where('LOWER(email)', strtolower($email))
                ->orWhere('LOWER(new_email)', strtolower($email))
            ->groupEnd()
            ->get();

        return $query->getNumRows() == 0;
    }

    // Create a new user
    public function create_user($data, $activated = true)
    {
        $data['created'] = date('Y-m-d H:i:s');
        $data['activated'] = $activated ? 1 : 0;

        if ($this->insert($data)) {
            $user_id = $this->insertID();
            if ($activated) {
                $this->create_profile($user_id);
            }
            return ['user_id' => $user_id];
        }
        return null;
    }

    // Activate a user account
    public function activate_user($user_id, $activation_key, $activate_by_email)
    {
        $query = $this->db->table($this->table)
            ->select('1', false)
            ->where('id', $user_id)
            ->where($activate_by_email ? 'new_email_key' : 'new_password_key', $activation_key)
            ->where('activated', 0)
            ->get();

        if ($query->getNumRows() == 1) {
            $this->db->table($this->table)
                ->where('id', $user_id)
                ->update([
                    'activated' => 1,
                    'new_email_key' => null,
                ]);

            $this->create_profile($user_id);
            return true;
        }

        return false;
    }

    // Ban a user
    public function ban_user($user_id, $reason = null)
    {
        $this->db->table($this->table)
            ->where('id', $user_id)
            ->update([
                'banned' => 1,
                'ban_reason' => $reason,
            ]);
    }

    // Unban a user
public function unban_user($user_id)
{
    $this->db->table($this->table)
        ->where('id', $user_id)
        ->update([
            'banned' => 0,
            'ban_reason' => null,
        ]);
}

// Set password reset key
public function set_password_key($user_id, $new_pass_key)
{
    $this->db->table($this->table)
        ->where('id', $user_id)
        ->update([
            'new_password_key' => $new_pass_key,
            'new_password_requested' => date('Y-m-d H:i:s'),
        ]);

    return $this->db->affectedRows() > 0;
}

// Check if password can be reset
public function can_reset_password($user_id, $new_pass_key, $expire_period = 900)
{
    $query = $this->db->table($this->table)
        ->select('1', false)
        ->where('id', $user_id)
        ->where('new_password_key', $new_pass_key)
        ->where('UNIX_TIMESTAMP(new_password_requested) >', time() - $expire_period)
        ->get();

    return $query->getNumRows() == 1;
}

public function reset_password($user_id, $new_pass, $new_pass_key, $expire_period = 900)
    {
        return $this->db->table($this->table)
            ->where('ID', $user_id)
            ->where('new_password_key', $new_pass_key)
            ->where('UNIX_TIMESTAMP(new_password_requested) >=', time() - $expire_period)
            ->update([
                'password' => $new_pass,
                'new_password_key' => null,
                'new_password_requested' => null,
            ]);
    }

    public function change_password($user_id, $new_pass)
    {
        return $this->db->table($this->table)
            ->where('ID', $user_id)
            ->update(['password' => $new_pass]);
    }

    public function delete_user($user_id)
    {
        $this->db->table($this->table)
            ->where('ID', $user_id)
            ->delete();

        if ($this->db->affectedRows() > 0) {
            return true;
        }
        return false;
    }

    public function get_pagination_where($table_name = '', $limit = '', $offset = '', $condition = '')
    {
        $builder = $this->db->table($table_name);

        if (!empty($condition)) {
            foreach ($condition as $key => $value) {
                $builder->where($key, $value);
            }
        }

        $builder->orderBy('id', 'DESC');

        if ($limit > 0 && $offset >= 0) {
            $query = $builder->limit($limit, $offset)->get();
            return ($query->getNumRows() > 0) ? $query->getResult() : false;
        } else {
            return $builder->countAllResults();
        }
    }

    public function insert_data($table_name = '', $data = '')
    {
        $query = $this->db->table($table_name)->insert($data);
        return $query ? $this->db->insertID() : false;
    }

    public function get_result($table_name = '', $where = [], $orWhere = [], $orderBy = [])
{
    $builder = $this->db->table($table_name);

    if (!empty($where)) {
        $builder->where($where);
    }

    if (!empty($orWhere)) {
        $builder->orWhere($orWhere);
    }

    if (!empty($orderBy)) {
        foreach ($orderBy as $column => $direction) {
            $builder->orderBy($column, $direction);
        }
    }

    $query = $builder->get();

    return ($query->getNumRows() > 0) ? $query->getResult() : false;
}

    public function get_row($table_name = '', $id_array = '')
    {
        $builder = $this->db->table($table_name);

        if (!empty($id_array)) {
            foreach ($id_array as $key => $value) {
                $builder->where($key, $value);
            }
        }

        $query = $builder->get();

        return ($query->getNumRows() > 0) ? $query->getRow() : false;
    }

    public function update_data($table_name = '', $data = '', $id_array = '')
{
    $builder = $this->db->table($table_name);

    if (!empty($id_array)) {
        foreach ($id_array as $key => $value) {
            $builder->where($key, $value);
        }
    }

    return $builder->update($data);
}


    public function delete($table_name = '', $id_array = '')
    {
        return $this->db->table($table_name)->delete($id_array);
    }

    public function get_pagination_result($table_name = '', $limit = '', $offset = '', $id_array = '')
    {
        $builder = $this->db->table($table_name);

        if (!empty($id_array)) {
            foreach ($id_array as $key => $value) {
                $builder->where($key, $value);
            }
        }

        $builder->orderBy('id', 'DESC');

        if ($limit > 0 && $offset >= 0) {
            $query = $builder->limit($limit, $offset)->get();
            return ($query->getNumRows() > 0) ? $query->getResult() : false;
        } else {
            return $builder->countAllResults();
        }
    }



    // not used functions 

    // Get count of providers
    public function get_providers_count()
    {
        return $this->db->table($this->table)
            ->where('is_delete', 0)
            ->where('role', 'DOCTOR')
            ->countAllResults();
    }

    // Get count of consumers (patients)
    public function get_consumers_count()
    {
        return $this->db->table('consumers')->countAllResults();
    }

    // Get count of appointments
    public function get_appointments_count($status = false)
    {
        $builder = $this->db->table('appointments');
        if ($status !== false) {
            $builder->where('status', $status);
        }
        return $builder->countAllResults();
    }

    // Get timezone list
    public function get_timezone_array()
    {
        $timezoneIdentifiers = \DateTimeZone::listIdentifiers();
        $utcTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $tempTimezones = [];

        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $currentTimezone = new \DateTimeZone($timezoneIdentifier);
            $tempTimezones[] = [
                'offset' => (int)$currentTimezone->getOffset($utcTime),
                'identifier' => $timezoneIdentifier
            ];
        }

        usort($tempTimezones, function ($a, $b) {
            return ($a['offset'] == $b['offset'])
                ? strcmp($a['identifier'], $b['identifier'])
                : $a['offset'] - $b['offset'];
        });

        $timezoneList = [];
        foreach ($tempTimezones as $tz) {
            $sign = ($tz['offset'] > 0) ? '+' : '-';
            $offset = gmdate('H:i', abs($tz['offset']));
            $timezoneList[$tz['identifier']] = '(UTC ' . $sign . $offset . ') ' . $tz['identifier'];
        }

        return $timezoneList;
    }

}
