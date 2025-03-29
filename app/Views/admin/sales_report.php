<?php 
    //error_reporting(0);
    $consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));
    $providers = get_key_value_array('users', 'id', array('first_name', 'last_name')); 
    $services = get_key_value_array('service_types', 'id', array('name')); 
    $serpric = get_key_value_array('service_types', 'id', array('amount'));
    $appointments = get_key_value_array('appointments', 'id', array('consumer_id'));
    $products = get_key_value_array('products', 'id', array('name'));

    $paymentTypes = get_key_value_array('payment_types', 'id', array('name'));

    /*echo '<pre>';
    //print_r($serpric);
    print_r($paymentTypes);
    print_r($payments);
    echo '</pre>';*/

?>
<style type="text/css">
    
    .thcls{
        padding: 10px 18px 6px 8px !important;
    }
</style>
<section id="unseen">
	<div class="row" style="margin-bottom: 15px;">
		<div class="col-lg-9">
			<form action="<?php echo get_site_url('admin/sales_report'); ?>" method="get" >
				<input value="<?php echo $from; ?>" type="text" name="from" placeholder="From" required style="margin-right: 10px; width: 180px; height: 35px; border: none; background-color: #eee; padding-left: 15px;" >
				<input value="<?php echo $to; ?>" type="text" name="to" placeholder="To" required style="margin-right: 10px; width: 180px; height: 35px; border: none; background-color: #eee; padding-left: 15px;" >
				<input type="submit" name="submit" value="Search" class="btn btn-success">
			</form>
		</div>
        <div class="col-lg-3">
            <a href="javascript:;" id="downloadCsv" class="btn btn-info pull-right">Download Excel</a>
            <a href="javascript:;" id="customPrintButton" class="btn btn-danger pull-right" style="margin-right: 5px;">Print</a>
        </div>
	</div>
    <table id="salesPrint" class="table table-bordered table-striped table-condensed table-db-js">
        <thead>
            <tr>
            	<th>Receipt #</th>
                <th>Service Name</th>
                <th>Patient Name</th>
                <th>Payment Status</th>
                <th>Payment By</th>
                <th>Payment Date</th>
                <th>Amount</th>
               
            </tr>
        </thead>
        <tbody>
            <?php 
            $tot_amount = 0;
            /*if($sales1): ?>
                <?php foreach($sales1 as $row): 

                    $amt = isset($serpric[$row->service_id]) ? $serpric[$row->service_id] : 0;
                    $tot_amount += $amt; ?>
                    <tr>	
                    	<td><?php echo 'REC-'.$row->id; ?></td>
                        <td><?php echo 'Consalt fees - '. $services[$row->service_id]; ?></td>
                        <td><?php echo @$consumers[$row->consumer_id]; ?></td>
                        <td>Paid</td>
                        <td><?php echo date('Y-m-d', strtotime($row->start_time)); ?></td>
                        <td><?php echo number_format($amt,2);?></td>
                        
                    </tr>
                <?php endforeach; ?>
            <?php endif;*/ ?>

            <?php if($sales3): ?>
                <?php foreach($sales3 as $row): $tot_amount += $row->amount; ?>
                    <tr>    
                        <td><?php echo 'REC-'.$row->receipt_id; ?></td>
                        <td><?php echo $row->remark;?></td>
                        <td><?php echo @$consumers[$row->patient_id]; ?></td>
                        <td>Paid</td>
                        <td><?php echo @$providers[$row->user_id]; ?></td>
                        <td><?php echo $row->datetime; ?></td>
                        <td><?php echo number_format($row->amount,2);?></td>
                        
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>


            <?php if($sales2): ?>
                <?php foreach($sales2 as $row): $tot_amount += $row->subtot; ?>
                    <tr>    
                        <td><?php echo 'REC-'.$row->receipt_no; ?></td>
                        <td><?php if($row->product_id == 0){echo 'Consultations Fee';}else{echo 'Lab Test';}?></td>
                        <td><?php echo @$consumers[$appointments[$row->appointment_id]]; ?></td>
                        <td>Paid</td>
                        <td><?php echo @$providers[$row->payment_by]; ?></td>
                        <td><?php echo $row->full_paid_at; ?></td>
                        <td><?php echo number_format($row->subtot,2);?></td>
                        
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if($inppay): ?>
                <?php foreach($inppay as $row): $tot_amount += $row->payment_type_amount; ?>
                    <tr>    
                        <td><?php echo 'REC-'.$row->receipt_id; ?></td>
                        <td><?php if($row->pay_from == 'INP'){echo 'In-Patient Fee';}else{echo 'Operation Fee';}?></td>
                        <td><?php echo @$consumers[$appointments[$row->appointment_id]]; ?></td>
                        <td>Paid</td>
                        <td><?php echo @$providers[$row->payment_by]; ?></td>
                        <td><?php echo $row->payment_datetime; ?></td>
                        <td><?php echo number_format($row->payment_type_amount,2);?></td>
                        
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <?php if(!empty($paymentTypes)){ 
                    foreach ($paymentTypes as $kk => $vv) { 

                        $amt = isset($payments[$kk]) ? number_format($payments[$kk],2) : '0.00';?>

                        <th class="thcls"><?php echo $vv.': '.$amt; ?> </th>
                    
                <?php } }else{ ?>
                    <th class="thcls" colspan="5"></th>
                <?php } ?>
                <th class="thcls" id="amount-total"><?php echo number_format($tot_amount,2)?></th>
            </tr>
        </tfoot>
    </table>
