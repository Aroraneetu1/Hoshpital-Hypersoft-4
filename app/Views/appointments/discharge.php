<?php

    $get_clinic_info = get_clinic_info();

    $consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));
    $users = get_key_value_array('users', 'id', array('first_name', 'last_name'));
    $userscon = get_key_value_array('users', 'id', array('phone_number'));

    $get_patient_data = get_patient_data($inpatients->patient_id);
    $get_appointment_data = get_appointment_data($inpatients->appointment_id);

    /*echo '<pre>';
    print_r($get_patient_data);
    echo '</pre>';*/
?>
<style type="text/css">
    b{
        font-weight: 600;
    }
</style>
<section id="unseen">

    <?php include('inpatient_div.php'); ?>

    <div id="dischargeFrm">
        <div class="row">
            <div class="col-md-12">
                <a type="button" class="btn btn-danger pull-right" onclick="fzPrint('sectiondata', 'Report')"> Print</a>
            </div>
        </div>

        <div class="row" id="sectiondata" style="margin-top: 10px;">
            <div class="col-md-12">
                
                <table class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 25%;">
                                <img src="<?php echo str_replace('index.php/', '', get_site_url().''.$get_clinic_info->logo)?>" alt="Hospital Logo" style="width: 130px;">
                            </td>
                            <th class="text-center"  style="vertical-align: top;"><b style="font-size: 20px;s">Patient Discharge Form</b></th>
                        </tr>
                        <tr>
                            <td colspan="2"><b>Hospital information</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Hospital/facility name</td>
                            <td><?php echo $get_clinic_info->name;?></td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td><?php echo $get_clinic_info->address;?></td>
                        </tr>
                        <tr>
                            <td>Emergency contact information</td>
                            <td><?php echo $get_clinic_info->contact;?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr>
                            <td colspan="2"><b>Patient information</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Name</td>
                            <td><?php echo @$consumers[$inpatients->patient_id];?></td>
                        </tr>
                        <tr>
                            <td>Date of birth</td>
                            <td><?php echo $get_patient_data->dob;?></td>
                        </tr>
                        <tr>
                            <td>Medical record number</td>
                            <td><?php echo get_formated_id('P', $get_patient_data->id);?></td>
                        </tr>
                        <tr>
                            <td>Contact information</td>
                            <td><?php echo $get_patient_data->phone_number;?></td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td><?php echo $get_patient_data->address.' '.$get_patient_data->city.' '.$get_patient_data->state.' '.$get_patient_data->country.' '.$get_patient_data->postal_code;?></td>
                        </tr>
                        <tr>
                            <td>Primary care physician</td>
                            <td><?php echo @$users[$inpatients->doctor_id];?></td>
                        </tr>
                        <tr>
                            <td>Contact information</td>
                            <td><?php echo @$userscon[$inpatients->doctor_id];?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr>
                            <td colspan="2"><b>Admission and discharge details</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Admission date</td>
                            <td><?php echo $inpatients->check_in;?></td>
                        </tr>
                        <tr>
                            <td>Reason for admission</td>
                            <td><?php echo $inpatients->admission_reason;?></td>
                        </tr>
                        <tr>
                            <td>Diagnosis</td>
                            <td><?php echo $get_appointment_data->remark;?></td>
                        </tr>
                        <tr>
                            <td>Treatments received</td>
                            <td><?php echo $get_appointment_data->notes;?></td>
                        </tr>
                        <tr>
                            <td>Discharge summary</td>
                            <td><?php echo $inpatients->remark;?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr>
                            <td colspan="2"><b>Patient acknowledgment</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2">I, the undersigned, acknowledge that I have received and understand the information provided in this discharge form. I am aware of the follow-up appointments, medications, and care instructions.</td>
                        </tr>
                        <tr>
                            <td>Patient name</td>
                            <td><?php echo @$consumers[$inpatients->patient_id];?></td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td><?php echo date('Y-m-d');?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</section>