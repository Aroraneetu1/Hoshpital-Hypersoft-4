<?php
use Config\Services; 
use Config\Database;

function get_the_current_user($role = 1, $field = '')
{
    $session = Services::session();
    $userSession = $session->get('user_session');

    if (isset($userSession['role'])) {
        if ($field == '') {
            return $userSession; 
        } else {
            return $userSession[$field] ?? false;
        }
    }

    return false;
}


function cms_current_url()
{
    $request = Services::request();
    $currentUri = $request->getUri()->getPath(); 

    return base_url($currentUri);
}

if (!function_exists('alert')) {
    function alert()
    {
        $session = session(); // Get the session instance

        if ($session->getFlashdata('success_msg')) {
            echo success_alert($session->getFlashdata('success_msg'));
        }

        if ($session->getFlashdata('error_msg')) {
            echo error_alert($session->getFlashdata('error_msg'));
        }

        if ($session->getFlashdata('info_msg')) {
            echo info_alert($session->getFlashdata('info_msg'));
        }
    }
}

function get_site_name(){

	return 'Hospital Management';

}

if (!function_exists('set_timezone')) {
    function set_timezone()
    {
        // Fetch timezone from database or config
        $server_timezone = get_option('timezone', 'Etc/Greenwich');

        // Set the default timezone
        date_default_timezone_set($server_timezone);
    }
}

if (!function_exists('get_option')) {
    function get_option($option = '', $default = '')
    {
        $db = \Config\Database::connect(); // Get the database connection
        $query = $db->table('options')->where('option', $option)->get();

        if ($query->getNumRows() > 0) {
            $row = $query->getRow();
            return $row->value;
        }

        return $default;
    }
}

if (!function_exists('get_session_data')) {
    function get_session_data($field = '')
    {
        $session = session()->get('user_session');

        if (!$session) {
            return null; // Return null if session doesn't exist
        }

        return ($field === '') ? $session : ($session[$field] ?? null);
    }
}

if (!function_exists('get_site_url')) {
    function get_site_url($uri = '')
    {
        $url = base_url($uri); // Get base URL with optional URI

        if (strpos($url, 'localhost') !== false) {
            return 'http://localhost:' . $_SERVER['SERVER_PORT'] . '/' . $uri;
        }

        return base_url($uri); // For production
    }
}

if (!function_exists('get_providers_count')) {
    function get_providers_count()
    {
        $db = \Config\Database::connect(); // Get database connection
        $builder = $db->table('users'); // Load 'users' table
        
        $builder->where('is_delete', 0);
        $builder->where('role', 'DOCTOR');

        return $builder->countAllResults(); // Count number of matching records
    }
}

if (!function_exists('get_appointments_count')) {
    function get_appointments_count($status = FALSE)
    {
        $db = \Config\Database::connect(); // Get database connection
        $builder = $db->table('appointments'); // Load 'appointments' table

        if ($status !== FALSE) {
            $builder->where('status', $status);
        }

        return $builder->countAllResults(); // Count number of matching records
    }
}

if (!function_exists('get_consumers_count')) {
    function get_consumers_count()
    {
        $db = \Config\Database::connect(); // Get database connection
        $builder = $db->table('consumers'); // Load 'consumers' table

        return $builder->countAllResults(); // Count number of records
    }
}

if (!function_exists('get_timezone_array')) {
    function get_timezone_array()
    {
        $timezoneIdentifiers = DateTimeZone::listIdentifiers();
        $utcTime = new DateTime('now', new DateTimeZone('UTC'));

        $tempTimezones = [];

        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $currentTimezone = new DateTimeZone($timezoneIdentifier);
            $tempTimezones[] = [
                'offset' => (int) $currentTimezone->getOffset($utcTime),
                'identifier' => $timezoneIdentifier
            ];
        }

        // Sort timezones by offset and then alphabetically
        usort($tempTimezones, function ($a, $b) {
            return ($a['offset'] === $b['offset'])
                ? strcmp($a['identifier'], $b['identifier'])
                : $a['offset'] - $b['offset'];
        });

        $timezoneList = [];

        foreach ($tempTimezones as $tz) {
            $sign = ($tz['offset'] >= 0) ? '+' : '-';
            $offset = gmdate('H:i', abs($tz['offset']));
            $timezoneList[$tz['identifier']] = "(UTC $sign$offset) {$tz['identifier']}";
        }

        return $timezoneList;
    }
}

