<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CronjobController extends BaseController
{
    protected $elfin_model;
    protected $session;
    protected $db;

    public function __construct()
    {
        helper('elfin_helper');
        set_timezone();
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->elfin_model = model('App\Models\ElfinModel');
    }

    public function auto_update_discharge_date()
    {
        $clinicInfo = get_clinic_info();
        $cronJob = $clinicInfo->cron_job ?? 0;

        if ($cronJob == 1) {
            $currentDate = date('Y-m-d');

            $builder = $this->db->table('inpatients');
            $builder->where('pay_status', 'Pending');
            $builder->where("check_out <=", $currentDate);
            $builder->orderBy('id', 'DESC');

            $query = $builder->get();
            $inpatientAll = $query->getResult();

            if (!empty($inpatientAll)) {
                foreach ($inpatientAll as $row) {
                    $newCheckOut = date('Y-m-d', strtotime($row->check_out . ' +1 day'));
                    $newRoomRateAmt = $row->room_rate_amt + $row->room_rate;
                    $newDayDiff = $row->dayDiff + 1;

                    $updateData = [
                        'check_out' => $newCheckOut,
                        'room_rate_amt' => $newRoomRateAmt,
                        'dayDiff' => $newDayDiff
                    ];

                    $this->elfin_model->update_data('inpatients', $updateData, ['id' => $row->id]);
                }
            }

            echo 1;
        }

        echo 1;
    }

    public function manual_update_discharge_date()
    {
        $currentDate = date('Y-m-d');

        $builder = $this->db->table('inpatients');
        $builder->where('pay_status', 'Pending');
        $builder->where("check_out <=", $currentDate);
        $builder->orderBy('id', 'DESC');

        $query = $builder->get();
        $inpatientAll = $query->getResult();

        if (!empty($inpatientAll)) {
            foreach ($inpatientAll as $row) {
                $newCheckOut = date('Y-m-d', strtotime($row->check_out . ' +1 day'));
                $newRoomRateAmt = $row->room_rate_amt + $row->room_rate;
                $newDayDiff = $row->dayDiff + 1;

                $updateData = [
                    'check_out' => $newCheckOut,
                    'room_rate_amt' => $newRoomRateAmt,
                    'dayDiff' => $newDayDiff
                ];

                $this->elfin_model->update_data('inpatients', $updateData, ['id' => $row->id]);
            }

            session()->setFlashdata('success_msg', 'Cron job run successfully.');
        } else {
            session()->setFlashdata('error_msg', 'Nothing to update, all data updated already.');
        }

        return redirect()->to(get_site_url('appointments/allinpatient'));
    }
}
