<?php 

$permissions = unserialize(get_session_data('permissions'));

$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));

$providers = get_key_value_array('users', 'id', array('first_name', 'last_name'));

$services = get_key_value_array('service_types', 'id', array('name'));

$products = get_service_products();
$pro_chk_type = get_key_value_array('products', 'id', array('has_subproduct'));
$subproduct_of = get_key_value_array('products', 'id', array('subproduct_of'));


$vital_index = json_decode($row->vital_index, TRUE);

$vital_value = json_decode($row->vital_value, TRUE);

if(!is_array($vital_index) || count($vital_index) == 0 || !is_array($vital_value) || count($vital_value) == 0 || count($vital_index) != count($vital_value)){

    $vital_index = array(

        'Temperature (C)',

        'Blood Pressure (low/high)',

        'Pulse (/min)',

        'Weight (kg)',

        'Height (cm)',

        'Respiratory Rate (/min)',

        'Blood Oxygen Saturation (%)',

    );

    $vital_value = array('', '', '', '', '', '', '');

}

$sel_prodct = array();


/*echo '<pre>';
print_r($products);
print_r($pro_chk_type);
echo '</pre>';*/

?>


<style>
    @media print {
      .hide-on-print, .form, .panel-heading {
        display: none;
      }

      .container1 {
        display: block;
        margin-top: 0;
      }
    }

    strong, th{
        font-weight: 600;
    }

    .container1 .lab-result-container {
        border: 1px solid #000;
        padding: 0px;
        margin: 0px;
    }

    .container1 .table-bordered td, .container1 .table-bordered th {
        border: 1px solid #000 !important;
    }

    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 2px 8px !important;
    }

    
