<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ElfinModel;

class FinanceController extends BaseController
{
    protected $elfinModel;
    protected $session;
    protected $db;
    protected $validator;

    public function __construct()
    {
        helper(['form', 'url', 'elfin_helper']);
        $this->elfinModel = new ElfinModel();
        $this->session = session();
        $this->db = \Config\Database::connect();
        $this->validator = \Config\Services::validation();

        set_timezone();
        if (!get_the_current_user(1)) {
            return redirect()->to(get_site_url('login/admin'));
        }
    }

    public function all()
    {
        $data['banks'] = $this->db->table('banks')
            ->where('is_delete', 0)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult(); 
    
        return view('templates/admin_template', array_merge($data, [
            'template' => 'finance/ball',
            'menu' => 'finance',
            'active' => 'all',
        ]));
    }     

    public function add()
{
    if ($this->request->is('post')) {
        $rules = [
            'bank_name' => 'required',
            'branch_code' => 'required',
            'bank_phone' => 'required',
            'bank_account' => 'required',
            'opening_balance' => 'required',
            'current_balance' => 'required',
            'main_account' => 'required',
        ];

        $this->validator->setRules($rules);

        if ($this->validator->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $this->validator);

        $insert = [
            'bank_name' => $this->request->getPost('bank_name'),
            'branch_code' => $this->request->getPost('branch_code'),
            'bank_phone' => $this->request->getPost('bank_phone'),
            'bank_account' => $this->request->getPost('bank_account'),
            'opening_balance' => $this->request->getPost('opening_balance'),
            'current_balance' => $this->request->getPost('current_balance'),
            'main_account' => $this->request->getPost('main_account'),
            'payment_type' => $this->request->getPost('payment_type'),
        ];

        $this->elfinModel->insert_data('banks', $insert);
        session()->setFlashdata('success_msg', 'Bank added successfully.');
        return redirect()->to(get_site_url('finance/all'));
    }
}

    $data['paymenttypes'] = $this->db->table('payment_types')
        ->where('is_delete', 0)
        ->where('name !=', 'Debit')
        ->orderBy('id', 'ASC')
        ->get()
        ->getResult();

    return view('templates/admin_template', array_merge($data,[
        'template' => 'finance/badd',
        'menu' => 'finance',
        'active' => 'add',
        'validation' => $this->validator    
    ]));
}


