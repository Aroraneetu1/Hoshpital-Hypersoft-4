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

    public function pay_operation()
    {
        $price = $this->request->getPost('price') ?? [];
		$ttotnedaay = array_sum($price);

		$operation_id = $this->request->getPost('operation_id');
		$appointment_id = $this->request->getPost('appion_id');
		$receipt_No = $this->request->getPost('unique_id');
		$payment_type = $this->request->getPost('payment_type') ?? [];
		$payment_type_amount = $this->request->getPost('payment_type_amount') ?? [];

		$ttotpaid = array_sum($payment_type_amount);


		$discount = $this->request->getPost('discount');
		foreach ($price as $key1 => $value1) {

		    $update = array(
	            'subtotal' => $value1,
	            'discount' => $discount[$key1],
	            'payment_by' => get_session_data('id'),
	        );

	        $this->db->set($update);
	       
	        $this->db->where('product_id', $key1);
	        $this->db->where('operation_id', $operation_id);
	        
	        $this->db->update('operation_items'); 
		}

		//==== Update Payment table ====//

		foreach ($payment_type as $key => $value) {
			if($value !=''){

				$sql_querys = $this->db->query("SELECT * FROM payments WHERE receipt_id='".$receipt_No."' AND payment_type = '".$value."'");
				if($sql_querys->num_rows() == 0) {

					$inserts = array(
						'appointment_id' => $appointment_id,
						'receipt_id' => $receipt_No,
						'payment_type' => $value,
						'payment_type_amount' => $payment_type_amount[$key],
						'pay_from' => 'OPS',
						'payment_by' => get_session_data('id'),
						'payment_datetime' => date('Y-m-d H:i:s a')
					);
				    $this->elfin_model->insert('payments', $inserts);

				    $transactions = $this->bankTransactions($receipt_No, $value, $payment_type_amount[$key]);

				}else{

					$upss = array(
						'payment_type_amount' => $payment_type_amount[$key],
						'payment_by' => get_session_data('id'),
					);

					$where['receipt_id'] = $receipt_No;
					$where['payment_type'] = $value;

				    $this->elfin_model->update_data('payments', $upss, $where);
				}
			}
		}

		if($ttotnedaay == $ttotpaid){
			$this->elfin_model->update_data('operation', array('pay_status'=>'Paid'), array('id'=>$operation_id));
		}

        $this->session->setFlashdata('success_msg', 'Updated successfully.');
        return redirect()->to(get_site_url('appointments/payinpatient/' . $operation_id));
    }

    public function payoperation($id = null)
{
    $data = [];

    $this->elfin_model = new ElfinModel();

    $data['operation'] = $this->elfin_model->get_row('operation', ['id' => $id]);
    $data['operation_items'] = $this->elfin_model->get_result('operation_items', ['operation_id' => $id]);
    $data['paymentTypes'] = $this->elfin_model->get_result('payment_types', ['is_delete' => 0]);

    if (!empty($data['operation'])) {
        $appointment_id = $data['operation']->appointment_id;
        $data['paymentss'] = $this->elfin_model->get_result('payments', ['appointment_id' => $appointment_id, 'pay_from' => 'OPS']);
    } else {
        $data['paymentss'] = [];
    }

    return view('appointments/payoperation', [
        'data' => $data,
        'template' => 'appointments/payoperation',
        'menu' => 'appointments',
        'active' => 'alloperation',
    ]);
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
            return redirect()->to(site_url('appointments/allinpatient'));
        }

        $data['inpatients'] = $this->elfin_model->get_row('inpatients', ['id' => $id]);
        $data['inpatients_items'] = $this->elfin_model->get_result('inpatients_items', ['inpatients_id' => $id]);
        $data['template'] = 'appointments/viewinpatient';
        return view('templates/admin_template', $data);
    }

    public function discharge($id = null)
    {
        if (!$id) {
            return redirect()->to(site_url('appointments/allinpatient'));
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

        $this->elfin_model->update('inpatients', $updata, ['id' => $id]);

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

                    $this->elfin_model->update('inpatients_items', $exupdii, ['product_id' => $v, 'inpatients_id' => $id]);
                } else {
                    $insertii = [
                        'inpatients_id' => $id,
                        'product_id' => $v,
                        'price' => $price[$k],
                        'qty' => $qty[$k],
                        'subtotal' => $sub_amount[$k],
                        'updated' => date('Y-m-d'),
                    ];
                    $this->elfin_model->insert('inpatients_items', $insertii);
                }
            }
        }

        session()->setFlashdata('success_msg', 'In-Patient updated successfully.');
        return redirect()->to(site_url('appointments/allinpatient'));
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
    $this->elfin_model->update('rooms', ['status' => 'Available'], ['id' => $roomid]);

    return $this->response->setJSON(['status' => 1]);
}


}
