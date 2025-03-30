<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ElfinModel;

class AppointmentController extends BaseController
{
    protected $elfin_model;
    protected $session;
    protected $db;
    protected $validation;

    public function __construct()
    {
        helper(['url', 'form', 'email', 'elfin_helper']);

        $this->session = session();
        $this->elfin_model = new ElfinModel();
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();
        set_timezone();

        if (!get_the_current_user(1)) {
            return redirect()->to(get_site_url('login/admin'));
        }
    }

    public function all()
    {
        $appointments = $this->db->table('appointments')
            ->orderBy('id', 'DESC')
            ->limit(1000)
            ->get()
            ->getResult();

        return view('templates/admin_template', [
            'appointments' => $appointments,
            'template' => 'appointments/all',
            'menu' => 'appointments',
            'active' => 'all'
        ]);
    }

    public function allinpatient()
    {
        $inpatientall = $this->db->table('inpatients')
            ->orderBy("CASE WHEN pay_status = 'Pending' THEN 1 ELSE 2 END", 'ASC')
            ->orderBy('check_in', 'DESC')
            ->get()
            ->getResult();

        return view('templates/admin_template', [
            'inpatientall' => $inpatientall,
            'template' => 'appointments/allinpatient',
            'menu' => 'appointments',
            'active' => 'allinpatient'
        ]);
    }

    public function addinpatient()
{
    $this->validation->setRules([
        'check_in'          => 'required',
        'check_out'         => 'required',
        'patient_id'        => 'required',
        'room_no'           => 'required',
        'room_rate'         => 'required',
        'doctor_id'         => 'required',
        'admission_reason'  => 'required'
    ]);

    if ($this->request->is('post') && $this->validation->withRequest($this->request)->run()) {

        $insert = [
            'uniq_id'          => 'INP-' . date('ymdHis'),
            'check_in'         => $this->request->getPost('check_in'),
            'check_out'        => $this->request->getPost('check_out'),
            'appointment_id'   => $this->request->getPost('appointment_id'),
            'patient_id'       => $this->request->getPost('patient_id'),
            'room_no'          => $this->request->getPost('room_no'),
            'room_rate'        => $this->request->getPost('room_rate'),
            'room_rate_amt'    => $this->request->getPost('room_rate_amt'),
            'dayDiff'          => $this->request->getPost('dayDiff'),
            'remark'           => $this->request->getPost('remark'),
            'doctor_id'        => $this->request->getPost('doctor_id'),
            'admission_reason' => $this->request->getPost('admission_reason'),
            'pay_status'       => 'Pending',
            'created_by'       => get_session_data('id'),
        ];

        $this->db->table('inpatients')->insert($insert);
        $insert_id = $this->db->insertID(); // Get last inserted ID

        // Handle inpatient items
        $product_id = $this->request->getPost('product_id');
        $price = $this->request->getPost('price');
        $qty = $this->request->getPost('qty');
        $sub_amount = $this->request->getPost('sub_amount');

        if (!empty($product_id)) {
            foreach ($product_id as $k => $v) {
                $insertii = [
                    'inpatients_id' => $insert_id,
                    'product_id'    => $v,
                    'price'         => $price[$k],
                    'qty'           => $qty[$k],
                    'subtotal'      => $sub_amount[$k],
                    'updated'       => date('Y-m-d'),
                ];
                $this->db->table('inpatients_items')->insert($insertii);
            }
        }

        // Update room status
        $this->db->table('rooms')->where('id', $this->request->getPost('room_no'))->update(['status' => 'Booked']);

        session()->setFlashdata('success_msg', 'In-Patient added successfully.');
        return redirect()->to(get_site_url('appointments/allinpatient'));
    }

    $data['inpatientlist'] = $this->db->table('appointments')
        ->where('status', 2)
        ->where('inpatient_chkbx', 1)
        ->orderBy('id', 'DESC')
        ->get()
        ->getResult();

    $data['rooms'] = $this->db->table('rooms')
        ->where('is_delete', 0)
        ->where('status', 'Available')
        ->orderBy('id', 'DESC')
        ->get()
        ->getResult();

    $data['doctors'] = $this->db->table('users')
        ->whereIn('role', ['DOCTOR'])
        ->where('is_delete', 0)
        ->get()
        ->getResult();

    // View setup
    $data['template'] = 'appointments/addinpatient';
    $data['menu'] = 'appointments';
    $data['active'] = 'allinpatient';

    return view('templates/admin_template', $data);
}

public function pay_inpatient()
{
    $request = service('request');

    $pricerr = $request->getPost('pricerr') ?? 0;
    $price = $request->getPost('price') ?? [];
    $room_id = $request->getPost('room_id');

    // Ensure price is an array before summing
    $ttotnedaay = array_sum((array) $price) + $pricerr;

    $inpatient_id = $request->getPost('inpatient_id');
    $appointment_id = $request->getPost('appion_id');
    $receipt_No = $request->getPost('uniq_id');
    $payment_type = $request->getPost('payment_type') ?? [];
    $payment_type_amount = $request->getPost('payment_type_amount') ?? [];

    // Ensure payment_type_amount is an array before summing
    $ttotpaid = array_sum((array) $payment_type_amount);

    $discount = $request->getPost('discount') ?? [];

    foreach ($price as $key1 => $value1) {
        $update = [
            'subtotal' => $value1,
            'discount' => $discount[$key1] ?? 0,
            'payment_by' => get_session_data('id'),
        ];

        $this->db->table('inpatients_items')
            ->where('product_id', $key1)
            ->where('inpatients_id', $inpatient_id)
            ->update($update);
    }

    //==== Update Payment table ====//
    foreach ($payment_type as $key => $value) {
        if (!empty($value)) {
            $existingPayment = $this->db->table('payments')
                ->where('receipt_id', $receipt_No)
                ->where('payment_type', $value)
                ->countAllResults();

            if ($existingPayment == 0) {
                $this->db->table('payments')->insert([
                    'appointment_id' => $appointment_id,
                    'receipt_id' => $receipt_No,
                    'payment_type' => $value,
                    'payment_type_amount' => $payment_type_amount[$key] ?? 0,
                    'pay_from' => 'INP',
                    'payment_by' => get_session_data('id'),
                    'payment_datetime' => date('Y-m-d H:i:s a')
                ]);

                $this->bankTransactions($receipt_No, $value, $payment_type_amount[$key] ?? 0);
            } else {
                $this->db->table('payments')
                    ->where('receipt_id', $receipt_No)
                    ->where('payment_type', $value)
                    ->update([
                        'payment_type_amount' => $payment_type_amount[$key] ?? 0,
                        'payment_by' => get_session_data('id'),
                    ]);
            }
        }
    }

    if ($ttotnedaay == $ttotpaid) {
        $this->db->table('inpatients')
            ->where('id', $inpatient_id)
            ->update(['pay_status' => 'Paid']);

        $this->db->table('rooms')
            ->where('id', $room_id)
            ->update(['status' => 'Available']);
    }

    session()->setFlashdata('success_msg', 'Updated successfully.');
    return redirect()->to(get_site_url('appointments/payinpatient/' . $inpatient_id));
}

public function payinpatient($id = null)
{
    if (!$id) {
        return redirect()->to(get_site_url('appointments/allinpatient'));
    }

    $data['inpatients'] = $this->db->table('inpatients')->where('id', $id)->get()->getRow();
    $data['inpatients_items'] = $this->db->table('inpatients_items')->where('inpatients_id', $id)->get()->getResult();
    $data['paymentTypes'] = $this->db->table('payment_types')->where('is_delete', 0)->get()->getResult();

    if ($data['inpatients']) {
        $appointment_id = $data['inpatients']->appointment_id;
        $uniq_id = $data['inpatients']->uniq_id;
        $data['paymentss'] = $this->db->table('payments')
            ->where('appointment_id', $appointment_id)
            ->where('receipt_id', $uniq_id)
            ->where('pay_from', 'INP')
            ->get()
            ->getResult();
    }

    $data['template'] = 'appointments/payinpatient';
    $data['menu'] = 'appointments';
    $data['active'] = 'allinpatient';

    return view('templates/admin_template', $data);
}

public function viewInpatient($id = null)
    {
        if (!$id) {
            return redirect()->to(get_site_url('appointments/allinpatient'));
        }

        $data['inpatients'] = $this->elfin_model->get_row('inpatients', ['id' => $id]);
        $data['inpatients_items'] = $this->elfin_model->get_result('inpatients_items', ['inpatients_id' => $id]);
        $data['template'] = 'appointments/viewinpatient';
        return view('templates/admin_template', $data);
    }

