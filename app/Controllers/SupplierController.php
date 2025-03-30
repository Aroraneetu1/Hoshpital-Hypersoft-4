<?php

namespace App\Controllers;

use App\Models\ElfinModel;
use App\Controllers\BaseController;

class SupplierController extends BaseController
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
        $elfinModel = new ElfinModel();
        $data['supplier'] = $elfinModel->get_result('supplier', ['is_delete' => 0],[], ['id'=>'DESC']);
        $data['template'] = 'supplier/all';
        $data['menu'] = 'supplier';
        $data['active'] = 'all';

        return view('templates/admin_template', $data);
    }

    public function add()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'    => 'required',
            'address' => 'required',
            'contact' => 'required',
        ]);

        if ($this->request->is('post') && $validation->withRequest($this->request)->run()) {
            $elfinModel = new ElfinModel();
            $insert = [
                'name'    => $this->request->getPost('name'),
                'address' => $this->request->getPost('address'),
                'contact' => $this->request->getPost('contact'),
            ];

            $elfinModel->insert_data('supplier', $insert);
            return redirect()->to(get_site_url('supplier/all'))->with('success_msg', 'Supplier Added successfully.');
        }

        $data['template'] = 'supplier/add';
        $data['menu'] = 'supplier';
        $data['active'] = 'add';

        return view('templates/admin_template', $data);
    }

    public function edit($id = '')
    {
        $elfinModel = new ElfinModel();
        $data['row'] = $elfinModel->get_row('supplier', ['id' => $id]);

        if (!$data['row']) {
            return redirect()->to(get_site_url('supplier/all'))->with('error_msg', 'Supplier not found.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'    => 'required',
            'address' => 'required',
            'contact' => 'required',
        ]);

        if ($this->request->is('post') && $validation->withRequest($this->request)->run()) {
            $update = [
                'name'    => $this->request->getPost('name'),
                'address' => $this->request->getPost('address'),
                'contact' => $this->request->getPost('contact'),
            ];

            $elfinModel->update_data('supplier', $update, ['id' => $id]);
            return redirect()->to(get_site_url('supplier/all'))->with('success_msg', 'Supplier Updated successfully.');
        }

        $data['template'] = 'supplier/edit';
        $data['menu'] = 'supplier';
        $data['active'] = 'edit';

        return view('templates/admin_template', $data);
    }

    public function cancel()
    {
        $id = $this->request->getPost('id');
        $elfinModel = new ElfinModel();
        $elfinModel->update('supplier', ['is_delete' => 1], ['id' => $id]);

        return $this->response->setJSON(['status' => 1]);
    }
}
