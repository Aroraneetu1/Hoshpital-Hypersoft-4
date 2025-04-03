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
        $uri = ltrim($uri, '/'); // Ensure no leading slash
        $base_url = base_url();

        if (strpos($base_url, 'localhost') !== false) {
            return 'http://localhost/hypersofts/' . $uri;
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

function no_access_msg(){

	return '<div class="text-center">
			    <div class="alert alert-danger p-4">
			        <h1 class="display-4">403</h1>
			        <h3 class="fw-bold">Access Denied</h3>
			        <p class="text-muted">You do not have permission to access this page.</p>
			    </div>
			    <a href="'.get_site_url('admin').'" class="btn btn-primary">Go to Homepage</a>
			</div>';
}

function get_operation_total_amt($operation_id) {
    $db = \Config\Database::connect();
    $builder = $db->table('operation_items');
    $builder->where('operation_id', $operation_id);
    $query = $builder->get();

    $total = 0;
    if ($query->getNumRows() > 0) {
        foreach ($query->getResult() as $item) {
            $total += (float) $item->subtotal;
        }
    }

    return $total;
}

function get_assets_url($uri = '')
{
    $url = base_url();

    if (strpos($url, 'localhost') !== false) {
        return 'http://localhost/hypersofts/assets/' . ltrim($uri, '/');
    }

    return base_url('assets/' . ltrim($uri, '/'));
}

function get_schedule_appointments_info($schedule)
{
    $db = \Config\Database::connect();
    $query = $db->table('appointments')
        ->where('status !=', 0)
        ->where('schedule_id', $schedule->id)
        ->get();

    $alloted_appointments = $query->getNumRows();
    $used_time = 0;

    if ($alloted_appointments > 0) {
        foreach ($query->getResult() as $appointment) {
            $used_time += $appointment->estimate_sec;
        }
    }

    $available_time = $schedule->duration_sec - $used_time;

    return [
        'alloted' => $alloted_appointments,
        'available_time' => $available_time,
    ];
}

function get_print_header(){

	$get_clinic_info = get_clinic_info();

	$header = '<div style="text-align: center;margin-bottom: 15px;">
		       <img src="'.str_replace('index.php/', '', get_site_url().''.$get_clinic_info->logo).'" alt="Hospital Logo" style="width: 150px;">
		       <!-- <h1 style="font-size: 22px;"><strong>HOSPITAL / CLINIC NAME </strong></h1> -->
		    </div>

		    <div style="text-align: center; margin-bottom: 20px;">
		        <p style="margin: 0; font-size: 18px;"><strong>'.$get_clinic_info->name.'</strong></p>
		        <p style="margin: 0; font-size: 16px;"><strong>'.$get_clinic_info->address.'</strong></p>
		        <p style="margin: 0; font-size: 14px;">'.$get_clinic_info->contact.'</p>
		    </div>';

	return $header;

}

function get_service_products() {
    $db = db_connect();
    
    $query = $db->table('products')
                ->where('is_delete', 0)
                ->get();

    $data = $query->getResult();

    $productsd = [];
    $category = get_key_value_array('category', 'id', ['type']);

    if (!empty($data)) {
        foreach ($data as $value) {
            if (isset($category[$value->category_id]) && $category[$value->category_id] == 1) {
                $productsd[$value->id] = $value->name;
            }
        }
    }

    return $productsd;
}

function get_appointments_result($appid) {
    $db = \Config\Database::connect();

    return $db->table('lab_test')
        ->where('appointment_id', $appid)
        ->where('lt_type', 0)
        ->get()
        ->getResult();
}

if ( ! function_exists('get_theme_pagination')) {



	function get_theme_pagination(){



		$data = array();



		$data['cur_tag_open'] = '<li class="disabled"><a>';



		$data['cur_tag_close'] = '<</li>';



		$data['full_tag_open'] = '<div style="padding-left:10px"><ul class="pagination">';



		$data['full_tag_close'] = '</ul></div>';



		$data['first_tag_open'] = '<li>';



		$data['first_tag_close'] = '</li>';



		$data['num_tag_open'] = '<li>';



		$data['num_tag_close'] = '</li>';



		$data['last_tag_open'] = '<li>';



		$data['last_tag_close'] = '</li>';



		$data['next_tag_open'] = '<li>';



		$data['next_tag_close'] = '</li>';



		$data['prev_tag_open'] = '<li>';



		$data['prev_tag_close'] = '</li>';



		$data['next_link'] = '&raquo;';



		$data['prev_link'] = '&laquo;';



		$data['cur_tag_open'] = '<li class="active"><a>';



		$data['cur_tag_close'] = '</a></li>';



		return $data;



	}



}

function get_country_array(){

	$countries = array('AF' => 'Afghanistan', 'AX' => 'Aland Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua And Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia And Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Congo, Democratic Republic', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'Cote D\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island & Mcdonald Islands', 'VA' => 'Holy See (Vatican City State)', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic Of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle Of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KR' => 'Korea', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States Of', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'Reunion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'BL' => 'Saint Barthelemy', 'SH' => 'Saint Helena', 'KN' => 'Saint Kitts And Nevis', 'LC' => 'Saint Lucia', 'MF' => 'Saint Martin', 'PM' => 'Saint Pierre And Miquelon', 'VC' => 'Saint Vincent And Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome And Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia And Sandwich Isl.', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard And Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad And Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks And Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'WF' => 'Wallis And Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe');

	return $countries;

}

function get_debit_payment($id) {
    $db = \Config\Database::connect();

    $debitpay = [];
    $query = $db->query("SELECT * FROM `appointments` as a JOIN payments as p ON a.id = p.appointment_id WHERE p.payment_type = 8 AND a.consumer_id = ?", [$id]);

    if ($query->getNumRows() > 0) {
        $debitpay = $query->getResult();
    }

    $remamt = 0;
    if (!empty($debitpay)) {
        foreach ($debitpay as $value) {
            $debitpayamt = 0;
            $query11 = $db->query("SELECT SUM(amount) as totdebit FROM `debit_payment` WHERE receipt_id = ?", [$value->receipt_id]);

            if ($query11->getNumRows() > 0) {
                $result11 = $query11->getRow();
                $debitpayamt = $result11->totdebit ?? 0;
            }

            $payment_type_amount = $value->payment_type_amount ?? 0;

            if ($payment_type_amount > $debitpayamt) {
                $remamt += round($payment_type_amount - $debitpayamt);
            }
        }
    }

    return $remamt;
}

function get_row($table_name = '', $id_array = [])
{
    $db = \Config\Database::connect();
    
    if (!empty($id_array)) {
        foreach ($id_array as $key => $value) {
            $db->where($key, $value);
        }
    }

    $query = $db->get($table_name);

    return $query->getNumRows() > 0 ? $query->getRow() : false;
}

if (!function_exists('get_expense_total_amt')) {
    function get_expense_total_amt($exid)
    {
        $db = \Config\Database::connect();
        $query = $db->table('expense_items')->where('expense_id', $exid)->get();

        $total = 0;
        foreach ($query->getResult() as $item) {
            $total += $item->subtotal;
        }

        return $total;
    }
}

if (!function_exists('get_expense_totpaid_amt')) {
    function get_expense_totpaid_amt($exid)
    {
        $db = \Config\Database::connect();
        $query = $db->table('expense_payment')->where('expense_id', $exid)->get();

        $total = 0;
        foreach ($query->getResult() as $item) {
            $total += $item->amount;
        }

        return $total;
    }
}

if (!function_exists('get_expense_info')) {
    function get_expense_info($exid)
    {
        $db = \Config\Database::connect();
        $query = $db->table('expenses')->where('id', $exid)->get();

        return ($query->getNumRows() > 0) ? $query->getRow() : null;
    }
}

function get_date_format(){

	return 'd F Y';

}

?>