</style>
<div class="form">

    <form action="" method="post">
       
        <div class="form-group">

            <div class="col-lg-6 col-md-6 col-sm-12 col-xm-12">

                <table class="table table-bordered">

                    <thead>

                        <tr>

                            <th colspan="2">

                                <i class="fa fa-clock-o"></i> 

                                Appointment Details

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    	<tr>

                    		<td>Visit Status</td>

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

                    	</tr>

                    	<tr>

                    		<td>Visit Start Time</td>

                    		<td><?php echo date('Y-m-d h:i A', $row->visited_start_time); ?></td>

                    	</tr>

                    	<tr>

                    		<td>Visit End Time</td>

                    		<td>

                    			<?php if($row->status == 2): ?>

	                                Active

                    			<?php elseif($row->status == 3): ?>

                    				<?php echo date('Y-m-d h:i A', $row->visited_end_time); ?>				

                    			<?php else: ?>

                    				-

                    			<?php endif; ?>

                    		</td>

                    	</tr>

                        <tr>

                            <td>Patient</td>

                            <td><?php echo $consumers[$row->consumer_id]; ?></td>

                        </tr>

                        <tr>

                            <td>Doctor</td>

                            <td><?php echo $providers[$row->provider_id]; ?></td>

                        </tr>

                        <tr>

                            <td>Service</td>

                            <td><?php echo $services[$row->service_id]; ?></td>

                        </tr>
                        <tr>

                            <td>Token Number</td>

                            <td><?php echo $services[$row->service_id].' - '.$row->token_number; ?></td>

                        </tr>

                        <tr>
                            
                            <td colspan="2"> 
                                <span> 
                                    <input type="hidden" name="inpatient_chkbx" value="0">
                                    <input type="checkbox" name="inpatient_chkbx" value="1" <?php if($row->inpatient_chkbx  == 1){echo 'checked';}?>>
                                    In-Patient
                                </span>

                                <span style="margin-left:30px;"> 
                                    <input type="hidden" name="operationward_chkbx" value="0">
                                    <input type="checkbox" name="operationward_chkbx" value="1" <?php if($row->operationward_chkbx  == 1){echo 'checked';}?>>
                                    Operation
                                </span>

                                <span style="margin-left:30px;"> 
                                    <input type="hidden" name="emergency_chkbx" value="0">
                                    <input type="checkbox" name="emergency_chkbx" value="1" <?php if($row->emergency_chkbx  == 1){echo 'checked';}?>>
                                    Emergency
                                </span>
                            </td>
                        </tr>

                        <tr>

                        	<td colspan="2">

                        		<i class="fa fa-lightbulb-o"></i> Remark

                                <textarea rows="4" name="remark" class="form-control"><?php echo $row->remark; ?></textarea>

                        	</td>

                        </tr>

                    </tbody>

                    <tfoot>

                    	<tr>

                    		<td colspan="2">

                                <?php if($permissions['visit'] == 1){?>

    								<?php if(in_array($row->status, array(1, 2))): ?>

    									<!-- <button value="end" name="mode" type="submit" class="btn btn-warning btn-sm pull-right">
    									    End Visit
    									</button> -->

                                        <?if($inpatient_data == 1){?>

                                            <button type="button" onclick="endVisit_warning('In-patients payment not paid yet!')" class="btn btn-warning btn-sm pull-right">End Visit</button>

                                        <? }else if($operation_data == 1){?>

                                            <button type="button" onclick="endVisit_warning('Operation payment not paid yet!')" class="btn btn-warning btn-sm pull-right">End Visit</button>

                                        <? }else{ ?>

                                            <button type="button" onclick="endVisit(<?php echo $row->id;?>)" class="btn btn-warning btn-sm pull-right">End Visit </button>

                                        <? }?>

    								<?php endif; ?>

    								

    								<button value="save" name="mode" type="submit" class="btn btn-success btn-sm pull-right">

    								    Save Changes

    								</button>

    								<button type="button" class="btn btn-info btn-sm pull-right" data-toggle="modal" data-target="#patient-history-modal">

    									Patient History

    								</button>

                                    <a type="button" class="btn btn-danger btn-sm pull-right" onclick="fzPrint('prescriptionResult', 'Report');"> Print</a>
                                    <!-- <a type="button" class="btn btn-danger btn-sm pull-right" onclick="print_prescription();"> Print</a> -->

                                    <?php if($row->status == 2){ ?>
                                        <button type="button" class="btn btn-primary btn-sm pull-left" data-toggle="modal" data-target="#Laboratory-Services-modal">

                                            Laboratory Services

                                        </button>
                                    <?php } ?>

                                <?php } ?>


                    		</td>

                    	</tr>

                    </tfoot>	

                </table>

            </div>    

            <div class="col-lg-6 col-md-6 col-sm-12 col-xm-12">

                <table class="table table-bordered">

                    <thead>

                        <tr>

                            <td style="background-color: #337ab7; color: #fff;">

                                <i class="fa fa-file"></i>

                                Prescription     

                            </td>

                        </tr>

                    </thead>

                    <tbody>

                        <tr>

                            <td>

                            	<?php if(in_array($row->status, array(1, 2))): ?>

                                	<textarea rows="4" name="notes" class="form-control" ><?php echo $row->notes; ?></textarea>

                            	<?php else: ?>	

                                	<textarea name="notes" style="display: none;"><?php echo $row->notes; ?></textarea>

                                	<?php echo $row->notes; ?>

                            	<?php endif; ?>	

                            </td>

                        </tr>

                    </tbody>

                </table>

            	<table class="table table-bordered">

                    <thead>

                        <tr>

                            <th colspan="3" style="background-color: #d9534f; color: #fff;">

                                <i class="fa fa-search"></i> Patient Vitals

                            </th>

                        </tr>

                    </thead>

                    <tbody id="tbody-vitals">

	                    <?php if(in_array($row->status, array(1, 2))): ?>

	                        <?php foreach($vital_index as $i => $vindex): ?>

	                    		<tr>

	                    			<td>

	                                    <input name="vital_index[]" value="<?php echo $vindex; ?>" class="form-control" style="background-color: #fff; border: none; box-shadow: none; border-bottom: 1px dashed #ddd;" >

	                                </td>

	                    			<td>

	                    				<input name="vital_value[]" value="<?php echo $vital_value[$i]; ?>" class="form-control" >

	                    			</td>

	                                <td>
                                        <?php if($permissions['visit'] == 1){?>
    	                                    <a onclick="$(this).parents('tr').remove();" href="javascript: void(0);" style="color: red;">

    	                                        <i class="fa fa-trash"></i>

    	                                    </a>
                                        <?php } ?>

	                                </td>

	                    		</tr>

	                        <?php endforeach; ?>

	                    <?php else: ?>	

	                        <?php foreach($vital_index as $i => $vindex): ?>

	                    		<tr>

	                    			<td colspan="2">

	                                    <input name="vital_index[]" value="<?php echo $vindex; ?>" type="hidden">

	                                    <?php echo $vindex; ?>

	                                </td>

	                    			<td>

	                    				<input name="vital_value[]" value="<?php echo $vital_value[$i]; ?>" type="hidden">

	                    				<?php echo $vital_value[$i]; ?>

	                    			</td>

	                    		</tr>

	                        <?php endforeach; ?>

	                    <?php endif; ?>	

                    </tbody>

                    <?php if(in_array($row->status, array(1, 2))): ?>

	                    <tfoot>

	                        <tr>

	                            <td colspan="3">

                                    <?php if($permissions['visit'] == 1){?>

    	                                <a href="javascript: void(0);" class="btn btn-danger btn-sm pull-right" id="add-more-vital-btn">

    	                                    <i class="fa fa-plus"></i>

    	                                </a>
                                    <?php } ?>

	                            </td>

	                        </tr>

	                    </tfoot>

	                <?php endif; ?>    

                </table>

            </div>

        </div>

        <div class="form-group">

            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">

                <label>&nbsp;</label>

            </div>

        </div>

   </form>

