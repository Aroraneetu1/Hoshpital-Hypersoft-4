<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ElfinModel;

class ExpenseController extends BaseController
{
    protected $elfinModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        helper(['form', 'url', 'elfin_helper']);
        $this->elfinModel = new ElfinModel();
        $this->session = session();
        $this->db = \Config\Database::connect();

        set_timezone();
        if (!get_the_current_user(1)) {
            return redirect()->to(get_site_url('login/admin'));
        }
    }

    public function all()
    {
        $from = $this->request->getGet('from') ?: date('d F Y');
        $to = $this->request->getGet('to') ?: date('d F Y');

        $fromDate = date('Y-m-d', strtotime($from));
        $toDate = date('Y-m-d', strtotime($to));

        $query = $this->db->table('expenses')
            ->where('expense_date >=', $fromDate)
            ->where('expense_date <=', $toDate)
            ->orderBy('id', 'DESC')
            ->get();

        $data = [
            'from' => $from,
            'to' => $to,
            'expenses' => $query->getResult(),
            'template' => 'expenses/all',
            'menu' => 'expenses',
            'active' => 'all'
        ];

        return view('templates/admin_template', $data);
    }

    public function expensePayment()
    {
        $from = $this->request->getGet('from') ?: date('d F Y');
        $to = $this->request->getGet('to') ?: date('d F Y');

        $fromDate = date('Y-m-d', strtotime($from));
        $toDate = date('Y-m-d', strtotime($to));

        $query = $this->db->table('expense_payment')
            ->where('payment_date >=', $fromDate)
            ->where('payment_date <=', $toDate)
            ->orderBy('id', 'DESC')
            ->get();

        $data = [
            'from' => $from,
            'to' => $to,
            'expensespay' => $query->getResult(),
            'template' => 'expenses/expensepayment',
            'menu' => 'expenses',
            'active' => 'expensepayment'
        ];

        return view('templates/admin_template', $data);
    }

    public function payNow()
    {
        $expenseId = $this->request->getPost('rid');
        $payType = $this->request->getPost('payment_type');
        $amount = $this->request->getPost('amount');
        $paymentDate = $this->request->getPost('payment_date');
        $totamts = $this->request->getPost('totamts');

        $totalPaidYet = get_expense_totpaid_amt($expenseId);
        $pendingPay = ($totamts - ($totalPaidYet + $amount));

        if ($pendingPay == 0) {
            $this->elfinModel->update_data('expenses', [
                'payment_status' => 'Paid',
                'payment_date' => $paymentDate,
                'payment_by' => get_session_data('id')
            ], ['id' => $expenseId]);
        }

        $this->elfinModel->insert_data('expense_payment', [
            'expense_id' => $expenseId,
            'pay_type' => $payType,
            'amount' => $amount,
            'payment_date' => $paymentDate
        ]);

        if ($amount > 0) {
            $this->bankTransactions2($expenseId, $payType, $amount);
        }

        session()->setFlashdata('success_msg', 'Expense Paid successfully.');
        return redirect()->to(get_site_url('expenses/all'));
    }

    public function view($id = null)
    {
        if (!$id) {
            return redirect()->to(get_site_url('expenses/all'));
        }

        $suppliers = $this->elfinModel->get_result('supplier', ['is_delete' => 0], [],['id'=>'DESC']);
        $expense = $this->elfinModel->get_row('expenses', ['id' => $id]);
        $expenseItems = $this->elfinModel->get_result('expense_items', ['expense_id' => $id]);

        return view('templates/admin_template', [
            'suppliers' => $suppliers,
            'expense' => $expense,
            'expenses_items' => $expenseItems,
            'template' => 'expenses/view',
            'menu' => 'expenses',
            'active' => 'view'
        ]);
    }

    public function add()
    {
        if ($this->request->is('post')) {
            if ($this->validate([
                'expense_date' => 'required',
                'supplier_id' => 'required'
            ])) {
                $query = $this->db->query("SELECT MAX(id) as uni_number FROM expenses");
                $result = $query->getRow();
                $uniqueId = str_pad(($result->uni_number ?? 0) + 1, 3, '0', STR_PAD_LEFT);

                $insertId = $this->elfinModel->insert_data('expenses', [
                    'unique_id' => $uniqueId,
                    'expense_date' => $this->request->getPost('expense_date'),
                    'supplier_id' => $this->request->getPost('supplier_id'),
                    'remark' => $this->request->getPost('remark'),
                    'payment_status' => 'Pending',
                    'created_by' => get_session_data('id')
                ], true);

                $productId = $this->request->getPost('product_id');
                $price = $this->request->getPost('price');
                $qty = $this->request->getPost('qty');
                $subAmount = $this->request->getPost('sub_amount');

                if (!empty($productId)) {
                    foreach ($productId as $k => $v) {
                        $this->elfinModel->insert_data('expense_items', [
                            'expense_id' => $insertId,
                            'product_id' => $v,
                            'price' => $price[$k],
                            'qty' => $qty[$k],
                            'subtotal' => $subAmount[$k]
                        ]);
                    }
                }

                session()->setFlashdata('success_msg', 'Expense Added successfully.');
                return redirect()->to(get_site_url('expenses/all'));
            }
        }

        $data['suppliers'] = $this->elfinModel->get_result('supplier', ['is_delete' => 0]);
        $data['template'] = 'expenses/add';
        $data['menu'] = 'expenses';
        $data['active'] = 'add';
        return view('templates/admin_template', $data);
    }

    public function edit($id = null)
    {
        helper(['form', 'url']);

        $rules = [
            'expense_date' => 'required',
            'supplier_id'  => 'required',
        ];

        if ($this->validate($rules)) {
            $exupd = [
                'expense_date' => $this->request->getPost('expense_date'),
                'supplier_id'  => $this->request->getPost('supplier_id'),
                'remark'       => $this->request->getPost('remark'),
            ];

            $this->elfinModel->update_data('expenses', $exupd, ['id' => $id]);

            $product_id  = $this->request->getPost('product_id');
            $price       = $this->request->getPost('price');
            $qty         = $this->request->getPost('qty');
            $sub_amount  = $this->request->getPost('sub_amount');

            if (!empty($product_id)) {
                foreach ($product_id as $k => $v) {
                    $exists = $this->db->table('expense_items')
                        ->where('product_id', $v)
                        ->where('expense_id', $id)
                        ->countAllResults();

                    if ($exists > 0) {
                        $exupdii = [
                            'price'    => $price[$k],
                            'qty'      => $qty[$k],
                            'subtotal' => $sub_amount[$k],
                        ];

                        $this->elfinModel->update_data('expense_items', $exupdii, [
                            'product_id' => $v,
                            'expense_id' => $id,
                        ]);
                    } else {
                        $insertii = [
                            'expense_id' => $id,
                            'product_id' => $v,
                            'price'      => $price[$k],
                            'qty'        => $qty[$k],
                            'subtotal'   => $sub_amount[$k],
                        ];

                        $this->elfinModel->insert_data('expense_items', $insertii);
                    }
                }
            }

            $this->session->setFlashdata('success_msg', 'Expense updated successfully.');
            return redirect()->to(get_site_url('expenses/all'));
        }

        $data['suppliers'] = $this->elfinModel->get_result('supplier', ['is_delete' => 0],[], ['id'=>'DESC']);
        $data['expense'] = $this->elfinModel->get_row('expenses', ['id' => $id]);
        $data['expenses_items'] = $this->elfinModel->get_result('expense_items', ['expense_id' => $id]);

        return view('templates/admin_template', $data + [
            'template' => 'expenses/edit',
            'menu'     => 'expenses',
            'active'   => 'edit',
        ]);
    }

    public function cancel()
    {
        $id = $this->request->getPost('id');
        $this->elfinModel->delete('expenses', ['id' => $id]);
        $this->elfinModel->delete('expense_items', ['expense_id' => $id]);
        return $this->response->setJSON(['status' => 1]);
    }

    public function remove_expense_products()
    {
        $id = $this->request->getPost('id');
        $this->elfinModel->delete('expense_items', ['id' => $id]);
        return $this->response->setJSON(['status' => 1]);
    }

    public function get_expense_products()
    {
        $keyword = $this->request->getPost('keyword');

        $query = $this->db->query("
            SELECT p.id as pid, p.name as pname, p.price as pprice
            FROM products as p 
            JOIN category as c ON p.category_id = c.id 
            WHERE c.type = 2 
            AND p.name LIKE '%".$this->db->escapeLikeString($keyword)."%' ESCAPE '!'
            AND p.is_delete = 0
        ");

        $html = '';

        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $v) {
                $html .= '<tr>';
                $html .= '<th id="pname'.$v->pid.'">'.$v->pname.'</th>';
                $html .= '<td id="pprice'.$v->pid.'">'.number_format($v->pprice, 2).'</td>';
                $html .= '<td><a href="javascript:;" onclick="additem('.$v->pid.')"><i class="fa fa-plus-circle fa-lg"></i></a></td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="3">Products not found!</td></tr>';
        }

        return $this->response->setBody($html);
    }

    public function bankTransactions2($receiptNo, $payType, $amount)
    {
        $banks = get_key_value_array('banks', 'payment_type', ['id']);
        if (isset($banks[$payType])) {
            $bankId = $banks[$payType];

            $query = $this->db->table('banks')->select('current_balance')->where('id', $bankId)->get();
            $row = $query->getRow();

            if ($row) {
                $updatedAmount = $row->current_balance - $amount;
                $this->db->table('banks')->where('id', $bankId)->update(['current_balance' => $updatedAmount]);

                $this->elfinModel->insert_data('bank_transactions', [
                    'bank_id' => $bankId,
                    'added_date' => date('Y-m-d'),
                    'ref' => $receiptNo,
                    'debit_amount' => 0,
                    'credit_amount' => $amount,
                    'balance' => $updatedAmount,
                    'remark' => 'Expense',
                    'added_by' => get_session_data('id')
                ]);
            }
        }
    }

}
