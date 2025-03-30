<?php 

$countries = get_country_array();
$providers = get_key_value_array('users', 'id', array('first_name', 'last_name'));
$services = get_key_value_array('service_types', 'id', array('name'));


$vital_index = array(
    'Height (cm)',
    'Weight (kg)',
    'Temperature (C)',
    'Pulse (/min)',
    'Respiratory Rate (/min)',
    'Blood Pressure (low/high)',
    'Blood Oxygen Saturation (%)',
);
$vital_value = array('Not Calculated', 'Not Calculated', 'Not Calculated', 'Not Calculated', 'Not Calculated', 'Not Calculated', 'Not Calculated');
if($vitals){
	$vital_index = json_decode($vitals->vital_index, TRUE);
	$vital_value = json_decode($vitals->vital_value, TRUE);
}

?>

<ul class="nav nav-tabs">
  	<li class="active">
  		<a data-toggle="tab" href="#panel1">
  			<i class="fa fa-user"></i> Patient Details
		</a>
	</li>
  	<li>
  		<a data-toggle="tab" href="#panel2">
  			<i class="fa fa-calendar"></i> Appointments / Visits
		</a>
	</li>
  	<li>
  		<a data-toggle="tab" href="#panel3">
  			<i class="fa fa-search"></i> Patient Vitals
		</a>
	</li>
</ul>

<div class="tab-content" style="padding: 20px; border: 1px solid #ddd;">
  	<div id="panel1" class="tab-pane fade in active">
        <table class="table table-bordered table-striped table-condensed">
        	<tr>
        		<td width="320">Reference ID</td>
        		<td>
					<?php echo get_formated_id('P', $consumer->id); ?>
        		</td>
        	</tr>
        	<tr>
        		<td>Patient Name</td>
        		<td>
					<?php echo $consumer->first_name.' '.$consumer->middle_name.' '.$consumer->last_name; ?>
        		</td>
        	</tr>
        	<tr>
        		<td>Gender</td>
        		<td>
        			<?php 
        				if($consumer->gender == "M"){ 
        					echo 'Male'; 
        				}else{
        					echo 'Female';
        				} 
					?>
        		</td>
        	</tr>
        	<tr>
        		<td>DOB</td>
        		<td>
        			<?php echo $consumer->dob; ?>
        		</td>
        	</tr>
			<tr>
				<td>Address</td>
				<td>
					<?php echo $consumer->address; ?>
				</td>	
			</tr>
			<tr>
				<td>City/Village</td>
				<td>
					<?php echo $consumer->city; ?>
				</td>	
			</tr>
			<tr>
				<td>State/Province</td>
				<td>
					<?php echo $consumer->state; ?>
				</td>	
			</tr>
			<tr>
				<td>Country</td>
				<td>
			        <?php 
			        	foreach($countries as $key => $value){
			        		if($consumer->country == $key){
			        			echo $value;
			        		}	
			        	}
			        ?>
				</td>
			</tr>
			<tr>
				<td>Postal Code</td>
				<td>
					<?php echo $consumer->postal_code; ?>
				</td>	
			</tr>
			<tr>
				<td>Phone Number</td>
				<td>
					<?php echo $consumer->phone_number; ?>
				</td>	
			</tr>
			<tr>
				<td>Alternate Phone Number</td>
				<td>
					<?php echo $consumer->alternate_phone_number; ?>
				</td>	
			</tr>
        </table>
  	</div>
  	<div id="panel2" class="tab-pane fade">
	    <table class="table table-bordered table-striped table-condensed">
	        <thead>
	            <tr>
	                <th>Doctor</th>
	                <th>Service</th>
	                <th>Appointment Time</th>
	                <th>Status</th>
	                <th>Actions</th>
	            </tr>
	        </thead>
	        <tbody>
	            <?php if($appointments): ?>
	                <?php foreach($appointments as $appointment): ?>
	                    <tr>
	                        <td><?php echo @$providers[$appointment->provider_id]; ?></td>
	                        <td><?php echo @$services[$appointment->service_id]; ?></td>
	                        <td>
	                            <?php //echo date('Y/m/d h:i A', $appointment->start_time);
	                            	echo $appointment->start_time; ?> 
	                        </td>
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
	                        <td>
	                            <?php if($appointment->status == 0): ?>
	                                -
	                            <?php elseif($appointment->status == 1): ?>
	                                <a class="btn btn-info loader-activate" href="<?php echo get_site_url('appointments/visit/'.$appointment->id.'/start'); ?>">
	                                    Start Visit
	                                </a>
	                                <a class="btn btn-danger loader-activate" href="<?php echo get_site_url('appointments/cancel_visit/'.$appointment->id); ?>">
	                                    Cancel
	                                </a>
	                            <?php elseif($appointment->status == 2): ?>
									<a class="btn btn-primary loader-activate" href="<?php echo get_site_url('appointments/visit/'.$appointment->id.'/start'); ?>">
	                                    Start Visit
	                                </a>
	                                <a class="btn btn-danger loader-activate" href="<?php echo get_site_url('appointments/cancel_visit/'.$appointment->id); ?>">
	                                    Cancel
	                                </a>
	                            <?php else: ?>    
	                                <a class="btn btn-success loader-activate" href="<?php echo get_site_url('appointments/view_visit/'.$appointment->id); ?>">
	                                    View
	                                </a>
	                            <?php endif; ?>    
	                        </td>
	                    </tr>
	                <?php endforeach; ?>
	            <?php endif; ?>
	        </tbody>
	    </table>
  	</div>
  	<div id="panel3" class="tab-pane fade">
		<table class="table table-bordered table-striped table-condensed">
            <thead>
                <tr>
                	<th colspan="2">
                		<?php 
                			if($vitals){
                				echo 'Last Vitals: '. date('Y-m-d h:i A', $vitals->visited_start_time);

            				}else{
                				echo 'Last Vitals: Not Calculated Yet.';
        					} 
    					?>
                	</th>
                </tr>
            </thead>
            <tbody>
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
            </tbody>
        </table> 	
    </div>
</div>