</div>




<!--============== For Print prescription only ===============-->
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
<div class="hidden">

    <div class="container1 lab-result-container" id="prescriptionResult" >

        <?php echo get_print_header();?>

        <div style="text-align: center; margin-bottom: 20px;font-size: 20px;"><strong>Doctor Prescription</strong></div>

        <div class="row">
            <div class="col-md-12" style="margin-bottom: 20px;">
                <p><strong>Patient ID:</strong> <?php echo get_formated_id('P', $row->consumer_id); ?></p>
                <p><strong>Patient Name:</strong> <?php echo $consumers[$row->consumer_id];?></p>
                <p><strong>Service:</strong> <?php echo $services[$row->service_id];?></p>
                <!-- <p><strong>Appointment No:</strong> <?php echo $services[$row->service_id];?> - <?php echo $row->token_number;?></p> -->
                <p><strong>Date:</strong> <?php echo date('d M, Y')?></p>
            </div>
        </div>

        <div class="row" >
            <div class="col-md-12" style="margin-bottom: 10px;padding-top: 5px;">
                <p><strong>Investigation</strong> </p>
                <p style="margin-top: 10px;"></p>
                <p style="margin-bottom: 150px;"><?php echo nl2br($row->remark); ?> </p>
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-12" style="margin-bottom: 30px;padding-top: 5px;">
                <p><strong>Prescription</strong> </p>
                <p style="margin-top: 10px;"></p>
                <p style="margin-bottom: 100px;"><?php echo nl2br($row->notes); ?> </p>
                
            </div>
        </div>


        <div style="position: fixed;bottom: 20px;left: 65px;width: 100%;text-align: left;">
            <p><strong>Doctor’s Signature:</strong><span style="border-bottom: 1px solid #000;"> <?php echo $providers[$row->provider_id]; ?></span></p>
        </div>

    </div>

</div>
<!--============== For Print prescription only ===============-->



<script type="text/javascript">

