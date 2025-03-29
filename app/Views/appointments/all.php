<?php 

$permissions = unserialize(get_session_data('permissions'));
$settingspp = isset($permissions['settings']) ? $permissions['settings'] : 0;
$salespay = isset($permissions['salespay']) ? $permissions['salespay'] : 0;

$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name')); 
$providers = get_key_value_array('users', 'id', array('first_name', 'last_name')); 
$services = get_key_value_array('service_types', 'id', array('name'));

//$labstatus = get_appointments_lab_pay_status(184);

/*echo '<pre>';
print_r($permissions);
echo '</pre>';*/

?>

<section id="unseen">
    <table id="table-db-js" class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Service</th>
                <th>Token No.</th>
                <th>Appointment Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($appointments): ?>
                <?php foreach($appointments as $row): 

                    $paystatus = get_appointments_pay_status($row->id); 
                    $labstatus = get_appointments_lab_pay_status($row->id);  ?>

                    <tr>
                        <td><?php echo @$consumers[$row->consumer_id]; ?></td>
                        <td><?php echo @$providers[$row->provider_id]; ?></td>
                        <td><?php echo @$services[$row->service_id]; ?></td>
                        <td><?php echo @$services[$row->service_id].' - '.$row->token_number; ?></td>
                        <td>
                            <?php //echo date('Y/m/d h:i A', $row->start_time); 
                                echo $row->start_time; ?> 
                        </td>
                        <td>
                            <?php 
                                switch($row->status){
                                    case '0':
                                        echo 'Canceled';
                                    break;
                                    case '1':
                                        echo 'Awaiting';
                                    break;
                                    case '2':
                                        echo 'Active';
                                    break;
                                    case '3':
                                        echo 'Completed';
                                    break;
                                    default:
                                        echo 'Pending';
                                    break;
                                }
                            ?>
                        </td>
                        <td>
                            <?php //if($permissions['appointments'] == 1){?>

                                <?php if($row->status == 0): ?>
                                    -
                                <?php elseif($row->status == 1): ?>

                                    <a style="margin-bottom: 5px;<?php if(in_array(0, $paystatus)){echo 'pointer-events: none;opacity: 0.4';}?>" class="btn btn-info btn-sm loader-activate" href="<?php echo get_site_url('appointments/visit/'.$row->id.'/start'); ?>">
                                        Start Visit
                                    </a>

                                    <!-- <a style="margin-bottom: 5px;" href="<?php echo get_site_url("appointments/receipt/".$row->id."/fee")?>" class="btn btn-primary btn-sm loader-activate"> Receipt</a> -->

                                    <?php if($salespay == 1){ ?>

                                        <a style="margin-bottom: 5px;" href="<?php echo get_site_url("appointments/pay/".$row->id."")?>" class="btn btn-success btn-sm loader-activate"> <?php if(in_array(0, $paystatus) || in_array(0, $labstatus)){echo 'Pay';}else{echo 'Paid';}?></a>
                                        
                                    <?php } ?>

                                    <?php if($settingspp == 1){?>

                                    <a style="margin-bottom: 5px;" class="btn btn-danger btn-sm" onclick="cancelApp(<?php echo $row->id?>)" href="javascript:;">
                                        Cancel
                                    </a>

                                    <?php } ?>

                                <?php elseif($row->status == 2): ?>

                                    <a style="margin-bottom: 5px;" class="btn btn-primary btn-sm loader-activate" href="<?php echo get_site_url('appointments/visit/'.$row->id); ?>">
                                        Continue Visit
                                    </a>

                                    <!-- <a style="margin-bottom: 5px;" href="<?php echo get_site_url("appointments/receipt/".$row->id."/lab")?>" class="btn btn-info btn-sm loader-activate"> Receipt</a> -->

                                    <?php if($settingspp == 1){?>

                                        <a style="margin-bottom: 5px;" class="btn btn-danger btn-sm" onclick="cancelApp(<?php echo $row->id?>)" href="javascript:;">
                                            Cancel
                                        </a>
                                    <?php } ?>

                                    <!--<a style="margin-bottom: 5px;" class="btn btn-warning btn-sm" onclick="fzPrint('print-appointment-<?php echo $row->id; ?>', 'Report');" href="javascript: void(0);">
                                        Print
                                    </a>-->

                                    <a style="margin-bottom: 5px;" class="btn btn-warning btn-sm" onclick="prescriptionPrint(<?php echo $row->id; ?>);" href="javascript: void(0);">
                                        Print
                                    </a>

                                    <?php if(get_session_data('role') != 'Receptionist'){ ?>

                                        <a style="margin-bottom: 5px;<?php if(in_array(0, $labstatus)){echo 'pointer-events: none;opacity: 0.4';}?>" href="<?php echo get_site_url("appointments/laboratory/".$row->id."")?>" class="btn btn-info btn-sm loader-activate"> Lab</a>

                                    <?php } ?>

                                    <?php if($salespay == 1){ ?>

                                        <a style="margin-bottom: 5px;" href="<?php echo get_site_url("appointments/pay/".$row->id."")?>" class="btn btn-success btn-sm loader-activate"> <?php if(in_array(0, $paystatus) || in_array(0, $labstatus)){echo 'Pay';}else{echo 'Paid';}?></a>
                                        
                                    <?php } ?>

                                <?php else: ?>
                                    <a style="margin-bottom: 5px;" class="btn btn-success btn-sm loader-activate" href="<?php echo get_site_url('appointments/visit/'.$row->id); ?>">
                                        View
                                    </a>
                                    <!--<a style="margin-bottom: 5px;" class="btn btn-warning btn-sm" onclick="fzPrint('print-appointment-<?php echo $row->id; ?>', 'Report');" href="javascript: void(0);">
                                        Print
                                    </a>-->
                                    <a style="margin-bottom: 5px;" class="btn btn-warning btn-sm" onclick="prescriptionPrint(<?php echo $row->id; ?>);" href="javascript: void(0);">
                                        Print
                                    </a> 
                                <?php endif; ?>    

                                <!-- <a class="btn btn-info loader-activate" href="<?php //echo get_site_url('appointments/edit/'.$row->id); ?>">
                                    Edit
                                </a> -->
                                <!-- <a class="btn btn-danger" onclick="return confirm('Are you sure?');" href="<?php //echo get_site_url('appointments/delete/'.$row->id); ?>">
                                    Cancel
                                </a> -->
                            <?php //} ?> 
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">
                        No record found.
                    </td>
                </tr>    
            <?php endif; ?>
        </tbody>
    </table>