    public function discharge($id = null)
    {
        if (!$id) {
            return redirect()->to(get_site_url('appointments/allinpatient'));
        }
        $data['template'] = 'appointments/discharge';
        $data['inpatients'] = $this->elfin_model->get_row('inpatients', ['id' => $id]);
        $data['inpatients_items'] = $this->elfin_model->get_result('inpatients_items', ['inpatients_id' => $id]);

        return view('templates/admin_template', $data);
    }

    public function editinpatient($id = null)
{
    $validation = \Config\Services::validation();

    $rules = [
        'check_in' => 'required',
        'check_out' => 'required',
        'patient_id' => 'required',
        'room_no' => 'required',
        'room_rate' => 'required',
        'doctor_id' => 'required',
        'admission_reason' => 'required',
    ];

    if ($this->validate($rules)) {
        $updata = [
            'check_in' => $this->request->getPost('check_in'),
            'check_out' => $this->request->getPost('check_out'),
            'appointment_id' => $this->request->getPost('appointment_id'),
            'patient_id' => $this->request->getPost('patient_id'),
            'room_no' => $this->request->getPost('room_no'),
            'room_rate' => $this->request->getPost('room_rate'),
            'room_rate_amt' => $this->request->getPost('room_rate_amt'),
            'dayDiff' => $this->request->getPost('dayDiff'),
            'remark' => $this->request->getPost('remark'),
            'doctor_id' => $this->request->getPost('doctor_id'),
            'admission_reason' => $this->request->getPost('admission_reason'),
            'pay_status' => 'Pending',
            'created_by' => get_session_data('id'),
        ];

        $this->elfin_model->update_data('inpatients', $updata, ['id' => $id]);

        $product_id = $this->request->getPost('product_id');
        $price = $this->request->getPost('price');
        $qty = $this->request->getPost('qty');
        $sub_amount = $this->request->getPost('sub_amount');

        if (!empty($product_id)) {
            foreach ($product_id as $k => $v) {
                $existingItem = $this->elfin_model->get_row('inpatients_items', ['product_id' => $v, 'inpatients_id' => $id]);

                if ($existingItem) {
                    $exupdii = [
                        'price' => $price[$k],
                        'qty' => $qty[$k],
                        'subtotal' => $sub_amount[$k]
                    ];

                    if ($existingItem->price != $price[$k] || $existingItem->qty != $qty[$k] || $existingItem->subtotal != $sub_amount[$k]) {
                        $exupdii['updated'] = date('Y-m-d');
                    }

                    $this->elfin_model->update_data('inpatients_items', $exupdii, ['product_id' => $v, 'inpatients_id' => $id]);
                } else {
                    $insertii = [
                        'inpatients_id' => $id,
                        'product_id' => $v,
                        'price' => $price[$k],
                        'qty' => $qty[$k],
                        'subtotal' => $sub_amount[$k],
                        'updated' => date('Y-m-d'),
                    ];
                    $this->elfin_model->insert_data('inpatients_items', $insertii);
                }
            }
        }

        session()->setFlashdata('success_msg', 'In-Patient updated successfully.');
        return redirect()->to(get_site_url('appointments/allinpatient'));
    }

    $data['inpatients'] = $this->elfin_model->get_row('inpatients', ['id' => $id]);
    $data['inpatients_items'] = $this->elfin_model->get_result('inpatients_items', ['inpatients_id' => $id]);
    $data['inpatientlist'] = $this->elfin_model->get_result('appointments', ['status' => 2, 'inpatient_chkbx' => 1],  [], ['id' => 'DESC']);
    $data['rooms'] = $this->elfin_model->get_result('rooms', ['is_delete' => 0],  [], ['id' => 'DESC']);
    $data['doctors'] = $this->elfin_model->get_result('users', ['role' => 'DOCTOR', 'is_delete' => 0]);

    $data['template'] = 'appointments/editinpatient';
    $data['menu'] = 'appointments';
    $data['active'] = 'allinpatient';

    return view('templates/admin_template', $data);
}

public function get_inpatients_products()
{
    $html = '';
    $keyword = $this->request->getPost('keyword');
    $db = \Config\Database::connect();
    $query = $db->query("SELECT p.id as pid, p.name as pname, p.price as pprice 
                         FROM products as p 
                         JOIN category as c ON p.category_id = c.id 
                         WHERE c.type = 3 
                         AND p.name LIKE '%" . $db->escapeLikeString($keyword) . "%' ESCAPE '!' 
                         AND p.is_delete = 0");

    if ($query->getNumRows() > 0) {
        $result = $query->getResult();
        foreach ($result as $v) {
            $html .= '<tr>';
            $html .= '<th id="pname' . $v->pid . '">' . esc($v->pname) . '</th>';
            $html .= '<td id="pprice' . $v->pid . '">' . number_format((float)$v->pprice, 2) . '</td>';
            $html .= '<td><a href="javascript:;" onclick="additem(' . $v->pid . ')"><i class="fa fa-plus-circle fa-lg"></i></a></td>';
            $html .= '<td><a href="javascript:;" onclick="minsitem(' . $v->pid . ')"><i class="fa fa-minus-circle fa-lg"></i></a></td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr>';
        $html .= '<td colspan="3">Products not found!</td>';
        $html .= '</tr>';
    }

    return $this->response->setBody($html);
}

public function remove_inpatients_products()
{
    $id = $this->request->getPost('id');
    $this->elfin_model->delete('inpatients_items', ['id' => $id]);
    return $this->response->setJSON(['status' => 1]);
}

public function delete_inpatient()
{
    $id = $this->request->getPost('id');
    $roomid = $this->request->getPost('roomid');

    $this->elfin_model->delete('inpatients', ['id' => $id]);
    $this->elfin_model->delete('inpatients_items', ['inpatients_id' => $id]);
    $this->elfin_model->update_data('rooms', ['status' => 'Available'], ['id' => $roomid]);

    return $this->response->setJSON(['status' => 1]);
}

public function get_operation_products()
{
    $html = '';
    $keyword = $this->request->getPost('keyword');
    $db = \Config\Database::connect();
    $query = $db->query("SELECT p.id as pid, p.name as pname, p.price as pprice 
                         FROM products as p 
                         JOIN category as c ON p.category_id = c.id 
                         WHERE c.type = 4 
                         AND p.name LIKE '%" . $db->escapeLikeString($keyword) . "%' ESCAPE '!' 
                         AND p.is_delete = 0");

    if ($query->getNumRows() > 0) {
        $result = $query->getResult();
        foreach ($result as $v) {
            $html .= '<tr>';
            $html .= '<th id="pname' . $v->pid . '">' . esc($v->pname) . '</th>';
            $html .= '<td id="pprice' . $v->pid . '">' . number_format((float)$v->pprice, 2) . '</td>';
            $html .= '<td><a href="javascript:;" onclick="additem(' . $v->pid . ')"><i class="fa fa-plus-circle fa-lg"></i></a></td>';
            $html .= '<td><a href="javascript:;" onclick="minsitem(' . $v->pid . ')"><i class="fa fa-minus-circle fa-lg"></i></a></td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr>';
        $html .= '<td colspan="3">Products not found!</td>';
        $html .= '</tr>';
    }

    return $this->response->setBody($html);
}

public function remove_operation_products()
{
    $id = $this->request->getPost('id');
    $this->elfin_model->delete('operation_items', ['id' => $id]);
    return $this->response->setJSON(['status' => 1]);
}

public function delete_operation()
{
    $id = $this->request->getPost('id');

    $this->elfin_model->delete('operation', ['id' => $id]);
    $this->elfin_model->delete('operation_items', ['operation_id' => $id]);

    return $this->response->setJSON(['status' => 1]);
}

public function alloperation() {
    $data['operationall'] = $this->elfin_model->get_result('operation');
    $data['template'] = 'appointments/alloperation';
    $data['menu'] = 'appointments';
    $data['active'] = 'alloperation';

    return view('templates/admin_template', $data);
}

public function pay_operation() {
    $request = service('request');

    $price = $request->getPost('price');
    $ttotnedaay = array_sum($price);

    $operation_id = $request->getPost('operation_id');
    $appointment_id = $request->getPost('appion_id');
    $receipt_No = $request->getPost('unique_id');
    $payment_type = $request->getPost('payment_type');
    $payment_type_amount = $request->getPost('payment_type_amount');

    $ttotpaid = array_sum($payment_type_amount);

    $discount = $request->getPost('discount');

    foreach ($price as $key1 => $value1) {
        $update = [
            'subtotal' => $value1,
            'discount' => $discount[$key1],
            'payment_by' => get_session_data('id'),
        ];

        $this->db->table('operation_items')
            ->where('product_id', $key1)
            ->where('operation_id', $operation_id)
            ->update($update);
    }

    foreach ($payment_type as $key => $value) {
        if ($value != '') {
            $existingPayment = $this->db->table('payments')
                ->where('receipt_id', $receipt_No)
                ->where('payment_type', $value)
                ->get()
                ->getRow();

            if (!$existingPayment) {
                $insert = [
                    'appointment_id' => $appointment_id,
                    'receipt_id' => $receipt_No,
                    'payment_type' => $value,
                    'payment_type_amount' => $payment_type_amount[$key],
                    'pay_from' => 'OPS',
                    'payment_by' => get_session_data('id'),
                    'payment_datetime' => date('Y-m-d H:i:s a'),
                ];
                $this->elfin_model->insert_data('payments', $insert);
                $this->bankTransactions($receipt_No, $value, $payment_type_amount[$key]);
            } else {
                $update = [
                    'payment_type_amount' => $payment_type_amount[$key],
                    'payment_by' => get_session_data('id'),
                ];
                $this->elfin_model->update_data('payments', $update, ['receipt_id' => $receipt_No, 'payment_type' => $value]);
            }
        }
    }

    if ($ttotnedaay == $ttotpaid) {
        $this->elfin_model->update_data('operation', ['pay_status' => 'Paid'], ['id' => $operation_id]);
    }

    session()->setFlashdata('success_msg', 'Updated successfully.');
    return redirect()->to(get_site_url('appointments/payoperation/' . $operation_id));
}

public function payoperation($id = '') {
    $data['operation'] = $this->elfin_model->get_row('operation', ['id' => $id]);
    $data['operation_items'] = $this->elfin_model->get_result('operation_items', ['operation_id' => $id]);
    $data['paymentTypes'] = $this->elfin_model->get_result('payment_types', ['is_delete' => 0]);

    $appointment_id = $data['operation']->appointment_id;
    $data['paymentss'] = $this->elfin_model->get_result('payments', ['appointment_id' => $appointment_id, 'pay_from' => 'OPS']);

    $data['template'] = 'appointments/payoperation';
    $data['menu'] = 'appointments';
    $data['active'] = 'alloperation';

    return view('templates/admin_template', $data);
}

public function viewoperation($id = '') {
    $data['operation'] = $this->elfin_model->get_row('operation', ['id' => $id]);
    $data['operation_items'] = $this->elfin_model->get_result('operation_items', ['operation_id' => $id]);

    $data['template'] = 'appointments/viewoperation';
    $data['menu'] = 'appointments';
    $data['active'] = 'alloperation';

    return view('templates/admin_template', $data);
}

public function addoperation()
{
    if ($this->request->is('post')) {
        $validation = \Config\Services::validation();
        $rules = [
            'operation_date' => 'required',
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'ass_doctor_id' => 'required',
            'operation_reason' => 'required'
        ];

        if ($validation->setRules($rules)->withRequest($this->request)->run()) {
            $db = \Config\Database::connect();

            $insert = [
                'unique_id' => 'OPS-' . date('ymdHis'),
                'operation_date' => $this->request->getPost('operation_date'),
                'appointment_id' => $this->request->getPost('appointment_id'),
                'patient_id' => $this->request->getPost('patient_id'),
                'doctor_id' => $this->request->getPost('doctor_id'),
                'ass_doctor_id' => $this->request->getPost('ass_doctor_id'),
                'operation_reason' => $this->request->getPost('operation_reason'),
                'pay_status' => 'Pending',
                'created_by' => get_session_data('id'),
            ];

            $db->table('operation')->insert($insert);
            $insert_id = $db->insertID();

            $product_id = $this->request->getPost('product_id');
            $price = $this->request->getPost('price');
            $qty = $this->request->getPost('qty');
            $sub_amount = $this->request->getPost('sub_amount');

            if (!empty($product_id)) {
                $operationItems = [];
                foreach ($product_id as $k => $v) {
                    $operationItems[] = [
                        'operation_id' => $insert_id,
                        'product_id' => $v,
                        'price' => $price[$k],
                        'qty' => $qty[$k],
                        'subtotal' => $sub_amount[$k],
                        'updated' => date('Y-m-d'),
                    ];
                }
                $db->table('operation_items')->insertBatch($operationItems);
            }

            session()->setFlashdata('success_msg', 'Operation added successfully.');
            return redirect()->to(get_site_url('appointments/alloperation'));
        }
    }

    $db = \Config\Database::connect();
    $data['operationlist'] = $db->table('appointments')
        ->where('status', 2)
        ->where('operationward_chkbx', 1)
        ->orderBy('id', 'DESC')
        ->get()
        ->getResult();

    $data['doctors'] = $db->table('users')
        ->whereIn('role', ['DOCTOR'])
        ->where('is_delete', 0)
        ->get()
        ->getResult();

    return view('templates/admin_template', [
        'operationlist' => $data['operationlist'],
        'doctors' => $data['doctors'],
        'template' => 'appointments/addoperation',
        'menu' => 'appointments',
        'active' => 'alloperation',
    ]);
}

public function editoperation($id = null)
{
    if ($this->request->is('post')) {
        $rules = [
            'operation_date' => 'required',
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'ass_doctor_id' => 'required',
            'operation_reason' => 'required',
        ];

        if (!$this->validator->setRules($rules)->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $operationData = [
            'operation_date' => $this->request->getPost('operation_date'),
            'appointment_id' => $this->request->getPost('appointment_id'),
            'patient_id' => $this->request->getPost('patient_id'),
            'doctor_id' => $this->request->getPost('doctor_id'),
            'ass_doctor_id' => $this->request->getPost('ass_doctor_id'),
            'operation_reason' => $this->request->getPost('operation_reason'),
        ];

        $this->db->table('operation')->where('id', $id)->update($operationData);

        $product_ids = $this->request->getPost('product_id');
        $prices = $this->request->getPost('price');
        $quantities = $this->request->getPost('qty');
        $sub_amounts = $this->request->getPost('sub_amount');

        if (!empty($product_ids)) {
            foreach ($product_ids as $key => $product_id) {
                $existingItem = $this->db->table('operation_items')
                    ->where('product_id', $product_id)
                    ->where('operation_id', $id)
                    ->get()->getRow();

                if ($existingItem) {
                    $updateData = [
                        'price' => $prices[$key],
                        'qty' => $quantities[$key],
                        'subtotal' => $sub_amounts[$key],
                        'updated' => ($existingItem->price != $prices[$key] || 
                                      $existingItem->qty != $quantities[$key] || 
                                      $existingItem->subtotal != $sub_amounts[$key]) 
                                      ? date('Y-m-d') 
                                      : $existingItem->updated
                    ];

                    $this->db->table('operation_items')->where(['product_id' => $product_id, 'operation_id' => $id])->update($updateData);
                } else {
                    $this->db->table('operation_items')->insert([
                        'operation_id' => $id,
                        'product_id' => $product_id,
                        'price' => $prices[$key],
                        'qty' => $quantities[$key],
                        'subtotal' => $sub_amounts[$key],
                        'updated' => date('Y-m-d'),
                    ]);
                }
            }
        }

        session()->setFlashdata('success_msg', 'Operation updated successfully.');
        return redirect()->to(get_site_url('appointments/alloperation'));
    }

    $data['operation'] = $this->db->table('operation')->where('id', $id)->get()->getRow();
    $data['operation_items'] = $this->db->table('operation_items')->where('operation_id', $id)->get()->getResult();
    $data['operationlist'] = $this->db->table('appointments')
        ->where('status', 2)
        ->where('operationward_chkbx', 1)
        ->orderBy('id', 'DESC')
        ->get()->getResult();
    $data['doctors'] = $this->db->table('users')
        ->whereIn('role', ['DOCTOR'])
        ->where('is_delete', 0)
        ->get()->getResult();

    return view('templates/admin_template', [
        'operation' => $data['operation'],
        'operation_items' => $data['operation_items'],
        'operationlist' => $data['operationlist'],
        'doctors' => $data['doctors'],
        'template' => 'appointments/editoperation',
        'menu' => 'appointments',
        'active' => 'alloperation',
    ]);
}

public function bankTransactions($receipt_No, $paytype, $amount)
{
    $bankss = get_key_value_array('banks', 'payment_type', array('id'));

    if (isset($bankss[$paytype])) {
        $bankid = $bankss[$paytype];

        $query = $this->db->table('banks')->select('current_balance')->where('id', $bankid)->get();
        $row = $query->getRow();

        if ($row) {
            $current_amount = $row->current_balance;
            $updated_amount = $current_amount + $amount;

            $this->db->table('banks')->where('id', $bankid)->update(['current_balance' => $updated_amount]);

            $insert2 = [
                'bank_id' => $bankid,
                'added_date' => date('Y-m-d'),
                'ref' => $receipt_No,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'balance' => $updated_amount,
                'remark' => 'Sale',
                'added_by' => get_session_data('id'),
            ];
            $this->elfin_model->insert_data('bank_transactions', $insert2);
        }
    }

    return;
}

public function display_token()
    {
        $data['appointments'] = $this->elfin_model->get_result('appointments', ['status' => 2],[], ['id' => 'DESC']);
        $data['template'] = 'appointments/display_token';
        $data['menu'] = 'appointments';
        $data['active'] = 'display_token';
        return view('templates/admin_template', $data);
    }

    public function add()
    {
        if ($this->request->is('post')) {
            $rules = [
                'consumer_id' => 'required',
                'service_id' => 'required',
                'new-appointment' => 'required',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to(get_site_url('appointments/add'))->withInput()->with('errors', $this->validator->getErrors());
            }

            list($estimate_sec, $schedule_id, $provider_id, $start_time, $end_time) = explode(
                '@#ELFIN#@',
                $this->request->getPost('new-appointment')
            );

            $service_id = $this->request->getPost('service_id');
            $token_number = 1;

            $query = $this->db->table('appointments')
                ->selectMax('token_number')
                ->where('service_id', $service_id)
                ->get()
                ->getRow();

            if ($query) {
                $token_number = $query->token_number + 1;
            }

            $paddedToken = str_pad($token_number, 3, '0', STR_PAD_LEFT);

            $insert = [
                'consumer_id' => $this->request->getPost('consumer_id'),
                'provider_id' => $provider_id,
                'schedule_id' => $schedule_id,
                'service_id' => $service_id,
                'estimate_sec' => $estimate_sec,
                'start_time' => $this->request->getPost('new-appointment-datetime'),
                'end_time' => $end_time,
                'status' => 1,
                'created' => date('Y-m-d'),
                'updated' => time(),
                'vital_index' => json_encode([]),
                'vital_value' => json_encode([]),
                'token_number' => $paddedToken,
            ];
            $this->db->table('appointments')->insert($insert);
            $insert_id = $this->db->insertID();

            $prival = get_key_value_array('service_types', 'id', ['amount']);
            $receipt_no = date('ymdHis');

            $inserts = [
                'lt_type' => 1,
                'appointment_id' => $insert_id,
                'product_id' => 'Consultations Fee',
                'lt_price' => $prival[$service_id] ?? 0,
                'receipt_no' => 'CON-' . $receipt_no,
                'payment_status' => 0,
            ];
            $this->db->table('lab_test')->insert($inserts);

            $this->session->setFlashdata('success_msg', 'Added successfully.');
            return redirect()->to(get_site_url('appointments/all'));
        }

        $data['consumers'] = $this->elfin_model->get_result('consumers', ['status' => 1], [], ['first_name' => 'ASC']);
        $data['services'] = $this->elfin_model->get_result('service_types', ['is_delete' => 0], [], ['name' => 'ASC']);
        $data['template'] = 'appointments/add';
        $data['menu'] = 'appointments';
        $data['active'] = 'add';
        $data['validation'] = $this->validation;
        return view('templates/admin_template', $data);
    }

    public function edit($id = '')
    {
        $data['row'] = $this->elfin_model->get_row('appointments', ['id' => $id]);

        if ($this->request->getMethod() == 'post') {
            $rules = [
                'consumer_id' => 'required',
                'service_id' => 'required',
                'provider_id' => 'required',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $update = [];
            $this->db->table('appointments')->where('id', $id)->update($update);

            $this->session->setFlashdata('success_msg', 'Updated successfully.');
            return redirect()->to(get_site_url('appointments/all'));
        }

        $data['consumers'] = $this->elfin_model->get_result('consumers', ['is_delete' => 0],[], ['first_name' => 'ASC'] );
        $data['services'] = $this->elfin_model->get_result('service_types', ['is_delete' => 0],[], ['name' => 'ASC'] );
        $data['template'] = 'appointments/edit';
        $data['menu'] = 'appointments';
        $data['active'] = 'edit';
        return view('templates/admin_template', $data);
    }

    public function ajaxGetAvailableProviders()
{
    $html = '';
    $service_id = $this->request->getPost('service_id');
    $duration_needed = $this->request->getPost('duration');

    $amountss = get_key_value_array('service_types', 'id', ['amount']);
    $amount = $amountss[$service_id] ?? 0;

    $query = $this->db->table('provider_schedules as ps')
        ->select('ps.*, u.first_name, u.last_name')
        ->join('users as u', 'ps.provider_id = u.id', 'LEFT')
        ->where('ps.is_delete', 0)
        ->where('ps.end_time >', time())
        ->where('u.is_delete', 0)
        ->like('u.provider_services', '"' . $service_id . '"', 'both')
        ->get();

    if ($query->getNumRows() > 0) {
        $schedules = $query->getResult();

        foreach ($schedules as $schedule) {
            $res = get_schedule_appointments_info($schedule);
            $available_time = $res['available_time'];

            if ($available_time >= $duration_needed) {
                $start_time = $schedule->start_time + (900 * $res['alloted']);
                $end_time = $start_time + 900;

                $temp_array = [
                    $duration_needed,
                    $schedule->id,
                    $schedule->provider_id,
                    $start_time,
                    $end_time,
                ];

                if (strtotime(date('Y-m-d')) > strtotime($schedule->s_date)) {
                    $schedule->s_date = date('Y-m-d');
                }

                $html .= '<tr>';
                $html .= '    <td>';
                $html .= '        <input value="' . $schedule->s_date . ' ' . date('h:i A', $start_time) . '" type="hidden" name="new-appointment-datetime">';
                $html .= '        <input id="new-appointment-' . $schedule->id . '" value="' . implode('@#ELFIN#@', $temp_array) . '" type="radio" name="new-appointment">';
                $html .= '        <label for="new-appointment-' . $schedule->id . '" class="new-appointment-label">' . $schedule->first_name . ' ' . $schedule->last_name . '</label>';
                $html .= '    </td>';
                $html .= '    <td>' . $schedule->s_date . '</td>';
                $html .= '    <td>' . date('h:i A', $start_time) . '</td>';
                $html .= '    <td>' . number_format($amount, 2) . '</td>';
                $html .= '</tr>';
            }
        }
    }
    echo $html;
    exit();
}

public function cancel_visit()
{
    $id = $this->request->getPost('id');

    $this->elfin_model->update_data('appointments', [
        'status' => 0,
        'updated' => time(),
    ], ['id' => $id]);

    $this->elfin_model->update_data('lab_test', [
        'is_delete' => 1,
    ], ['appointment_id' => $id]);

    return $this->response->setJSON(['status' => 1]);
}

public function removelb($id = '', $appid = '')
{
    if ($id) {
        $this->elfin_model->delete('lab_test', ['id' => $id]);
        session()->setFlashdata('success_msg', 'Removed successfully.');
    }
    return redirect()->to(get_site_url('appointments/laboratory/' . $appid));
}

public function lab_result()
{
    $appointment_id = $this->request->getPost('appointment_id');
    $results = $this->request->getPost('result');

    foreach ($results as $key => $value) {
        $update = [
            'result' => $value,
            'payment_by' => get_session_data('id'),
        ];

        $this->db->table('lab_test')
            ->where('product_id', $key)
            ->where('appointment_id', $appointment_id)
            ->update($update);
    }

    session()->setFlashdata('success_msg', 'Updated successfully.');
    return redirect()->to(get_site_url('appointments/laboratory/' . $appointment_id));
}

public function pay_result()
{
    $appointment_id = $this->request->getPost('appointment_id');
    $results = $this->request->getPost('result');
    $price = $this->request->getPost('price');
    $discount = $this->request->getPost('discount');
    $receipt_No = $this->request->getPost('receipt_No');
    $payment_type = $this->request->getPost('payment_type');
    $payment_type_amount = $this->request->getPost('payment_type_amount');

    foreach ($results as $key => $value) {
        $update = [
            'lt_price' => $price[$key],
            'discount' => $discount[$key],
            'payment_by' => get_session_data('id'),
            'payment_status' => 1,
        ];

        $this->db->table('lab_test')
            ->set($update)
            ->set('paid_at', 'IF(paid_at IS NULL, "' . date('Y-m-d') . '", paid_at)', false)
            ->set('full_paid_at', 'IF(full_paid_at IS NULL, "' . date('Y-m-d H:i:s a') . '", full_paid_at)', false)
            ->where('product_id', $key)
            ->where('appointment_id', $appointment_id)
            ->update();
    }

    foreach ($payment_type as $key => $value) {
        if ($value != '') {
            $existingPayment = $this->db->table('payments')
                ->where('receipt_id', $receipt_No)
                ->where('payment_type', $value)
                ->get()
                ->getNumRows();

            if ($existingPayment == 0) {
                $insert = [
                    'appointment_id' => $appointment_id,
                    'receipt_id' => $receipt_No,
                    'payment_type' => $value,
                    'payment_type_amount' => $payment_type_amount[$key],
                    'payment_datetime' => date('Y-m-d H:i:s a'),
                ];
                $this->elfin_model->insert('payments', $insert);

                $this->bankTransactions($receipt_No, $value, $payment_type_amount[$key]);
            } else {
                $update = ['payment_type_amount' => $payment_type_amount[$key]];

                $this->elfin_model->update('payments', $update, [
                    'receipt_id' => $receipt_No,
                    'payment_type' => $value,
                ]);
            }
        }
    }

    session()->setFlashdata('success_msg', 'Updated successfully.');
    return redirect()->to(get_site_url('appointments/pay/' . $appointment_id));
}

public function lab_services()
{
    $appointment_id = $this->request->getPost('appointment_id');
    $product_Ids = $this->request->getPost('product_Ids');
    $receipt_no = date('ymdHis');

    if (!empty($product_Ids)) {
        foreach ($product_Ids as $value) {
            $query = $this->db->table('lab_test')
                ->where('product_id', $value)
                ->where('appointment_id', $appointment_id)
                ->get();

            if ($query->getNumRows() == 0) {
                $prival = get_key_value_array('products', 'id', ['price']);
                $norrng = get_key_value_array('products', 'id', ['normal_range']);

                $insert = [
                    'appointment_id' => $appointment_id,
                    'product_id' => $value,
                    'lt_price' => $prival[$value] ?? 0,
                    'lt_normal_range' => $norrng[$value] ?? '',
                    'receipt_no' => 'LAB-' . $receipt_no,
                    'payment_status' => 0,
                ];
                $this->elfin_model->insert('lab_test', $insert);
            }
        }
    }

    session()->setFlashdata('success_msg', 'Updated successfully.');
    return redirect()->to(get_site_url('appointments/visit/' . $appointment_id));
}

public function laboratory($id = '')
{
    $data['appointment'] = $this->elfin_model->get_row('appointments', ['id' => $id]);
    $data['rows'] = $this->elfin_model->get_result('lab_test', [
        'appointment_id' => $id,
        'payment_status' => 1,
        'lt_type' => 0
    ]);

    $data['template'] = 'appointments/laboratory';
    $data['menu'] = 'appointments';
    $data['active'] = 'laboratory';
    
    return view('templates/admin_template', $data);
}

public function pay($id = '', $recpt = '') {
    if ($recpt) {
        $data['rows'] = $this->elfin_model->get_result('lab_test', ['appointment_id' => $id, 'receipt_no' => $recpt]);
        $data['paymentss'] = $this->elfin_model->get_result('payments', ['appointment_id' => $id, 'receipt_id' => $recpt, 'pay_from' => 0]);
    } else {
        $data['rows'] = $this->elfin_model->get_result('lab_test', ['appointment_id' => $id]);
        $data['paymentss'] = $this->elfin_model->get_result('payments', ['appointment_id' => $id, 'pay_from' => 0]);
    }

    $data['rows11'] = $this->elfin_model->get_result('lab_test', ['appointment_id' => $id]);
    $data['appointment'] = $this->elfin_model->get_row('appointments', ['id' => $id]);
    $data['paymentTypes'] = $this->elfin_model->get_result('payment_types', ['is_delete' => 0]);
    $data['template'] = 'appointments/pay';
    $data['menu'] = 'appointments';
	$data['active'] = 'pay';

    return view('templates/admin_template', $data);
}

public function receipt($id = '')
{
    $data['appointment'] = $this->elfin_model->get_row('appointments', ['id' => $id]);
    $data['rows'] = $this->elfin_model->get_result('lab_test', ['appointment_id' => $id, 'payment_status' => 1]);
    $data['paymentss'] = $this->elfin_model->get_result('payments', ['appointment_id' => $id]);

    $data['template'] = 'appointments/receipt';
    
    return view('templates/admin_template', $data);
}

public function end_visit()
{
    $id = $this->request->getPost('id');

    $this->elfin_model->update('appointments', [
        'status' => 3,
        'visited_end_time' => time(),
    ], ['id' => $id]);

    return $this->response->setJSON(1);
}

public function visit($id = '', $flag = '') {
    if ($flag == 'start') {
        $this->elfin_model->update('appointments', [
            'status' => 2,
            'visited_date' => date('Y-m-d'),
            'visited_start_time' => time(),
        ], ['id' => $id]);
    }

    $data['row'] = $this->elfin_model->get_row('appointments', ['id' => $id]);

    $inpatient_data = 0;
    if ($data['row']->inpatient_chkbx == 1) {
        $consumer_id = $data['row']->consumer_id;
        $appid = $id;

        $datainpatients = $this->elfin_model->get_row('inpatients', ['patient_id' => $consumer_id, 'appointment_id' => $appid]);
        $room_rate_amt = $datainpatients->room_rate_amt;
        $room_rate_amt += get_inpatients_total_amt($datainpatients->id);

        $tot_need_topay = $room_rate_amt;

        $paymentss = $this->elfin_model->get_result('payments', [
            'appointment_id' => $appid,
            'receipt_id' => $datainpatients->uniq_id,
            'pay_from' => 'INP'
        ]);

        $total_paid_yet = 0;
        if (!empty($paymentss)) {
            foreach ($paymentss as $kk => $vv) {
                $total_paid_yet += $vv->payment_type_amount;
            }
        }

        if ($tot_need_topay > $total_paid_yet) {
            $inpatient_data = 1;
        }
    }

    $data['inpatient_data'] = $inpatient_data;

    $operation_data = 0;
    if ($data['row']->operationward_chkbx == 1) {
        $consumer_id = $data['row']->consumer_id;
        $appid = $id;

        $datainpatients = $this->elfin_model->get_row('operation', ['patient_id' => $consumer_id, 'appointment_id' => $appid]);
        if ($datainpatients) {
            $room_rate_amt = get_operation_total_amt($datainpatients->id);
        } else {
            $room_rate_amt = 0; // Default value if no record is found
        }


        $tot_need_topay = $room_rate_amt;

        $paymentss = [];
        if ($datainpatients) {
            $paymentss = $this->elfin_model->get_result('payments', [
                'appointment_id' => $appid,
                'receipt_id' => $datainpatients->unique_id,
                'pay_from' => 'OPS'
            ]);
        }

        $total_paid_yet = 0;
        if (!empty($paymentss)) {
            foreach ($paymentss as $kk => $vv) {
                $total_paid_yet += $vv->payment_type_amount;
            }
        }

        if ($tot_need_topay > $total_paid_yet) {
            $operation_data = 1;
        }
    }

    $data['operation_data'] = $operation_data;

    $data['lab_rows'] = $this->elfin_model->get_result('lab_test', ['appointment_id' => $id]);

    if ($this->request->getMethod() == 'post') {
        $rules = ['mode' => 'required'];

        if ($this->validate($rules)) {
            $mode = $this->request->getPost('mode');
            $where = ['id' => $id];
            $update = [
                'remark' => $this->request->getPost('remark'),
                'notes' => $this->request->getPost('notes'),
                'operationward_chkbx' => $this->request->getPost('operationward_chkbx'),
                'inpatient_chkbx' => $this->request->getPost('inpatient_chkbx'),
                'emergency_chkbx' => $this->request->getPost('emergency_chkbx'),
                'vital_index' => json_encode($this->request->getPost('vital_index')),
                'vital_value' => json_encode($this->request->getPost('vital_value')),
            ];
            if ($mode == 'end') {
                $update['status'] = 3;
                $update['visited_end_time'] = time();
            }
            $this->elfin_model->update('appointments', $update, $where);
            if ($mode == 'end') {
                session()->setFlashdata('success_msg', 'Visit has been ended successfully.');
                return redirect()->to(get_site_url('appointments/visit/' . $id . '/view'));
            } else {
                session()->setFlashdata('success_msg', 'Saved successfully.');
                return redirect()->to(get_site_url('appointments/visit/' . $id));
            }
        }
    }

    $appointments = $this->db->table('appointments')
    ->where('consumer_id', $data['row']->consumer_id)
    ->whereNotIn('vital_index', ['NULL', '[]', ''])
    ->whereNotIn('vital_value', ['NULL', '[]', ''])
    ->orderBy('visited_start_time', 'DESC')
    ->get()
    ->getResult();

    $data['modal'] = view('appointments/modal_view_visits_by_patient', ['appointments' => $appointments]);

    $data['template'] = 'appointments/visit';
    $data['menu'] = 'appointments';
    $data['active'] = 'visit';
    return view('templates/admin_template', $data);
}

public function ajax_get_token_number() {
    $service_id = $this->request->getPost('service_id');
    $query = $this->db->query("SELECT token_number FROM appointments WHERE service_id='{$service_id}' AND status = 2 AND DATE(start_time) = '".date('Y-m-d')."'");

    $token_number = ($query->getNumRows() > 0) ? $query->getRow()->token_number : '';

    return $this->response->setBody($token_number);
}

public function ajax_print_prescription() {
    $appointment_id = $this->request->getPost('appointment_id');
    $row = $this->elfin_model->get_row('appointments', ['id' => $appointment_id]);

    $consumers = get_key_value_array('consumers', 'id', ['first_name', 'last_name']);
    $providers = get_key_value_array('users', 'id', ['first_name', 'last_name']);
    $services = get_key_value_array('service_types', 'id', ['name']);

    $html = '<div id="pp">';
    $html .= get_print_header();
    $html .= "<div style='text-align: center; margin-bottom: 20px;font-size: 20px;'><strong>Doctor Prescription</strong></div>
        <div class='row'>
            <div class='col-md-12' style='margin-bottom: 20px;'>
                <p><strong>Patient ID:</strong> ".get_formated_id('P', $row->consumer_id)."</p>
                <p><strong>Patient Name:</strong> {$consumers[$row->consumer_id]}</p>
                <p><strong>Service:</strong> {$services[$row->service_id]}</p>
                <p><strong>Date:</strong> ".date('d M, Y')."</p>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-12' style='margin-bottom: 10px;padding-top: 5px;'>
                <p><strong>Investigation</strong></p>
                <p style='margin-bottom: 150px;'>".nl2br($row->remark)."</p>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-12' style='margin-bottom: 30px;padding-top: 5px;'>
                <p><strong>Prescription</strong></p>
                <p style='margin-bottom: 100px;'>".nl2br($row->notes)."</p>
            </div>
        </div>
        <div style='position: fixed;bottom: 20px;left: 65px;width: 100%;text-align: left;'>
            <p><strong>Doctor’s Signature:</strong> <span style='border-bottom: 1px solid #000;'>{$providers[$row->provider_id]}</span></p>
        </div>";
    $html .= '</div>';

    return $this->response->setBody($html);
}

}