$(document).on("click", "#add-more-vital-btn", function(){

    var html = '';

    html += '<tr>';

    html += '    <td>';

    html += '        <input name="vital_index[]" value="" class="form-control" style="background-color: #fff; border: none; box-shadow: none; border-bottom: 1px dashed #ddd;" required>';

    html += '    </td>';

    html += '    <td>';

    html += '        <input name="vital_value[]" value="" class="form-control" >';

    html += '    </td>';

    html += '    <td>';

    html += '        <a onclick="$(this).parents(\'tr\').remove();" href="javascript: void(0);" style="color: red;">';

    html += '            <i class="fa fa-trash"></i>';

    html += '        </a>';

    html += '    </td>';

    html += '</tr>';

    $("#tbody-vitals").append(html);

})

function endVisit_warning(msg){

    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: msg,
      //footer: '<a href="#">Why do I have this issue?</a>'
    });
}

function endVisit(appid){

    Swal.fire({
      title: "Are you sure?",
      text: "To end the patient visit?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, end it!"
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                type : 'POST',
                url: '<?php echo get_site_url('appointments/end_visit'); ?>',
                data: {id: appid},
                success: function(result){

                    Swal.fire({
                      title: "Completed!",
                      text: "Visit has been ended successfully.",
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





<div class="modal fade" id="patient-history-modal" role="dialog">

    <div class="modal-dialog modal-lg">

      	<div class="modal-content">

    		<div class="modal-header">

          		<button type="button" class="close" data-dismiss="modal">&times;</button>

          		<h4 class="modal-title">Patient History</h4>

        	</div>

        	<div class="modal-body">

				<?php echo $modal; ?>

        	</div>

    		<div class="modal-footer">

          		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

        	</div>

      	</div>	

    </div>

</div>


<div class="modal fade" id="Laboratory-Services-modal" role="dialog">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <form action="<?php echo get_site_url("appointments/lab_services");?>" method="post">
                <input type="hidden" name="appointment_id" value="<?php echo $this->uri->segment(3);?>">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                    <h4 class="modal-title">Laboratory Services</h4>

                </div>

                <div class="modal-body">

                    <table class="table table-bordered table-striped table-condensed">
                       <!--  <thead>
                            <tr>
                                
                                <th colspan="5">Laboratory Services Name</th>
                            </tr>
                        </thead> -->
                        <tbody>

                            <?php 

                                $counter = 0;

                                foreach ($products as $key => $value) {

                                    $onclickfn = '';
                                    if($pro_chk_type[$key] == 1){
                                        $onclickfn = 'onclick="checkother(this)"';
                                    }
                                    
                                    if ($counter % 5 == 0) {
                                        echo "<tr>";
                                    }
                                    
                                    echo '<td> <input type="checkbox" name="product_Ids[]" class="subp'.$subproduct_of[$key].'" value="'.$key.'" style="margin-left:5px;margin-right:5px;" '.$onclickfn.'> '.$value.'</td>';
                                    
                                    $counter++;
                                    
                                    
                                    if ($counter % 5 == 0) {
                                        echo "</tr>";
                                    }
                                    
                                }

                                
                                if ($counter % 5 != 0) {
                                    echo "</tr>";
                                }


                                //======== for special case =======//
                                /*$counter1 = 0;
                                foreach ($products as $key => $value) {

                                    if($pro_chk_type[$key] == 1){
                                    
                                        if ($counter1 % 5 == 0) {
                                            echo "<tr>";
                                        }
                                        
                                        echo '<td> <input type="checkbox" name="product_Ids[]" value="'.$key.'" style="margin-left:5px;margin-right:5px;"> '.$value.'</td>';
                                        
                                        $counter1++;
                                        
                                        
                                        if ($counter1 % 5 == 0) {
                                            echo "</tr>";
                                        }
                                    }
                                }

                                
                                if ($counter1 % 5 != 0) {
                                    echo "</tr>";
                                }*/

                            ?>
                            
                        </tbody>
                    </table>

                </div>

                <div class="modal-footer">

                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
            </form>

        </div>  

    </div>

</div>

<script type="text/javascript">
    
    function checkother(that){

        var key = that.value; //alert(key);
        if($(that).is(':checked')){
            $('.subp'+key).prop('checked',true);
        }else{
            $('.subp'+key).prop('checked',false);
        }
    }
</script>



