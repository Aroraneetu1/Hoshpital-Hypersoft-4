<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ElfinModel;

class ProviderController extends BaseController
{
    protected $elfin_model;
    protected $db;
    protected $session;
    protected $validator;

    public function __construct()
    {
        helper(['url', 'form', 'elfin_helper']);
        set_timezone();
        $this->elfin_model = new ElfinModel();

        $this->db = db_connect();
        $this->session = session();
        $this->validator = \Config\Services::validation();

        if (!get_the_current_user(1)) {
            return redirect()->to(get_site_url('login/admin'));
        }
    }

    public function schedules()
    {
        return view('templates/admin_template', [
            'template' => 'providers/schedules',
            'menu' => 'providers',
            'active' => 'schedules',
        ]);
    }

    public function all($offset = 0)
    {
        $limit = 10;
        $users = $this->db->table('users')
            ->where(['role' => 'DOCTOR', 'is_delete' => 0])
            ->orderBy('id', 'DESC')
            ->limit($limit, $offset)
            ->get();

        $total = $this->db->table('users')->where(['role' => 'DOCTOR', 'is_delete' => 0])->countAllResults();

        $pager = \Config\Services::pager();
        $pagination = $pager->makeLinks($offset, $limit, $total);

        return view('templates/admin_template', [
            'rows' => $users->getResult(),
            'pagination' => $pagination,
            'template' => 'providers/all',
            'menu' => 'providers',
            'active' => 'all'
        ]);
    }

    public function add()
    {
        if ($this->request->is('post')) {
            $this->validator->setRules([
                'email' => 'required|valid_email',
                'first_name' => 'required',
                'last_name' => 'required',
                'gender' => 'required',
                'dob' => 'required',
                'address' => 'required',
                'country' => 'required',
                'room_number' => 'required',
                'phone_number' => 'required',
                'provider_education' => 'required',
                'services'=>'required'
            ]);

            if ($this->validator->withRequest($this->request)->run()) {
                $this->elfin_model->insert_data('users', [
                    'role' => 'DOCTOR',
                    'status' => 1,
                    'email' => $this->request->getPost('email'),
                    'first_name' => $this->request->getPost('first_name'),
                    'last_name' => $this->request->getPost('last_name'),
                    'username' => uniqid(),
                    'gender' => $this->request->getPost('gender'),
                    'dob' => $this->request->getPost('dob'),
                    'address' => $this->request->getPost('address'),
                    'state' => $this->request->getPost('state'),
                    'country' => $this->request->getPost('country'),
                    'postal_code' => $this->request->getPost('postal_code'),
                    'room_number' => $this->request->getPost('room_number'),
                    'phone_number' => $this->request->getPost('phone_number'),
                    'provider_education' => $this->request->getPost('provider_education'),
                    'provider_services' => json_encode($this->request->getPost('services')),
                    'created' => time(),
                    'is_delete' => 0,
                ]);

                $this->session->setFlashdata('success_msg', 'Added successfully.');
                return redirect()->to(get_site_url('providers/all'));
            }
        }

        return view('templates/admin_template', [
            'services' => $this->elfin_model->get_result('service_types', ['is_delete' => 0]),
            'template' => 'providers/add',
            'menu' => 'providers',
            'active' => 'add'
        ]);
    }

    public function edit($id = null)
{
    $db = \Config\Database::connect();
    $query = $db->table('users')->where(['role' => 'DOCTOR', 'id' => $id])->get();
    $data['row'] = $query->getRow();

    if (!$data['row']) {
        return redirect()->to(get_site_url('providers/all'))->with('error_msg', 'Provider not found.');
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'email' => 'required|valid_email',
        'first_name' => 'required',
        'last_name' => 'required',
        'gender' => 'required',
        'dob' => 'required',
        'address' => 'required',
        'state' => 'required',
        'country' => 'required',
        'postal_code' => 'trim',
        'room_number' => 'required',
        'phone_number' => 'required',
        'provider_education' => 'required'
    ]);

    if ($this->request->is('post') && $validation->withRequest($this->request)->run()) {
        $update = [
            'role' => 'DOCTOR',
            'status' => 1,
            'email' => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'gender' => $this->request->getPost('gender'),
            'dob' => $this->request->getPost('dob'),
            'address' => $this->request->getPost('address'),
            'state' => $this->request->getPost('state'),
            'country' => $this->request->getPost('country'),
            'postal_code' => $this->request->getPost('postal_code'),
            'room_number' => $this->request->getPost('room_number'),
            'phone_number' => $this->request->getPost('phone_number'),
            'provider_education' => $this->request->getPost('provider_education'),
            'provider_services' => json_encode($this->request->getPost('services')),
            'created' => $data['row']->created,
            'updated' => time(),
            'is_delete' => $data['row']->is_delete
        ];

        $db->table('users')->where('id', $id)->update($update);
        return redirect()->to(get_site_url('providers/all'))->with('success_msg', 'Updated successfully.');
    }

