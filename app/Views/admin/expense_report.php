<?php 
    //error_reporting(0);
    //$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));
    $providers = get_key_value_array('users', 'id', array('first_name', 'last_name')); 
    //$services = get_key_value_array('service_types', 'id', array('name')); 
    //$serpric = get_key_value_array('service_types', 'id', array('amount'));
    //$appointments = get_key_value_array('appointments', 'id', array('consumer_id'));

    $products = get_key_value_array('products', 'id', array('name'));
    $supplier = get_key_value_array('supplier', 'id', array('name'));

    /*echo '<pre>';
    print_r($productsItems);
    //print_r($sales2);
    echo '</pre>';*/

?>

<section id="unseen">
	<div class="row" style="margin-bottom: 15px;">
		<div class="col-lg-9">
			<form action="<?php echo get_site_url('admin/expense_report'); ?>" method="get" >
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
            	<th>Product Name</th>
                <th>Expense No.</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Created By</th>
                <th>Supplier</th>
               
            </tr>
        </thead>
        <tbody>
            <?php 
            if($productsItems): ?>
                <?php foreach($productsItems as $row): ?>
                    <tr>	
                    	<td><?php echo @$products[$row->product_id]; ?></td>
                        <td><?php echo $row->unique_id; ?></td>
                        <td><?php echo $row->qty; ?></td>
                        <td><?php echo $row->subtotal; ?></td>
                        <td><?php echo $row->expense_date; ?></td>
                        <td><?php echo @$providers[$row->created_by]; ?></td>
                        <td><?php echo @$supplier[$row->supplier_id]; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th style="text-align:right">Total</th>
                <th colspan="2"></th>
                <th style="text-align:left" id="amount-total"></th>
                <th></th>
                <th></th>
                <th></th>
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
        const columnIdx = 3;
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
        csvData.push(["Product Name", "Expense No.", "Quantity", "Amount", "Date", "Created By", "Supplier"].join(',')); // Header row
        
        allData.each(function (row) {
            // Extract salary and convert to numeric
            var salaryNumeric = row[3].replace(/[^0-9.]/g, ''); // Remove $ and commas

            // Add row to CSV
            csvData.push([
                row[0], // Name
                row[1], // Position
                row[2], // Office
                salaryNumeric, // Numeric salary
                row[4], // Start date
                row[5], // Start date
                row[6], // Start date
            ].join(','));
        });

        // Convert array to CSV string
        var csvString = csvData.join('\n');

        // Create a blob and trigger download
        var blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'expense_report.csv');
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