</section>

<script type="text/javascript">
$(document).ready(function(){
    $(".table-db-js thead th").each(function(){
        var title = $(this).text();
        if(title != "Actions"){
        	$(this).append('<input class="fz-col-filter">');
        }
    });
    var table = $(".table-db-js").DataTable({
        paging: true,
        info: false,
        sorting: false,
        order: [[5, "desc"]],
        pageLength: 10,
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

    table.on('draw', function () {
        updateTotal();
    });

    updateTotal();

    function updateTotal() {
        const columnIdx = 6;
        const displayedData = table.rows({ filter: 'applied' }).data();
        let total = 0;

        displayedData.each(function (row) {
            total += parseFloat(row[columnIdx]) || 0;
        });

        $('#amount-total').text(total.toFixed(2));
    }

  	var dateFormat = "d MM yy";
  	var from = $("input[name='from']").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        changeYear: true,
    }).on("change", function(){
          to.datepicker("option", "minDate", getDate(this));
    });
    var to = $("input[name='to']").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        changeYear: true,
  	}).on("change", function(){
        from.datepicker("option", "maxDate", getDate(this));
    });
 
    function getDate(element){
      	var date;
      	try{
        	date = $.datepicker.parseDate(dateFormat, element.value);
      	}catch(error){
        	date = null;
      	}
      	return date;
    }

    $('#customPrintButton').on('click', function () {
        //var table = $('#example').DataTable();
        
        // Switch to all rows
        table.page.len(-1).draw();

        // Trigger print
        setTimeout(() => {
           fzPrint('salesPrint', 'Report')

            // Restore original page length after print
            table.page.len(10).draw(); // Set back to default page size
        }, 500); // Add slight delay to ensure data renders
    });


    $('#downloadCsv').on('click', function () {
        // Fetch all rows
        var allData = table.rows({ search: 'applied' }).data();

        // Generate CSV
        var csvData = [];
        csvData.push(["Receipt #", "Service Name", "Patient Name", "Payment Status", "Payment By", "Payment Date", "Amount"].join(',')); // Header row
        
        allData.each(function (row) {
            // Extract salary and convert to numeric
            var salaryNumeric = row[6].replace(/[^0-9.]/g, ''); // Remove $ and commas

            // Add row to CSV
            csvData.push([
                row[0], // Name
                row[1], // Position
                row[2], // Office
                row[3], // Age
                row[4], // Start date
                row[5], // Start date
                salaryNumeric // Numeric salary
            ].join(','));
        });

        // Convert array to CSV string
        var csvString = csvData.join('\n');

        // Create a blob and trigger download
        var blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'sales_report.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });



});
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