public function edit($id = '')
{
    if ($this->request->is('post')) {
        $rules = [
            'bank_name' => 'required',
            'branch_code' => 'required',
            'bank_phone' => 'required',
            'bank_account' => 'required',
            'opening_balance' => 'required',
            'current_balance' => 'required',
            'main_account' => 'required',
        ];

        $this->validator->setRules($rules);

        if ($this->validator->withRequest($this->request)->run()) {

        $updateData = [
            'bank_name' => $this->request->getPost('bank_name'),
            'branch_code' => $this->request->getPost('branch_code'),
            'bank_phone' => $this->request->getPost('bank_phone'),
            'bank_account' => $this->request->getPost('bank_account'),
            'opening_balance' => $this->request->getPost('opening_balance'),
            'current_balance' => $this->request->getPost('current_balance'),
            'main_account' => $this->request->getPost('main_account'),
            'payment_type' => $this->request->getPost('payment_type'),
        ];

        $this->elfinModel->update_data('banks', $updateData, ['id' => $id]);
        session()->setFlashdata('success_msg', 'Bank updated successfully.');
        return redirect()->to(get_site_url('finance/all'));
    }
}

    $data['paymenttypes'] = $this->db->table('payment_types')
        ->where('is_delete', 0)
        ->where('name !=', 'Debit')
        ->orderBy('id', 'ASC')
        ->get()
        ->getResult();

    $data['row'] = $this->elfinModel->get_row('banks', ['id' => $id]);

    return view('templates/admin_template',array_merge($data,[
        'template' => 'finance/bedit',
        'menu' => 'finance',
        'active' => 'edit',
        'validation' => $this->validator 
    ]));
}


    public function ajax_btransections()
    {
        $bn_id = $this->request->getPost('bn_id');
        $from = date('Y-m-d', strtotime($this->request->getPost('from') ?? date('d F Y')));
        $to = date('Y-m-d', strtotime($this->request->getPost('to') ?? date('d F Y')));

        $bank_transactions = $this->db->table('bank_transactions')
    ->where('added_date >=', $from)
    ->where('added_date <=', $to)
    ->where('bank_id', $bn_id)
    ->orderBy('id', 'DESC')
    ->get()
    ->getResult();

        $html = '';
        if (!empty($bank_transactions)) {
            foreach ($bank_transactions as $value) {
                $html .= '<tr>
                            <td>' . date('d-M-Y', strtotime($value->added_date)) . '</td>
                            <td>' . $value->ref . '</td>
                            <td>' . $value->debit_amount . '</td>
                            <td>' . $value->credit_amount . '</td>
                            <td>' . $value->balance . '</td>
                            <td>' . $value->remark . '</td>
                          </tr>';
            }
        } else {
            $html = 0;
        }

        return $this->response->setBody($html);
    }

    public function transactions()
{
    $data['from'] = $this->request->getGet('from') ?? date('d F Y');
    $data['to'] = $this->request->getGet('to') ?? date('d F Y');

    $data['banks'] = $this->db->table('banks')
        ->where('is_delete', 0)
        ->orderBy('id', 'ASC')
        ->get()
        ->getResult();

    return view('templates/admin_template',array_merge($data,[
        'template' => 'finance/transactions',
        'menu' => 'finance',
        'active' => 'transactions',
    ]));
}

    public function btransfr_now()
    {
        $bankss = get_key_value_array('banks', 'id', ['bank_name']); 

        $frm_bnid = $this->request->getPost('frm_bnid');
        $frm_cblnc = $this->request->getPost('frm_cblnc');
        $trn_bankid = $this->request->getPost('trn_bankid');
        $tamount = $this->request->getPost('tamount');

        // Update 1st bank balance
        $fcurrent_balance = $frm_cblnc - $tamount;
        $this->db->table('banks')->where('id', $frm_bnid)->update(['current_balance' => $fcurrent_balance]);

        // Insert transaction for 1st bank
        $insert1 = [
            'bank_id' => $frm_bnid,
            'added_date' => date('Y-m-d'),
            'ref' => 'Bank to Bank',
            'debit_amount' => 0,
            'credit_amount' => $tamount,
            'balance' => $fcurrent_balance,
            'remark' => 'Transfer to ' . ($bankss[$trn_bankid] ?? ''),
            'added_by' => get_session_data('id'),
        ];
        $this->elfinModel->insert_data('bank_transactions', $insert1);

        // Get current balance of 2nd bank
        $row = $this->db->table('banks')->select('current_balance')->where('id', $trn_bankid)->get()->getRow();

        if ($row) {
            $current_amount = $row->current_balance;
            $updated_amount = $current_amount + $tamount;

            // Update 2nd bank balance
            $this->db->table('banks')->where('id', $trn_bankid)->update(['current_balance' => $updated_amount]);

            // Insert transaction for 2nd bank
            $insert2 = [
                'bank_id' => $trn_bankid,
                'added_date' => date('Y-m-d'),
                'ref' => 'Bank to Bank',
                'debit_amount' => $tamount,
                'credit_amount' => 0,
                'balance' => $updated_amount,
                'remark' => 'Received from ' . ($bankss[$frm_bnid] ?? ''),
                'added_by' => get_session_data('id'),
            ];
            $this->elfinModel->insert_data('bank_transactions', $insert2);
        }

        session()->setFlashdata('success_msg', 'Bank transactions successfully.');
        return redirect()->to(get_site_url('finance/all'));
    }

    public function income_statement()
{
    $fromInput = $this->request->getGet('from');
    $toInput = $this->request->getGet('to');

    $data['from'] = $fromInput ? $fromInput : date('d F Y');
    $data['to'] = $toInput ? $toInput : date('d F Y');

    $from = date('Y-m-d', strtotime($data['from']));
    $to = date('Y-m-d', strtotime($data['to']));

    $data['sales1'] = $this->elfinModel->get_result('appointments', [
        'created >=' => $from,
        'created <=' => $to,
        'status !=' => 0
    ], [],['id'=>'DESC']);

    $data['sales2'] = $this->elfinModel->get_result('lab_test', [
        'paid_at >=' => $from,
        'paid_at <=' => $to,
        'payment_status' => 1,
        'is_delete' => 0
    ], [],['id'=>'DESC']);

    $expense_array = [];

    $querycat = $this->db->query("SELECT id, name FROM category WHERE type = 2 AND is_delete = 0");

    if ($querycat->getNumRows() > 0) {
        foreach ($querycat->getResult() as $category) {
            $expense_array[$category->id]['name'] = $category->name;
            $extot = 0;

            $products = $this->db->query("SELECT id FROM products WHERE category_id = {$category->id} AND is_delete = 0");

            if ($products->getNumRows() > 0) {
                foreach ($products->getResult() as $product) {
                    $proId = $product->id;
                    $exitem = $this->db->query("
                        SELECT SUM(subtotal) as extots 
                        FROM expenses e 
                        JOIN expense_items ei ON e.id = ei.expense_id 
                        WHERE e.expense_date >= '{$from}' 
                        AND e.expense_date <= '{$to}' 
                        AND ei.product_id = '{$proId}'
                    ");

                    if ($exitem->getNumRows() > 0) {
                        $resultppee = $exitem->getRow();
                        $extot += $resultppee->extots ?? 0;
                    }
                }
            }

            $expense_array[$category->id]['amount'] = $extot;
        }
    }

    $data['expense_array'] = $expense_array;

    return view('templates/admin_template',array_merge($data, [
        'template' => 'admin/income_statement',
        'menu' => 'finance',
        'active' => 'income_statement',
        'data' => $data
    ]));
}


}
