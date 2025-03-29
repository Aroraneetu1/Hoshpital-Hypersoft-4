<?php  

    $productname = get_key_value_array('products', 'id', array('name')); 
    $users = get_key_value_array('users', 'id', array('first_name', 'last_name'));
    //$supplier = get_key_value_array('supplier', 'id', array('name')); 

    $total_amt = get_operation_total_amt($operation->id);

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
	<?php include('operation_div.php'); ?>
	<div class="row">
		<div class="col-md-12">
			<a href="javascript:;" onclick="fzPrint('sectiondata', 'Report')" class="btn btn-danger pull-right" style="margin-right: 5px;">Print</a>
		</div>
	</div>

	<div class="section" id="sectiondata">

		<div class="row">
		    <div class="col-md-12">
		        <p><strong>Operation Number:</strong> <?php echo $operation->unique_id;?></p>
		        <p><strong>Operation Amount:</strong> <?php echo number_format($total_amt,2);?></p>
		        <p><strong>Created at:</strong> <?php echo $operation->operation_date;?></p>
		    </div>
		    <div class="col-md-12">
		        <p><strong>Created by:</strong> <?php echo $users[$operation->created_by];?></p>
		        <p><strong>Doctor Name:</strong> <?php echo @$users[$operation->doctor_id];?></p>
		        <p><strong>Assistant Doctor Name:</strong> <?php echo @$users[$operation->ass_doctor_id];?></p>
		        <p><strong>Payment Status:</strong> <span style="font-weight: 600;" class="<?php if($operation->pay_status == 'Pending'){echo 'text-danger';}else{echo 'text-success';}?>"><?php echo $operation->pay_status;?></span></p>
		        
		        
		    </div>
		</div>
		
		<table class="table table-bordered" style="margin-top: 10px;">
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

		    	<?php 
		    	$totamt = 0;
		    	if(!empty($operation_items)){ 
		    			foreach ($operation_items as $k => $v) { 

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
		            <td><strong><?php echo number_format($totamt,2);?></strong></td>
		            <td></td>
		        </tr>
		    </tfoot>
		</table>
	</div>
</section>