</section>


<script type="text/javascript">
$(document).ready(function(){
    $("#table-db-js thead th").each(function(){
        var title = $(this).text();
        if(title == "Appointment Time" || title == "Status" || title == "Doctor" || title == "Patient"){
        	$(this).append('<input class="fz-col-filter">');
        }
    });
    var table = $("#table-db-js").DataTable({
        paging: true,
        info: false,
        sorting: false,
        pageLength: 6,
        columnDefs: [
            { orderable: false, targets: '_all' }
        ],
    });
    table.columns().every(function(){
        var that = this;
        $("input.fz-col-filter", this.header()).on("keyup change", function(){
            if(that.search() !== this.value){
                that.search( this.value ).draw();
            }
        });
    });
    $(".dataTables_wrapper .dataTables_filter").hide();


    
});

function prescriptionPrint(appid) {

    $.ajax({
        type : 'POST',
        url: '<?php echo get_site_url('appointments/ajax_print_prescription'); ?>',
        data: {appointment_id: appid},
        success: function(result){

            $('#prescptnPrint div').remove();
            $('#prescptnPrint').html(result);

            setTimeout(function(){
               fzPrint('pp', 'Report');
            }, 500);

            
        }
    })

}

function cancelApp(appid) {

    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this appointment!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                type : 'POST',
                url: '<?php echo get_site_url('appointments/cancel_visit'); ?>',
                data: {id: appid},
                success: function(result){

                    Swal.fire({
                      title: "Deleted!",
                      text: "Your appointment has been deleted.",
                      icon: "success"
                    });

                    setTimeout(function(){
                       window.location.reload();
                    }, 500);
                }
            })
        }
    });

   
}
</script>

<style type="text/css">
.fz-col-filter{
	font-weight: normal; 
	width: 100%; 
	border: none; 
	background-color: #eee; 
	height: 35px;
	display: block;
}

.paginate_button{
    padding: 0.2em 0.3em !important;
}

.next{
    background: transparent;
}
.dataTables_paginate {
    width: 20%;
}
.dataTables_length {
   display: none;
}
</style>

<!-- <div class="hidden">
	<?php// $this->load->view('appointments/modal_view_visits_by_patient', array('appointments' => $appointments)); ?>
</div> -->

<div class="hidden" id="prescptnPrint">
    
</div>