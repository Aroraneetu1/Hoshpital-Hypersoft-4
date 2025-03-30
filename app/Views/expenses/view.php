<?php  

    $productname = get_key_value_array('products', 'id', array('name')); 
    $users = get_key_value_array('users', 'id', array('first_name', 'last_name'));
    $supplier = get_key_value_array('supplier', 'id', array('name')); 

    $total_amt = get_expense_total_amt($expense->id);

    /*echo '<pre>';
    print_r($expenses_items);
    echo '</pre>';*/
?>
<style type="text/css">
	strong, th{
		font-weight: 600 !important;
	}
</style>

<div class="row">
	<div class="col-md-12">
		<a href="javascript:;" onclick="fzPrint('sectiondata', 'Report')" class="btn btn-danger pull-right" style="margin-right: 5px;">Print</a>
	</div>
</div>

<div class="section" id="sectiondata">

	<div class="row">
	    <div class="col-md-12">
	        <p><strong>Expense Number:</strong> <?php echo $expense->unique_id;?></p>
	        <p><strong>Expense Amount:</strong> <?php echo number_format($total_amt,2);?></p>
	        <p><strong>Created at:</strong> <?php echo $expense->expense_date;?></p>
	    </div>
	    <div class="col-md-12">
	        <p><strong>Created by:</strong> <?php echo $users[$expense->created_by];?></p>
	        <p><strong>Payment Status:</strong> <span style="font-weight: 600;" class="<?php if($expense->payment_status == 'Pending'){echo 'text-danger';}else{echo 'text-success';}?>"><?php echo $expense->payment_status;?></span></p>
	        <p><strong>Payment by:</strong> <?php if($expense->payment_by !=''){echo $users[$expense->payment_by];}?></p>
	    </div>
	</div>
	<br>
	<table class="table table-bordered" >
	    <thead>
	        <tr>
	            <th>Product</th>
	            <th>Supplier Name</th>
	            <th>Price</th>
	            <th>Qty</th>
	            <th>Sub Total</th>
	        </tr>
	    </thead>
	    <tbody>
	    	<?php 
	    	$totamt = 0;
	    	if(!empty($expenses_items)){ 
	    			foreach ($expenses_items as $k => $v) { 

	    				$totamt += $v->subtotal; ?>
	    				
	    				<tr>
				            <td><?php echo $productname[$v->product_id];?></td>
				            <td><?php echo @$supplier[$expense->supplier_id];?></td>
				            <td><?php echo $v->price;?></td>
				            <td><?php echo $v->qty;?></td>
				            <td><?php echo $v->subtotal;?></td>
				        </tr>

	    	<?php }	} ?>
	    </tbody>
	    <tfoot>
	        <tr>
	            <td><strong>Total</strong></td>
	            <td colspan="3"></td>
	            <td><strong><?php echo number_format($totamt,2);?></strong></td>
	        </tr>
	    </tfoot>
	</table>
</div>