<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ElfinModel;

class ConsumerController extends BaseController
{
    protected $elfin_model;
    protected $session;
    protected $db;

    public function __construct()
    {
        helper(['form', 'url', 'elfin_helper']);
        $this->elfin_model = new ElfinModel();
        $this->session = session();
        $this->db = \Config\Database::connect();

        set_timezone();
        if (!get_the_current_user(1)) {
            return redirect()->to(get_site_url('login/admin'));
        }
    }

    public function all()
    {
        $data = [
            'rows' => $this->elfin_model->get_result('consumers', [], [],['id'=>'DESC']),
            'template' => 'consumers/all',
            'menu' => 'consumers',
            'active' => 'all',
        ];

        return view('templates/admin_template', $data);
    }

    public function export_excel()
    {
        $header = implode("\t", [
            'Patient Name', 'DOB', 'Gender', 'Address', 'City', 'State',
            'Postal Code', 'Phone Number', 'Alternate Phone Number', 'Debit Amount'
        ]) . "\n";

        $rows = $this->elfin_model->get_result('consumers', ['status' => 1], [],['first_name'=>'ASC']);
        $body = [];

        if ($rows) {
            foreach ($rows as $row) {
                $get_debit_payment = get_debit_payment($row->id);
                $body[] = implode("\t", [
                    $row->first_name . ' ' . $row->middle_name . ' ' . $row->last_name,
                    $row->dob, $row->gender, $row->address, $row->city, $row->state,
                    $row->postal_code, $row->phone_number, $row->alternate_phone_number,
                    $get_debit_payment
                ]);
            }
        }

        return $this->response
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-Disposition', 'attachment; filename=patients.xls')
            ->setBody($header . "\n" . implode("\n", $body) . "\n");
    }

    public function add()
    {
        $validation = \Config\Services::validation();
        if ($this->request->is('post') && $this->validate([
            'first_name' => 'required',
            'gender' => 'required',
            'dob' => 'required',
            'country' => 'required',
            'phone_number' => 'required'
        ])) {
            $insert = [
                'first_name' => $this->request->getPost('first_name'),
                'middle_name' => $this->request->getPost('middle_name'),
                'last_name' => $this->request->getPost('last_name'),
                'gender' => $this->request->getPost('gender'),
                'dob' => $this->request->getPost('dob'),
                'address' => $this->request->getPost('address'),
                'city' => $this->request->getPost('city'),
                'state' => $this->request->getPost('state'),
                'country' => $this->request->getPost('country'),
                'postal_code' => $this->request->getPost('postal_code'),
                'phone_number' => $this->request->getPost('phone_number'),
                'alternate_phone_number' => $this->request->getPost('alternate_phone_number'),
                'created' => time(),
                'status' => 1,
            ];
            $this->elfin_model->insert_data('consumers', $insert);
            $this->session->setFlashdata('success_msg', 'Added successfully.');
            return redirect()->to(get_site_url('consumers/all'));
        }

        return view('templates/admin_template', [
            'template' => 'consumers/add',
            'menu' => 'consumers',
            'active' => 'add',
            'validator' => $validation
        ]);
    }

    public function edit($id = null)
    {
        $validation = \Config\Services::validation();
        $data['row'] = $this->elfin_model->get_row('consumers', ['id' => $id]);

        if ($this->request->is('post') && $this->validate([
            'first_name' => 'required',
            'gender' => 'required',
            'dob' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'phone_number' => 'required'
        ])) {
            $update = [
                'first_name' => $this->request->getPost('first_name'),
                'middle_name' => $this->request->getPost('middle_name'),
                'last_name' => $this->request->getPost('last_name'),
                'gender' => $this->request->getPost('gender'),
                'dob' => $this->request->getPost('dob'),
                'address' => $this->request->getPost('address'),
                'city' => $this->request->getPost('city'),
                'state' => $this->request->getPost('state'),
                'country' => $this->request->getPost('country'),
                'postal_code' => $this->request->getPost('postal_code'),
                'phone_number' => $this->request->getPost('phone_number'),
                'alternate_phone_number' => $this->request->getPost('alternate_phone_number'),
                'updated' => time(),
                'status' => $data['row']->status,
            ];
            $this->elfin_model->update_data('consumers', $update, ['id' => $id]);
            $this->session->setFlashdata('success_msg', 'Updated successfully.');
            return redirect()->to(get_site_url('consumers/all'));
        }

        $data['template'] = 'consumers/edit';
		$data['menu'] = 'consumers';
		$data['active'] = 'edit';
        $data['validator'] = $validation;

        return view('templates/admin_template', $data);
    }

