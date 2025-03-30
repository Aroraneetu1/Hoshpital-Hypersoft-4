<?php 
    $permissions = unserialize(get_session_data('permissions'));
    $settingspp = isset($permissions['settings']) ? $permissions['settings'] : 0;

    $supplier = get_key_value_array('supplier', 'id', array('name')); 

    $users = get_key_value_array('users', 'id', array('first_name', 'last_name'));

    $paymenttypes = get_key_value_array('payment_types', 'id', array('name'));
    $bankbblns = get_key_value_array('banks', 'payment_type', array('current_balance'));

    /*$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));
    $providers = get_key_value_array('users', 'id', array('first_name', 'last_name'));
    $services = get_key_value_array('service_types', 'id', array('name'));*/

    /*echo '<pre>';
    print_r($expensespay);
    //print_r($bankbblns);
    echo '</pre>';*/

?>
<style type="text/css">
    table.dataTable thead th, table.dataTable tfoot th{
        border-bottom: none !important;
        border-top: none !important;
    }
    table.dataTable thead th, table.dataTable thead td {
        padding: 5px 5px !important;
    }
</style>
<section id="unseen">
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-lg-9">
            <form action="<?php echo get_site_url('expenses/expensepayment'); ?>" method="get" >
                <input value="<?php echo $from; ?>" type="text" autocomplete="off" name="from" placeholder="From" required style="margin-right: 10px; width: 180px; height: 35px; border: none; background-color: #eee; padding-left: 15px;" >
                <input value="<?php echo $to; ?>" type="text" autocomplete="off"  name="to" placeholder="To" required style="margin-right: 10px; width: 180px; height: 35px; border: none; background-color: #eee; padding-left: 15px;" >
                <input type="submit" name="submit" value="Search" class="btn btn-success">
            </form>
        </div>
        <div class="col-lg-3">
            <a href="javascript:;" id="downloadCsv" class="btn btn-info pull-right">Download Excel</a>
            <a href="javascript:;" id="customPrintButton" class="btn btn-danger pull-right" style="margin-right: 5px;">Print</a>
        </div>
    </div>

    <table id="table-db-js" class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th style="vertical-align: top;width: 10%;">Expense No.</th>
                <th style="vertical-align: top;">Supplier</th>
                <th style="vertical-align: top;">Payment Type</th>
                <th style="vertical-align: top;">Amount</th>
                <th style="vertical-align: top;">Payment Date</th>
                
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($expensespay)): ?>
                <?php foreach($expensespay as $row):  

                    $get_expense_info = get_expense_info($row->expense_id); ?>

                    <tr>
                        <td><?php echo $row->expense_id; ?></td>
                        <td><?php echo @$supplier[$get_expense_info->supplier_id]; ?></td>
                        <td><?php echo @$paymenttypes[$row->pay_type]; ?></td>
                        <td><?php echo $row->amount; ?></td>
                        <td><?php echo $row->payment_date; ?></td>
                        
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
        <tfoot>
            <tr>
                <th style="text-align:right">Total</th>
                <th colspan="2"></th>
                <th style="text-align:left" id="amount-total"></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</section>



<script type="text/javascript">
$(document).ready(function(){

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

    $("#table-db-js thead th").each(function(){
        var title = $(this).text();
        if(title == "Expense No." || title == "Supplier" || title == "Status"){
        	$(this).append('<input class="fz-col-filter">');
        }
    });
    var table = $("#table-db-js").DataTable({
        paging: true,
        info: false,
        sorting: false,
        pageLength: 8,
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



    $('#customPrintButton').on('click', function () {
       
        table.page.len(-1).draw();
        //$('#table-db-js th:last-child, #table-db-js td:last-child').hide();

        setTimeout(() => {
            fzPrint('table-db-js', 'Report')

            //$('#table-db-js th:last-child, #table-db-js td:last-child').show();

            table.page.len(10).draw(); 
        }, 500); 
    });


    $('#downloadCsv').on('click', function () {
        // Fetch all rows
        var allData = table.rows({ search: 'applied' }).data();

        // Generate CSV
        var csvData = [];
        csvData.push(["Expense No.", "Supplier", "Payment Type", "Amount", "Payment Date"].join(',')); // Header row
        
        allData.each(function (row) {
            // Extract salary and convert to numeric
            var salaryNumeric = row[1].replace(/[^0-9.]/g, ''); // Remove $ and commas

            // Add row to CSV
            csvData.push([
                row[0], 
                row[1], 
                row[2], // Office
                row[3], // Age
                row[4], // Start date
                
                
            ].join(','));
        });

        // Convert array to CSV string
        var csvString = csvData.join('\n');

        // Create a blob and trigger download
        var blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'expenses_payment.csv');
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
	height: 30px;
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

