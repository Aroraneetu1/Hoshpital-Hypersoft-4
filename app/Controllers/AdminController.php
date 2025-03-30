<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Session\Session;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Config\Database;
use App\Models\ElfinModel;

class AdminController extends BaseController
{
    protected $session;
    protected $elfin_model;
    protected $db;

    public function __construct()
    {
        helper(['url', 'form','email','elfin_helper']);

        $this->session = session(); 
        $this->elfin_model = new ElfinModel();
        $this->db = Database::connect();
        set_timezone();
        if (!get_the_current_user(1)) {
            return redirect()->to(get_site_url('login/admin'))->send();
        }
    }


    public function index()
    {
        $data['template'] = 'admin/dashboard';
        return view('templates/admin_template', $data);
    }

    public function settings()
    {
        $data['template'] = 'admin/settings';
        return view('templates/admin_template', $data);
    }

    public function profile()
    {
        $admin_id = get_the_current_user(1, 'id');
        $data['admin'] = $this->elfin_model->get_row('users', ['id' => $admin_id]);

        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => "required|valid_email|is_unique[users.email,id,{$admin_id}]",
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required'
        ]);

        if ($this->request->is('post') && $validation->withRequest($this->request)->run()) {
            $updateData = [
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone')
            ];

            $this->elfin_model->update_data('users', $updateData, ['id' => $admin_id]);
            $this->session->setFlashdata('success_msg', 'Profile has been updated successfully.');

            $row = $this->elfin_model->get_row('users', ['id' => $admin_id]);
            $this->session->set('admin_session', $row);

            return redirect()->to(get_site_url('admin/profile'));
        }

        $data['template'] = 'admin/profile';
        return view('templates/admin_template', $data);
    }

    public function change_password()
    {
        $admin_id = get_the_current_user(1, 'id');
        $admin = $this->elfin_model->get_row('users', ['id' => $admin_id]);

        if (!$admin) {
            return redirect()->to(get_site_url('admin'))->with('error_msg', 'User not found');
        }

        $oldpassword = $admin->password;
        $opwd = $this->request->getPost('opwd');
        $npwd = $this->request->getPost('npwd');
        $cpwd = $this->request->getPost('cpwd');

        if ($oldpassword === sha1($opwd)) {
            if ($npwd === $cpwd) {
                $data = ['password' => sha1($npwd)];
                $this->elfin_model->update_data('users', $data, ['id' => $admin_id]);

                return redirect()->to(get_site_url('admin'))->with('success_msg', 'Password has been changed successfully.');
            } else {
                return redirect()->to(get_site_url('admin'))->with('error_msg', 'New password and confirm password do not match.');
            }
        } else {
            return redirect()->to(get_site_url('admin'))->with('error_msg', 'Old password is incorrect.');
        }
    }

    public function user_list()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->where('is_delete', 0);
        $builder->whereIn('role', ['Receptionist', 'Lab', 'Finance', 'Cashier', 'Doctor-User']);
        $builder->orderBy('id', 'DESC');
        $query = $builder->get();
        
        $data = [
            'users' => $query->getResult(),
            'template' => 'admin/user_list',
            'menu' => 'admin',
            'active' => 'user_list'
        ];
    
        return view('templates/admin_template', $data);
    }

    public function user_staff()
    {
        $db = \Config\Database::connect();
        $validation = \Config\Services::validation();

        $validation->setRules([
            'first_name' => 'required',
            'last_name'  => 'required',
            'role'       => 'required',
            'username'   => 'required|is_unique[users.username]',
            'password'   => 'required|min_length[6]'
        ]);

        if ($this->request->is('post') && $validation->withRequest($this->request)->run()) {
            $data = [
                'role'        => $this->request->getPost('role'),
                'status'      => 1,
                'first_name'  => $this->request->getPost('first_name'),
                'last_name'   => $this->request->getPost('last_name'),
                'username'    => $this->request->getPost('username'),
                'password'    => sha1($this->request->getPost('password')),
                'permissions' => serialize($this->request->getPost('permissions')),
            ];

            $db->table('users')->insert($data);
            session()->setFlashdata('success_msg', 'User added successfully.');
            return redirect()->to(get_site_url('admin/user-list'));
        }

        return view('templates/admin_template', [
            'template' => 'admin/user_staff',
            'menu'     => 'admin',
            'active'   => 'user_staff'
        ]);
    }

    public function edit_user($id = null)
    {
        if (!$id) {
            return redirect()->to(get_site_url('admin/user_list'))->with('error_msg', 'Invalid User ID');
        }

        $data['users'] = $this->elfin_model->get_row('users', ['id' => $id]);

        if (!$data['users']) {
            return redirect()->to(get_site_url('admin/user_list'))->with('error_msg', 'User not found');
        }

        if ($this->request->is('post')) {
            $rules = [
                'first_name' => 'required',
                'last_name'  => 'required'
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validation;
            } else {
                $updateData = [
                    'role'        => $this->request->getPost('role'),
                    'first_name'  => $this->request->getPost('first_name'),
                    'last_name'   => $this->request->getPost('last_name'),
                    'permissions' => serialize($this->request->getPost('permissions'))
                ];

                $this->elfin_model->update_data('users', $updateData, ['id' => $id]);
                return redirect()->to(get_site_url('admin/user_list'))->with('success_msg', 'User updated successfully.');
            }
        }
        $data['template'] = 'admin/user_staff_edit';
        return view('templates/admin_template', $data);
    }

    public function username_check($username)
    {
        $query = $this->db->table('users')->where('username', $username)->get();
        if ($query->getNumRows() > 0) {
            return false;
        }
        return true;
    }

    public function delete($id = null)
    {
        if ($id) {
            $this->elfin_model->update_data('users', ['is_delete' => 1], ['id' => $id]);
            $this->session->setFlashdata('success_msg', 'Deleted successfully.');
        }
        return redirect()->to(get_site_url('admin/user_list'));
    }

    public function change_timezone()
    {
        $timezone = $this->request->getPost('timezone');
        if ($timezone) {
            // Assuming set_option is a helper function to update the setting
            set_option('timezone', $timezone);
            $this->session->setFlashdata('success_msg', 'Timezone has been changed successfully.');
        }
        return redirect()->to(get_site_url('admin/settings'));
    }

    public function logout()
    {
        $this->session->remove('user_session');
        $this->session->setFlashdata('success_msg', 'Logout successfully.');
        return redirect()->to(get_site_url('login/admin'));
    }

    public function checkIsEmailUnique($newEmail, $oldEmail)
    {
        if ($oldEmail === $newEmail) {
            return true;
        }

        $resp = $this->elfin_model->getRow('users', ['email' => $newEmail]);

        if ($resp) {
            $this->validation->setError('checkIsEmailUnique', 'Email you are choosing already exists.');
            return false;
        }
        
        return true;
    }

    public function checkOldPassword($oldPwd, $refPwd)
    {
        if (sha1($oldPwd) === $refPwd) {
            return true;
        }

        $this->validation->setError('checkOldPassword', 'Old password is incorrect.');
        return false;
    }

    public function report()
    {
        $from = $this->request->getGet('from') ?? date('d F Y');
        $to = $this->request->getGet('to') ?? date('d F Y');

        $timeFrom = strtotime($from);
        $timeTo = strtotime($to) + 86400;

        $query = $this->elfin_model->db->table('appointments')
            ->where('visited_start_time >=', $timeFrom)
            ->where('visited_start_time <=', $timeTo)
            ->whereIn('status', [2, 3])
            ->orderBy('id', 'DESC')
            ->get();

        $data = [
            'rows' => $query->getResult(),
            'from' => $from,
            'to' => $to,
            'template' => 'admin/report',
            'menu' => 'settings',
            'active' => 'report'
        ];

        return view('templates/admin_template', $data);
    }

    public function debitPayment()
    {
        $from = $this->request->getGet('from') ?? date('d F Y');
        $to = $this->request->getGet('to') ?? date('d F Y');

        $fromDate = date('Y-m-d', strtotime($from));
        $toDate = date('Y-m-d', strtotime($to));

        $query = $this->elfin_model->db->table('debit_payment')
            ->where('paid_at >=', $fromDate)
            ->where('paid_at <=', $toDate)
            ->orderBy('id', 'DESC')
            ->get();

        $data = [
            'sales2' => $query->getResult(),
            'from' => $from,
            'to' => $to,
            'template' => 'admin/debit_payment',
            'menu' => 'settings',
            'active' => 'debit_payment'
        ];

        return view('templates/admin_template', $data);
    }

    public function salesReport()
    {
        helper('date');

        // Get date range from request, fallback to today’s date
        $data['from'] = $this->request->getGet('from') ?? date('d F Y');
        $data['to'] = $this->request->getGet('to') ?? date('d F Y');

        $fromDate = date('Y-m-d', strtotime($data['from']));
        $toDate = date('Y-m-d', strtotime($data['to']));

        // Get sales3 data from `debit_payment`
        $data['sales3'] = $this->elfin_model->get_result('debit_payment');

        // Fetch lab test sales data
        $sales2 = [];
        $payments = [];

        $query = $this->db->query("
            SELECT *, SUM(lt_price) as subtot 
            FROM lab_test 
            WHERE paid_at >= '$fromDate' 
                AND paid_at <= '$toDate' 
                AND payment_status = 1 
                AND is_delete = 0 
            GROUP BY receipt_no 
            ORDER BY id DESC
        ");

        if ($query->getNumRows() > 0) {
            $sales2 = $query->getResult();

            foreach ($sales2 as $sale) {
                $paymentQuery = $this->db->query("
                    SELECT * FROM payments WHERE receipt_id = '{$sale->receipt_no}'
                ");

                foreach ($paymentQuery->getResult() as $payment) {
                    $payments[$payment->payment_type] = ($payments[$payment->payment_type] ?? 0) + $payment->payment_type_amount;
                }
            }
        }

        // Fetch debit payments
        $querypdb = $this->db->query("
            SELECT * FROM debit_payment 
            WHERE paid_at >= '$fromDate' 
                AND paid_at <= '$toDate'
        ");

        foreach ($querypdb->getResult() as $payment) {
            $payments[$payment->payment_type] = ($payments[$payment->payment_type] ?? 0) + $payment->amount;
        }

        // Fetch inpatient & outpatient payments
        $inppay = [];
        $qrypb1 = $this->db->query("
            SELECT * FROM payments 
            WHERE DATE(payment_datetime) >= '$fromDate' 
                AND DATE(payment_datetime) <= '$toDate' 
                AND pay_from IN ('INP', 'OPS')
        ");

        if ($qrypb1->getNumRows() > 0) {
            $inppay = $qrypb1->getResult();

            foreach ($inppay as $payment) {
                $payments[$payment->payment_type] = ($payments[$payment->payment_type] ?? 0) + $payment->payment_type_amount;
            }
        }

        // Store retrieved data in $data array
        $data['sales2'] = $sales2;
        $data['payments'] = $payments;
        $data['inppay'] = $inppay;
        $data['template'] = 'admin/sales_report';
        $data['menu'] = 'settings';
        $data['active'] = 'sales_report';

        return view('templates/admin_template', $data);
    }

    public function expenseReport()
    {
        $data['from'] = $this->request->getGet('from') ?? date('d F Y');
        $data['to'] = $this->request->getGet('to') ?? date('d F Y');
    
        $fromDate = date('Y-m-d', strtotime($data['from']));
        $toDate = date('Y-m-d', strtotime($data['to']));
    
        $db = \Config\Database::connect();
    
        $query = $db->query("
            SELECT * FROM expenses e 
            JOIN expense_items ei ON e.id = ei.expense_id 
            WHERE e.expense_date >= '$fromDate' 
              AND e.expense_date <= '$toDate'
        ");
    
        $data['productsItems'] = $query->getResult();
        $data['template'] = 'admin/expense_report';
        $data['menu'] = 'settings';
        $data['active'] = 'expense_report';
    
        return view('templates/admin_template', $data);
    }

    public function exportSalesReport()
{
    $headerArray = [
        'Receipt #',
        'Patient Name',
        'Payment Status',
        'Payment By',
        'Amount',
    ];
    
    $header = implode("\t", $headerArray) . "\n";

    // Connect to database
    $db = \Config\Database::connect();
    $builder = $db->table('consumers')
                  ->where('status', 1)
                  ->orderBy('first_name', 'ASC')
                  ->limit(1000);

    $query = $builder->get();
    $rows = $query->getResult();

    $body = [];

    if ($rows) {
        foreach ($rows as $row) {
            $dataArray = [
                $row->first_name . ' ' . $row->middle_name . ' ' . $row->last_name,
                $row->dob,
                $row->gender,
                $row->address,
                $row->city,
                $row->state,
                $row->postal_code,
                $row->phone_number,
                $row->alternate_phone_number,
            ];
            $body[] = implode("\t", $dataArray);
        }
    }

    $body = implode("\n", $body);

    // Set headers for file download
    return $this->response
        ->setHeader('Content-Type', 'application/octet-stream')
        ->setHeader('Content-Disposition', 'attachment; filename="patients.xls"')
        ->setHeader('Pragma', 'no-cache')
        ->setHeader('Expires', '0')
        ->setBody($header . "\n" . $body);
}    

public function doctorReport()
{
    return view('templates/admin_template', [
        'template' => 'admin/doctor_report',
        'menu' => 'settings',
        'active' => 'doctor_report'
    ]);
}

public function managementReport()
{
    return view('templates/admin_template', [
        'template' => 'admin/management_report',
        'menu' => 'settings',
        'active' => 'management_report'
    ]);
}

public function serviceReport()
{
    return view('templates/admin_template', [
        'template' => 'admin/service_report',
        'menu' => 'settings',
        'active' => 'service_report'
    ]);
}

public function paymentReport()
{
    return view('templates/admin_template', [
        'template' => 'admin/payment_report',
        'menu' => 'settings',
        'active' => 'payment_report'
    ]);
}

public function smsPaymentReport()
{
    return view('templates/admin_template', [
        'template' => 'admin/sms_payment_report',
        'menu' => 'settings',
        'active' => 'sms_payment_report'
    ]);
}

public function paymentTypes()
{
    $query = $this->db->table('payment_types')
        ->where('is_delete', 0)
        ->orderBy('id', 'ASC')
        ->get();

    $data['payment_types'] = $query->getResult();

    return view('templates/admin_template', [
        'payment_types' => $data['payment_types'],
        'template' => 'admin/all_payment_type'
    ]);
}

public function addPaymentType()
{
    $validation = \Config\Services::validation();

    $rules = [
        'name' => [
            'label' => 'Payment Type Name',
            'rules' => 'required',
            'errors' => [
                'required' => 'The {field} field is required.'
            ]
        ]
    ];

    if ($this->request->is('post')) {
        if ($this->validate($rules)) {
            $data = [
                'name' => $this->request->getPost('name')
            ];

            if ($this->elfin_model->insert_data('payment_types', $data)) {
                session()->setFlashdata('success_msg', 'Payment type added successfully.');
                return redirect()->to(get_site_url('admin/paymentTypes'));
            } else {
                session()->setFlashdata('error_msg', 'Failed to add payment type.');
            }
        } else {
            session()->setFlashdata('validation', $this->validator);
        }
    }

    return view('templates/admin_template', [
        'template' => 'admin/add_payment_type',
        'validation' => session('validation') // Pass validation errors to the view
    ]);
}

public function editPaymentType($id = '')
{
    $validation = \Config\Services::validation();
    $validation->setRules([
        'name' => 'required'
    ]);

    if ($this->request->is('post') && $validation->withRequest($this->request)->run()) {
        $data = [
            'name' => $this->request->getPost('name')
        ];

        $this->elfin_model->update_data('payment_types', $data, ['id' => $id]);
        session()->setFlashdata('success_msg', 'Payment type updated successfully.');
        return redirect()->to(get_site_url('admin/paymentTypes'));
    }

    return view('templates/admin_template', [
        'row' => $this->elfin_model->get_row('payment_types', ['id' => $id]),
        'template' => 'admin/edit_payment_type'
    ]);
}

public function deletePaymentType()
{
    $id = $this->request->getPost('id');
    $this->elfin_model->update_data('payment_types', ['is_delete' => 1], ['id' => $id]);
    echo 1;
}

public function deleteRoom()
{  
    $id = $this->request->getPost('id');
    $this->elfin_model->update_data('rooms', ['is_delete' => 1], ['id' => $id]);
	echo 1;
}

public function rooms()
{
    $data['rooms'] = $this->elfin_model->get_result('rooms',['is_delete' => 0]);
    $data['current_segment'] = service('request')->getUri()->getSegment(2);

    return view('templates/admin_template', [
        'template' => 'admin/rooms',
        'rooms' => $data['rooms']
    ]);
}    
    public function arooms()
    {
        $rules = [
            'room_name'     => 'required',
            'room_number'   => 'required',
            'room_category' => 'required',
            'room_rate'     => 'required'
        ];

        if ($this->request->is('post') && $this->validate($rules)) {
            $data = [
                'room_name'     => $this->request->getPost('room_name'),
                'room_number'   => $this->request->getPost('room_number'),
                'room_category' => $this->request->getPost('room_category'),
                'description'   => $this->request->getPost('description'),
                'room_rate'     => $this->request->getPost('room_rate'),
                'status'        => 'Available'
            ];

            $this->elfin_model->insert_data('rooms', $data);
            $this->session->setFlashdata('success_msg', 'Room added successfully.');
            return redirect()->to(get_site_url('admin/rooms'));
        }

        $roomCategories = $this->elfin_model->get_result('room_categories', ['is_delete' => 0]);

        return view('templates/admin_template', [
            'template' => 'admin/rooms_add',
            'roomc' => $roomCategories
        ]);
    }

    public function erooms($id = '')
    {
        $rules = [
            'room_name'     => 'required',
            'room_number'   => 'required',
            'room_category' => 'required',
            'room_rate'     => 'required'
        ];

        if ($this->request->is('post') && $this->validate($rules)) {
            $data = [
                'room_name'     => $this->request->getPost('room_name'),
                'room_number'   => $this->request->getPost('room_number'),
                'room_category' => $this->request->getPost('room_category'),
                'description'   => $this->request->getPost('description'),
                'room_rate'     => $this->request->getPost('room_rate')
            ];

            $this->elfin_model->update_data('rooms', $data, ['id' => $id]);
            $this->session->setFlashdata('success_msg', 'Room updated successfully.');
            return redirect()->to(get_site_url('admin/rooms'));
        }

        $room = $this->elfin_model->get_row('rooms', ['id' => $id]);
        $roomCategories = $this->elfin_model->get_result('room_categories', ['is_delete' => 0]);

        return view('templates/admin_template', [
            'template' => 'admin/rooms_edit',
            'row' => $room,
            'roomc' => $roomCategories
        ]);
    }

    public function crooms()
    {
        $roomCategories = $this->elfin_model->get_result('room_categories', ['is_delete' => 0]);

        return view('templates/admin_template', [
            'template' => 'admin/rooms_category',
            'roomc' => $roomCategories
        ]);
    }

    public function croomsadd()
    {
        $rules = [
            'name' => [
                'label' => 'Room Category Name',
                'rules' => 'required',
                'errors' => ['required' => 'The {field} field is required.']
            ]
        ];

        if ($this->request->is('post')) {
            if ($this->validate($rules)) {
                $data = ['name' => $this->request->getPost('name')];

                $this->elfin_model->insert_data('room_categories', $data);
                $this->session->setFlashdata('success_msg', 'Room category added successfully.');
                return redirect()->to(get_site_url('admin/crooms'));
            } else {
                $this->session->setFlashdata('validation', $this->validator);
            }
        }

        return view('templates/admin_template', [
            'template' => 'admin/rooms_category_add',
            'validation' => session('validation')
        ]);
    }

    public function delete_room_category()
    {
            $id = $this->request->getPost('id');

            $this->elfin_model->update_data('room_categories', ['is_delete' => 1], ['id' => $id]);
            echo 1;
    }

    public function category()
{
    $data['category'] = $this->elfin_model->get_result('category', ['is_delete' => 0]);

    return view('templates/admin_template', [
        'template' => 'admin/category',
        'category' => $data['category']
    ]);
}

public function new_category()
{
    $validation = \Config\Services::validation();

    $rules = [
        'name' => 'required',
        'type' => 'required'
    ];

    if ($this->request->is('post')) {
        if ($this->validate($rules)) {
            $data = [
                'name' => $this->request->getPost('name'),
                'type' => $this->request->getPost('type')
            ];

            $this->elfin_model->insert_data('category', $data);
            session()->setFlashdata('success_msg', 'Category added successfully.');
            return redirect()->to(get_site_url('admin/category'));
        }
    }

    return view('templates/admin_template', [
        'template' => 'admin/new_category',
        'validation' => $validation
    ]);
}

public function edit_category($id = null)
{
    $validation = \Config\Services::validation();
    $data['row'] = $this->elfin_model->get_row('category', ['id' => $id]);

    $rules = [
        'name' => 'required',
        'type' => 'required'
    ];

    if ($this->request->is('post')) {
        if ($this->validate($rules)) {
            $update = [
                'name' => $this->request->getPost('name'),
                'type' => $this->request->getPost('type')
            ];
            $where = ['id' => $id];

            $this->elfin_model->update_data('category', $update, $where);
            session()->setFlashdata('success_msg', 'Updated successfully.');
            return redirect()->to(get_site_url('admin/category'));
        }
    }

    return view('templates/admin_template', [
        'template' => 'admin/edit_category',
        'row' => $data['row'],
        'validation' => $validation
    ]);
}

public function delete_category(){
    /*$this->elfin_model->update_data('category', array('is_delete' => 1), array('id' => $id));
    $this->session->set_flashdata('success_msg', 'Deleted successfully.');
    redirect(get_site_url('admin/category'));*/

    $id = $this->request->getPost('id');
    $this->elfin_model->update_data('category', array('is_delete' => 1), array('id' => $id));
    
    echo 1;
}

public function clinic_info()
    {
        helper(['form']);

        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required',
            'address' => 'required',
            'contact' => 'required'
        ];

        if ($this->request->is('post') && $this->validate($rules)) {
            $file = $this->request->getFile('logo');
            $file_path = null;

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move('uploads/logos/', $newName);
                $file_path = 'uploads/logos/' . $newName;
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'address' => $this->request->getPost('address'),
                'contact' => $this->request->getPost('contact'),
                'cron_job' => $this->request->getPost('cron_job')
            ];

            if ($file_path) {
                $data['logo'] = $file_path;
            }

            $this->elfin_model->update_data('clinicInfo', $data, ['id' => 1]);
            session()->setFlashdata('success_msg', 'Details updated successfully.');
            return redirect()->to(get_site_url('admin/clinic_info'));
        }

        $data['info'] = $this->elfin_model->get_row('clinicInfo');
        $data['template'] = 'admin/clinic_info';

        return view('templates/admin_template', $data);
    }

    public function products()
{
$data['products'] = $this->elfin_model->get_result('products', ['is_delete' => 0], [], ['id' => 'DESC']);


    $data['template'] = 'admin/product';

    return view('templates/admin_template', $data);
}


public function new_product()
{
    helper(['form']);

    $validation = \Config\Services::validation();

    $rules = [
        'name'  => 'required',
        'price' => 'required'
    ];

    if ($this->request->is('post') && $this->validate($rules)) {
        $data = [
            'category_id'   => $this->request->getPost('category_id'),
            'name'          => $this->request->getPost('name'),
            'price'         => $this->request->getPost('price'),
            'normal_range'  => $this->request->getPost('normal_range'),
            'has_subproduct'=> $this->request->getPost('has_subproduct'),
            'subproduct_of' => $this->request->getPost('subproduct_of'),
            'description'   => $this->request->getPost('description')
        ];

        $this->elfin_model->insert_data('products', $data);
        session()->setFlashdata('success_msg', 'Product added successfully.');
        return redirect()->to(get_site_url('admin/products'));
    }

    // ✅ Fixed order by issue
    $data['category'] = $this->elfin_model->get_result('category', ['is_delete' => 0], [], ['id' => 'ASC']);
    $data['products'] = $this->elfin_model->get_result('products', ['is_delete' => 0], [], ['id' => 'DESC']);

    $data['template'] = 'admin/new_product';
    return view('templates/admin_template', $data);
}

public function edit_product($id = '')
{
    helper(['form']);
    $validation = \Config\Services::validation();

    $data['row'] = $this->elfin_model->get_row('products', ['id' => $id]);

    $rules = [
        'name'  => 'required',
        'price' => 'required'
    ];

    if ($this->request->is('post') && $this->validate($rules)) {
        $update = [
            'category_id'   => $this->request->getPost('category_id'),
            'name'          => $this->request->getPost('name'),
            'price'         => $this->request->getPost('price'),
            'normal_range'  => $this->request->getPost('normal_range'),
            'has_subproduct'=> $this->request->getPost('has_subproduct'),
            'subproduct_of' => $this->request->getPost('subproduct_of'),
            'description'   => $this->request->getPost('description'),
        ];
        
        $where = ['id' => $id];
        $this->elfin_model->update_data('products', $update, $where);

        session()->setFlashdata('success_msg', 'Updated successfully.');
        return redirect()->to(get_site_url('admin/products'));
    }

    $data['category'] = $this->elfin_model->get_result('category', ['is_delete' => 0], [], ['id' => 'ASC']);
$data['products'] = $this->elfin_model->get_result('products', ['is_delete' => 0], [], ['id' => 'DESC']);

    $data['template'] = 'admin/edit_product';
    return view('templates/admin_template', $data);
}

public function delete_product()
{
    if ($this->request->is('post')) {
        $id = $this->request->getPost('id');

        if ($id) {
            $this->elfin_model->update_data('products', ['is_delete' => 1], ['id' => $id]);
            echo 1;
        }
    }
}

public function fileValidation(string $post = '0', string $parameter)
{
    list($image, $required, $types) = explode(';', $parameter);
    $imageArray = explode('.', $image);
    $format = strtolower(end($imageArray));
    $typesArray = explode(',', $types);

    $validation = \Config\Services::validation();

    if ($required == '' && $types == '') {
        return true;
    }

    if ($required != '' && $types == '') {
        if ($image == '') {
            $validation->setError('file_validation', 'Please select a file to upload.');
            return false;
        }
        return true;
    }

    if ($required == '' && $types != '') {
        if ($image == '') {
            return true;
        } else {
            if (in_array($format, $typesArray)) {
                return true;
            } else {
                $validation->setError('file_validation', 'File format not allowed.');
                return false;
            }
        }
    }

    if ($required != '' && $types != '') {
        if ($image == '') {
            $validation->setError('file_validation', 'Please select a file to upload.');
            return false;
        } else {
            if (in_array($format, $typesArray)) {
                return true;
            } else {
                $validation->setError('file_validation', 'File format not allowed.');
                return false;
            }
        }
    }
}

public function syncTxn()
{
    echo '<pre>';
    
    $xmlStr = '';

    $replacePatterns = [
        'ShipmentEvent', 'PayWithAmazonEvent', 'SolutionProviderCreditEvent',
        'RetrochargeEvent', 'RentalTransactionEvent', 'PerformanceBondRefundEvent',
        'ServiceFeeEvent', 'DebtRecoveryEvent', 'LoanServicingEvent', 'AdjustmentEvent',
        'ChargeComponent', 'FeeComponent', 'DirectPayment', 'ShipmentItem',
        'Promotion', 'ProductGroup', 'DebtRecoveryItem', 'ChargeInstrument', 'AdjustmentItem'
    ];

    foreach ($replacePatterns as $pattern) {
        $xmlStr = str_replace("</$pattern>", "</$pattern><$pattern></$pattern>", $xmlStr);
    }

    $object = simplexml_load_string($xmlStr);
    $json = json_encode($object);
    $array = json_decode($json, true);

    if (isset($array['ListFinancialEventsResult']['FinancialEvents']['ShipmentEventList']['ShipmentEvent'])) {
        $shipmentEvents = $array['ListFinancialEventsResult']['FinancialEvents']['ShipmentEventList']['ShipmentEvent'];

        foreach ($shipmentEvents as $shipmentEvent) {
            if (count($shipmentEvent) == 0) {
                continue;
            }
            print_r($shipmentEvent);
        }
    }

    print_r($array);
    die();

    $syncError = [];

    // Load the model
    $this->elfin_model = model('ElfinModel');

    // Fetch priority accounts
    $lowPriorityAccount = $this->elfin_model->getRow('accounts', [], ['cronjob_txn_by_time_priority' => 'DESC']);
    $highPriorityAccount = $this->elfin_model->getRow('accounts', [], ['cronjob_txn_by_time_priority' => 'ASC']);

    if ($lowPriorityAccount && $highPriorityAccount) {
        $adminId = $highPriorityAccount->admin_id;
        $accountId = $highPriorityAccount->id;

        // Update account priority
        $this->elfin_model->update_data(
            'accounts',
            ['cronjob_txn_by_time_priority' => $lowPriorityAccount->cronjob_txn_by_time_priority + 1],
            ['id' => $highPriorityAccount->id]
        );

        // API Configuration
        $config = [
            'MERCHANT_ID' => $highPriorityAccount->MERCHANT_ID,
            'MARKETPLACE_ID' => $highPriorityAccount->MARKETPLACE_ID,
            'MWS_ACCESS_TOKEN' => $highPriorityAccount->MWS_ACCESS_TOKEN,
        ];

        // Get recent transaction time
        $txn = $this->elfin_model->getRow('transactions', ['account_id' => $accountId, 'posted_date !=' => '', 'posted_time >' => '0'], ['posted_date' => 'DESC']);

        if ($txn) {
            $res = fire_query_to_amazon($config, 'get-transactions-by-time', '2017-04-05T12:23:48Z');
            print_r($res);
        }
    }
}


}
