<?php 



$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));

$providers = get_key_value_array('users', 'id', array('first_name', 'last_name'));

$services = get_key_value_array('service_types', 'id', array('name'));

$products = get_key_value_array('products', 'id', array('name'));

?>



<?php if($appointments): ?>

	<?php foreach($appointments as $appointment): ?>

		<?php

			$vital_index = json_decode($appointment->vital_index, TRUE);

			$vital_value = json_decode($appointment->vital_value, TRUE);

			if(!is_array($vital_index) || count($vital_index) == 0 || !is_array($vital_value) || count($vital_value) == 0 || count($vital_index) != count($vital_value)){

			    $vital_index = array(

			        'Height (cm)',

			        'Weight (kg)',

			        'Temperature (C)',

			        'Pulse (/min)',

			        'Respiratory Rate (/min)',

			        'Blood Pressure (low/high)',

			        'Blood Oxygen Saturation (%)',

			    );

			    $vital_value = array('', '', '', '', '', '', '');

			}

			$get_appointments_result = get_appointments_result($appointment->id);

		?>

		<div id="print-appointment-<?php echo $appointment->id; ?>" >

	        <table class="table table-bordered">

	            <thead>

	                <tr>

	                    <th colspan="2" style="background-color: #f9f3a6; color: #000;">

	                        <i class="fa fa-clock-o"></i> 

	                		<?php echo date('Y-m-d h:i A', $appointment->visited_start_time); ?> 

	                			- 

		        			<?php if($appointment->visited_end_time !=''){echo date('Y-m-d h:i A', $appointment->visited_end_time);} ?>

	                    </th>

	                </tr>

	            </thead>

	            <tbody>

	            	<tr>

	            		<td>Visit Status</td>

	            		<td>

	            			<?php 

	                            switch($appointment->status){

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

	                    <td>Patient</td>

	                    <td><?php echo $consumers[$appointment->consumer_id]; ?></td>

	                </tr>

	                <tr>

	                    <td>Doctor</td>

	                    <td><?php echo $providers[$appointment->provider_id]; ?></td>

	                </tr>

	                <tr>

	                    <td>Service</td>

	                    <td><?php echo $services[$appointment->service_id]; ?></td>

	                </tr>
	                <tr>

	                    <td>Token Number</td>

	                    <td><?php echo $services[$appointment->service_id].' - '.$appointment->token_number; ?></td>

	                </tr>

	                <tr>

	                	<td>Prescription</td>

	                	<td><?php echo nl2br($appointment->notes); ?></td>

	                </tr>
	                <tr>

	                	<td>Other Services</td>
	                	<td style="pointer-events: none;">
	                		<span> 
                                <input type="checkbox" <?php if($appointment->inpatient_chkbx  == 1){echo 'checked';}?>>
                                In-Patient
                            </span>

                            <span style="margin-left:30px;"> 
                                <input type="checkbox" <?php if($appointment->operationward_chkbx  == 1){echo 'checked';}?>>
                                Operation
                            </span>

                            <span style="margin-left:30px;"> 
                                <input type="checkbox" <?php if($appointment->emergency_chkbx  == 1){echo 'checked';}?>>
                                Emergency
                            </span>
	                	</td>

	                </tr>
	                <tr>

	                	<td>Remark</td>

	                	<td><?php echo $appointment->remark; ?></td>

	                </tr>

	                <tr>

	                	<td>Patient Vitals</td>

	                	<td>

							<table class="table table-striped">

				                <?php foreach($vital_index as $i => $vindex): ?>

				            		<tr>

				            			<td>

				            				<?php echo $vindex; ?>

				                        </td>

				            			<td>

											<?php echo $vital_value[$i]; ?>

				            			</td>

				            		</tr>

				                <?php endforeach; ?>

							</table>                		

	                	</td>

	                </tr>

	            </tbody>
	            <thead>

	                <tr>

	                    <th colspan="2" style="background-color: #f5d3c2; color: #000;">

	                        <i class="fa fa-clock-o"></i> 

	                		Lab Services

	                    </th>

	                </tr>

	            </thead>
	            <tbody>
	            	<?php if(!empty($get_appointments_result)){ 
	            		foreach ($get_appointments_result as $key => $value) { ?>

	            				<tr>
	            					<th><?php echo @$products[$value->product_id];?></th>
	            					<td><?php echo $value->result;?></td>
	            				</tr>
	            			
	            	<?php } } ?>
	            </tbody>

	        </table>

	    </div>    

	<?php endforeach; ?>	

<?php else: ?>

	<div class="alert alert-danger">

		Patient history not found.

	</div>

<?php endif; ?>