if (!function_exists('success_alert')) {
    function success_alert($msg = '') {
        return '<div class="alert alert-success ci_alert alert-dismissable">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <strong>Success!</strong> ' . esc($msg) . '
                </div>';
    }
}

if (!function_exists('get_key_value_array')) {
    function get_key_value_array(string $tableName, string $key, array $valueArray)
    {
        $db = \Config\Database::connect(); 
        $array = [];

        $query = $db->table($tableName)->get();

        if ($query->getNumRows() > 0) {
            $rows = $query->getResultArray();

            foreach ($rows as $row) {
                $temp = [];

                foreach ($valueArray as $value) {
                    $temp[] = $row[$value];
                }

                $array[$row[$key]] = implode(' ', $temp);
            }
        }

        return $array;
    }
}

if (!function_exists('get_appointments_pay_status')) {
    function get_appointments_pay_status($appid)
    {
        $db = Database::connect(); // Get the database connection
        $builder = $db->table('lab_test');
        $builder->where('appointment_id', $appid);
        $query = $builder->get();

        $data = $query->getResult();

        $payStatus = [];

        if (!empty($data)) {
            foreach ($data as $value) {
                if ($value->lt_type == 1) {
                    $receipt_no = $value->receipt_no;
                    $lt_price = $value->lt_price;

                    $payment = sum_payments($receipt_no); // Make sure this function is available

                    if ((float)$payment === (float)$lt_price) {
                        $payStatus[] = 1; // Paid
                    } else {
                        $payStatus[] = 0; // Unpaid
                    }
                } else {
                    $payStatus[] = $value->payment_status;
                }
            }
        }

        return $payStatus;
    }
}

if (!function_exists('sum_payments')) {
    function sum_payments($receid)
    {
        $db = Database::connect(); // Get the database connection
        $builder = $db->table('payments');
        $builder->where('receipt_id', $receid);
        $query = $builder->get();

        $data = $query->getResult();

        $amounts = 0;

        if (!empty($data)) {
            foreach ($data as $value) {
                if (!empty($value->payment_type_amount)) {
                    $amounts += (float) $value->payment_type_amount;
                }
            }
        }

        return number_format($amounts, 2, '.', '');
    }
}

if (!function_exists('get_appointments_lab_pay_status')) {
    function get_appointments_lab_pay_status($appid)
    {
        $db = Database::connect(); // Get database connection
        $builder = $db->table('lab_test');
        $builder->where('appointment_id', $appid);
        $builder->where('lt_type', 0);
        $query = $builder->get();

        $data = $query->getResult(); // Fetch results

        $totpay = 0;
        $receipt_no = [];

        if (!empty($data)) {
            $sumall = 0;

            foreach ($data as $value) {
                $receipt_no[$value->receipt_no] = $value->receipt_no;
                $sumall += (float) $value->lt_price;
            }

            $totpay = $sumall;
        }

        $labstatus = [];

        if (!empty($receipt_no)) {
            $payment = 0;

            foreach ($receipt_no as $value) {
                $payment += sum_payments($value); // Calls sum_payments() helper function
            }

            $labstatus[] = ((float) $payment === (float) $totpay) ? 1 : 0;
        }

        return $labstatus;
    }
}

function get_inpatients_total_amt($inpid)
{
    $db = \Config\Database::connect();
    $builder = $db->table('inpatients_items');
    $builder->where('inpatients_id', $inpid);
    $query = $builder->get();

    $tot = 0;
    if ($query->getNumRows() > 0) {
        $expense_items = $query->getResult();
        foreach ($expense_items as $vv) {
            $tot += (float) $vv->subtotal; 
        }
    }

    return $tot;
}

if (!function_exists('get_clinic_info')) {
    function get_clinic_info()
    {
        $db = \Config\Database::connect();
        $query = $db->table('clinicInfo')->get();
        return $query->getRow();
    }
}


if (!function_exists('get_patient_data')) {
    function get_patient_data($pid)
    {
        $db = Database::connect();
        $query = $db->table('consumers')->where('id', $pid)->get();

        if ($query->getNumRows() > 0) {
            return $query->getRow();
        }

        return null;
    }
}

if (!function_exists('get_appointment_data')) {
    function get_appointment_data($apid)
    {
        $db = Database::connect(); 
        $query = $db->table('appointments')->where('id', $apid)->get();

        if ($query->getNumRows() > 0) {
            return $query->getRow();
        }

        return null; 
    }
}

function get_formated_id($prefix, $id){

	return $prefix.'ID'.str_pad($id, 3, '0', STR_PAD_LEFT);

}

?>