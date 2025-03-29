<?php  

    $productname = get_key_value_array('products', 'id', array('name')); 
    $users = get_key_value_array('users', 'id', array('first_name', 'last_name'));
    //$supplier = get_key_value_array('supplier', 'id', array('name')); 

    $total_amt = (float) $inpatients->room_rate_amt;
	$total_amt += get_inpatients_total_amt($inpatients->id);


    /*echo '<pre>';
    print_r($expenses_items);
    echo '</pre>';*/
?>
<style type="text/css">
	strong, th{
		font-weight: 600 !important;
	}
</style>
<section>
	<?php include('inpatient_div.php'); ?>
	<div class="row">
		<div class="col-md-12">
			<a href="javascript:;" onclick="fzPrint('sectiondata', 'Report')" class="btn btn-danger pull-right" style="margin-right: 5px;">Print</a>
		</div>
	</div>

	<div class="section" id="sectiondata">

		<div class="row">
		    <div class="col-md-12">
		        <p><strong>In-Patient Number:</strong> <?php echo $inpatients->uniq_id;?></p>
		        <p><strong>In-Patient Amount:</strong> <?php echo number_format($total_amt,2);?></p>
		        <p><strong>Created at:</strong> <?php echo $inpatients->check_in;?></p>
		    </div>
		    <div class="col-md-12">
		        <p><strong>Created by:</strong> <?php echo $users[$inpatients->created_by];?></p>
		        <p><strong>Doctor Name:</strong> <?php echo @$users[$inpatients->doctor_id];?></p>
		        <p><strong>Payment Status:</strong> <span style="font-weight: 600;" class="<?php if($inpatients->pay_status == 'Pending'){echo 'text-danger';}else{echo 'text-success';}?>"><?php echo $inpatients->pay_status;?></span></p>
		        <p><strong>Payment Date:</strong> <?php echo $inpatients->check_out;?></p>
		        
		    </div>
		</div>
		<br>
		<table class="table table-bordered" >
		    <thead>
		        <tr>
		            <th>Product</th>
		            <th>Price</th>
		            <th>Qty</th>
		            <th>Sub Total</th>
		            <th>Date</th>
		        </tr>
		    </thead>
		    <tbody>

		    	<tr>
		            <td>Room Rate</td>
		            <td><?php echo number_format((float) $inpatients->room_rate, 2); ?></td>
		            <td><?php echo number_format($inpatients->dayDiff,2);?></td>
		            <td><?php echo number_format((float) $inpatients->room_rate_amt,2);?></td>
		            <td><?php echo $inpatients->check_out;?></td>
		        </tr>
		    	<?php 
		    	$totamt = $inpatients->room_rate_amt;
		    	if(!empty($inpatients_items)){ 
		    			foreach ($inpatients_items as $k => $v) { 

		    				$totamt += $v->subtotal; ?>
		    				
		    				<tr>
					            <td><?php echo $productname[$v->product_id];?></td>
					            <td><?php echo $v->price;?></td>
					            <td><?php echo $v->qty;?></td>
					            <td><?php echo $v->subtotal;?></td>
					            <td><?php echo $v->updated;?></td>
					        </tr>

		    	<?php }	} ?>

		    </tbody>
		    <tfoot>
		        <tr>
		            <td><strong>Total</strong></td>
		            <td colspan="2"></td>
		            <td><strong><?php echo number_format((float) $totamt,2);?></strong></td>
		            <td></td>
		        </tr>
		    </tfoot>
		</table>
	</div>
</section>