    public function history($id = '') {
        $data['consumer'] = $this->elfin_model->get_row('consumers', array('id' => $id));
        $data['appointments'] = $this->db->table('appointments')
        ->where('consumer_id', $id)
        ->orderBy('created', 'DESC')
        ->get()
        ->getResult();
    

        $data['vitals'] = $this->db->table('appointments')
            ->where('consumer_id', $id)
            ->whereIn('status', [2, 3])
            ->whereNotIn('vital_index', ['NULL', '[]'])
            ->whereNotIn('vital_value', ['NULL', '[]'])
            ->orderBy('visited_start_time', 'DESC')
            ->get()
            ->getRow();


       $data['template'] = 'consumers/history';
       $data['menu'] = 'consumers';
       $data['active'] = 'history';
    
        return view('templates/admin_template', $data);
    }    

    public function change_status($status, $id)
    {
        $this->elfin_model->update_data('consumers', ['status' => $status], ['id' => $id]);
        $this->session->setFlashdata('success_msg', 'Status has been changed successfully.');
        return redirect()->to(get_site_url('consumers/all'));
    }

    public function pay($id = '')
{
    $db = \Config\Database::connect();

    $debitpay = [];
    $query = $db->query("
        SELECT * FROM `appointments` as a 
        JOIN payments as p ON a.id = p.appointment_id 
        WHERE p.payment_type = 8 AND a.consumer_id = ?", [$id]);

    if ($query->getNumRows() > 0) {
        $debitpay = $query->getResult();
    }

    $debitarray = [];
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
                $string = $value->receipt_id;
                $servicename = '';

                if (strpos($string, "CON") === 0) {
                    $servicename = 'Consultations Fee';
                } elseif (strpos($string, "LAB") === 0) {
                    $servicename = 'Lab Test';
                } elseif (strpos($string, "INP") === 0) {
                    $servicename = 'In-Patient Fee';
                } elseif (strpos($string, "OPS") === 0) {
                    $servicename = 'Operation Fee';
                }

                $remamt = round($payment_type_amount - $debitpayamt);

                $debitarray[] = [
                    'receipt_id' => $value->receipt_id,
                    'payment_datetime' => $value->payment_datetime,
                    'payment_type_amount' => $remamt,
                    'servicename' => $servicename,
                ];
            }
        }
    }

    $data['debitpay'] = $debitarray;

		$data['template'] = 'consumers/payments';
		$data['menu'] = 'consumers';
		$data['active'] = 'pay';

    return view('templates/admin_template', $data);
}


    public function debitpay()
    {
        $inserts = [
            'receipt_id' => $this->request->getPost('rid'),
            'remark' => 'Debit Payment',
            'patient_id' => $this->request->getPost('patient_id'),
            'payment_type' => $this->request->getPost('payment_type'),
            'amount' => $this->request->getPost('payment_amt'),
            'user_id' => get_session_data('id'),
            'paid_at' => date('Y-m-d'),
            'datetime' => date('Y-m-d H:i:s a'),
        ];
        $this->elfin_model->insert_data('debit_payment', $inserts);
        $this->session->setFlashdata('success_msg', 'Debit paid successfully.');
        return redirect()->to(get_site_url('consumers/pay/' . $inserts['patient_id']));
    }

    public function bankTransactions1($receipt_No, $paytype, $amount){

		$bankss = get_key_value_array('banks', 'payment_type', array('id'));

		if(isset($bankss[$paytype])){

			$bankid = $bankss[$paytype];

			$this->db->select('current_balance');
	        $this->db->where('id', $bankid);
	        $query = $this->db->get('banks');
	        $row = $query->row();

	        if($row){
	            $current_amount = $row->current_balance;
	            $updated_amount = $current_amount + $amount;

	            $this->db->where('id', $bankid);
	            $this->db->update('banks', ['current_balance' => $updated_amount]);

	            $insert2 = array(
					'bank_id' => $bankid,
					'added_date' => date('Y-m-d'),
					'ref' => $receipt_No,
					'debit_amount' => $amount,
					'credit_amount' => 0,
					'balance' => $updated_amount,
					'remark' => 'Sale',
					'added_by' => get_session_data('id'),
				);
			    $this->elfin_model->insert_data('bank_transactions', $insert2);
	        }
	    }

        return;
	}
}