    $data['services'] = $db->table('service_types')->where('is_delete', 0)->orderBy('name', 'ASC')->get()->getResult();
    $data['template'] = 'providers/edit';
    $data['menu'] = 'providers';
    $data['active'] = 'edit';
    $data['validation'] = $validation;

    return view('templates/admin_template', $data);
}

    public function delete($id = '')
    {
        $this->elfin_model->update_data('users', ['is_delete' => 1], ['id' => $id]);
        $this->session->setFlashdata('success_msg', 'Deleted successfully.');
        return redirect()->to(get_site_url('providers/all'));
    }

    public function all_schedules()
    {
        $e_date = date('Y-m-d');
 		$data['rows'] = $this->elfin_model->get_result('provider_schedules', array('is_delete' => 0,'e_date >='=> $e_date),[],['start_time'=> 'DESC']);
		$data['template'] = 'providers/schedules/all';
		$data['menu'] = 'providers';
		$data['active'] = 'all_schedules';
    
        return view('templates/admin_template', $data);
    }    

    public function add_schedule()
    {
        $data = [
            'providers' => $this->elfin_model->get_result('users', ['role' => 'DOCTOR', 'is_delete' => 0]),
            'template' => 'providers/schedules/add',
            'menu' => 'providers',
            'active' => 'add_schedule',
            'validation' =>\Config\Services::validation()
        ];

        if ($this->request->is('post')) {
            $rules = [
                'provider_id' => 'required',
                's_date' => 'required',
                'e_date' => 'required',
                's_start' => 'required',
                's_end' => 'required'
            ];

            if (!$this->validate($rules)) {
                return view('templates/admin_template', $data);
            }

            $s_date = $this->request->getPost('s_date');
            $e_date = $this->request->getPost('e_date');
            $s_start = $this->request->getPost('s_start');
            $s_end = $this->request->getPost('s_end');

            $start_time = strtotime("$s_date $s_start");
            $end_time = strtotime("$e_date $s_end");
            $duration_sec = $end_time - $start_time;

            $insert = [
                'provider_id' => $this->request->getPost('provider_id'),
                's_date' => date('Y-m-d', strtotime($s_date)),
                'e_date' => date('Y-m-d', strtotime($e_date)),
                's_start' => $s_start,
                's_end' => $s_end,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'duration_sec' => $duration_sec,
                'is_delete' => 0,
                'created' => time(),
                'updated' => time(),
            ];

            $this->elfin_model->insert_data('provider_schedules', $insert);
            return redirect()->to(get_site_url('providers/all_schedules'))->with('success_msg', 'Added successfully.');
        }

        return view('templates/admin_template', $data);
    }

    public function edit_schedule($id = null)
    {
        $data['row'] = $this->elfin_model->get_row('provider_schedules', ['id' => $id]);

        if (!$data['row']) {
            return redirect()->to(get_site_url('providers/all_schedules'))->with('error_msg', 'Schedule not found.');
        }

        $data['providers'] = $this->elfin_model->get_result('users', ['role' => 'DOCTOR', 'is_delete' => 0]);
        $data['template'] = 'providers/schedules/edit';
        $data['menu'] = 'providers';
        $data['active'] = 'edit_schedule';
        $data['validation'] = \Config\Services::validation();

        if ($this->request->is('post')) {
            $rules = [
                'provider_id' => 'required',
                's_date' => 'required',
                'e_date' => 'required',
                's_start' => 'required',
                's_end' => 'required'
            ];

            if (!$this->validate($rules)) {
                return view('templates/admin_template', $data);
            }

            $s_date = $this->request->getPost('s_date');
            $e_date = $this->request->getPost('e_date');
            $s_start = $this->request->getPost('s_start');
            $s_end = $this->request->getPost('s_end');

            $start_time = strtotime("$s_date $s_start");
            $end_time = strtotime("$e_date $s_end");
            $duration_sec = $end_time - $start_time;

            $update = [
                'provider_id' => $this->request->getPost('provider_id'),
                's_date' => date('Y-m-d', strtotime($s_date)),
                'e_date' => date('Y-m-d', strtotime($e_date)),
                's_start' => $s_start,
                's_end' => $s_end,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'duration_sec' => $duration_sec,
                'is_delete' => $data['row']->is_delete,
                'created' => $data['row']->created,
                'updated' => time(),
            ];

            $this->elfin_model->update_data('provider_schedules', $update, ['id' => $id]);
            return redirect()->to(get_site_url('providers/all_schedules'))->with('success_msg', 'Updated successfully.');
        }

        return view('templates/admin_template', $data);
    }

    public function delete_schedule($id = null)
    {
        if ($id) {
            $this->elfin_model->update_data('provider_schedules', ['is_delete' => 1], ['id' => $id]);
            return redirect()->to(get_site_url('providers/all_schedules'))->with('success_msg', 'Deleted successfully.');
        }

        return redirect()->to(get_site_url('providers/all_schedules'))->with('error_msg', 'Invalid request.');
    }
}
