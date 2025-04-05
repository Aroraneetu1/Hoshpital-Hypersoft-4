<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ElfinModel;

class ServiceTypeController extends BaseController
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

    public function all($offset = 0)
{
    $elfinModel = new ElfinModel();
    $data['rows'] = $elfinModel->get_result('service_types', ['is_delete' => 0]);
    $data['template'] = 'service_types/all';
    $data['menu'] = 'service_types';
    $data['active'] = 'all';
    
    return view('templates/admin_template', $data);
}

public function add()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'name'        => 'required',
            'description' => 'required',
            'amount'      => 'required',
            'duration'    => 'required|integer',
        ]);

        if ($this->request->is('post') && $validation->withRequest($this->request)->run()) {
            $elfinModel = new ElfinModel();
            $insert = [
                'name'         => $this->request->getPost('name'),
                'description'  => $this->request->getPost('description'),
                'duration_min' => $this->request->getPost('duration'),
                'duration_sec' => $this->request->getPost('duration') * 60,
                'amount'       => $this->request->getPost('amount'),
                'is_delete'    => 0,
            ];

            $elfinModel->insert_data('service_types', $insert);
            session()->setFlashdata('success_msg', 'Added successfully.');
            return redirect()->to(get_site_url('service_types/all'));
        }

        $data['template'] = 'service_types/add';
        $data['menu'] = 'service_types';
        $data['active'] = 'add';

        return view('templates/admin_template', $data);
    }

    public function edit($id = '')
    {
        $elfinModel = new ElfinModel();
        $data['row'] = $elfinModel->get_row('service_types', ['id' => $id]);

        if (!$data['row']) {
            return redirect()->to(get_site_url('service_types/all'))->with('error_msg', 'Record not found.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'        => 'required',
            'description' => 'required',
            'amount'      => 'required',
            'duration'    => 'required|integer',
        ]);

        if ($this->request->is('post') && $validation->withRequest($this->request)->run()) {
            $update = [
                'name'         => $this->request->getPost('name'),
                'description'  => $this->request->getPost('description'),
                'duration_min' => $this->request->getPost('duration'),
                'duration_sec' => $this->request->getPost('duration') * 60,
                'amount'       => $this->request->getPost('amount'),
                'is_delete'    => 0,
            ];

            $elfinModel->update_data('service_types', $update, ['id' => $id]);
            return redirect()->to(get_site_url('service_types/all'))->with('success_msg', 'Updated successfully.');
        }

        $data['template'] = 'service_types/edit';
        $data['menu'] = 'service_types';
        $data['active'] = 'edit';

        return view('templates/admin_template', $data);
    }

    public function delete($id=''){
	    $this->elfinModel->update_data('service_types', array('is_delete' => 1), array('id' => $id));
        return redirect()->to(get_site_url('service_types/all'))->with('success_msg', 'Deleted successfully.');
	}

	public function file_validation($post=NULL, $parameter){
		list($file,$required,$types,$size) = explode(';',$parameter);
		if($required != ''){ 
			if($_FILES[$file]['name'] == ''){
				$this->form_validation->set_message('file_validation','Please select an file to upload.');
				return false;
			}
		}
		
		if($_FILES[$file]['name'] == ''){
			return true;
		}
		
		if($types != ''){
			$format = strtolower(pathinfo($_FILES[$file]['name'],PATHINFO_EXTENSION));
			$types_array = explode(',',$types);
			if(!in_array($format,$types_array)){
				$this->form_validation->set_message('file_validation','File format not allowed.');
				return false;
			}
		}
		
		if($size != ''){
			$actual_size = $_FILES[$file]['size']/1048576;
			if($actual_size > $size){
				$this->form_validation->set_message('file_validation','File not allowed which is large than '.$size.' MB.');
				return FALSE;
			}
		}
		return true;
	}

}
