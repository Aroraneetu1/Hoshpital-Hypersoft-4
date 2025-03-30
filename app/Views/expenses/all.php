<?php 
    $permissions = unserialize(get_session_data('permissions'));
    $settingspp = isset($permissions['settings']) ? $permissions['settings'] : 0;
    $expensepay = isset($permissions['expensepay']) ? $permissions['expensepay'] : 0;

    $supplier = get_key_value_array('supplier', 'id', array('name')); 

    $users = get_key_value_array('users', 'id', array('first_name', 'last_name'));

    $paymenttypes = get_key_value_array('payment_types', 'id', array('name'));
    $bankbblns = get_key_value_array('banks', 'payment_type', array('current_balance'));

    /*$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));
    $providers = get_key_value_array('users', 'id', array('first_name', 'last_name'));
    $services = get_key_value_array('service_types', 'id', array('name'));*/

    /*echo '<pre>';
    print_r($paymenttypes);
    print_r($bankbblns);
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
            <form action="<?php echo get_site_url('expenses/all'); ?>" method="get" >
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
                <th style="vertical-align: top;">Amount</th>
                <th>Supplier</th>
                <th style="vertical-align: top;width: 8%;">Status</th>
                <th style="vertical-align: top;">Payment Date</th>
                <th style="vertical-align: top;">Payment By</th>
                <th style="vertical-align: top;">Expense Date</th>
                <th style="vertical-align: top;width: 25%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($expenses): ?>
                <?php foreach($expenses as $row): 
                    $total_amt = get_expense_total_amt($row->id); 
                    $total_paidyet = get_expense_totpaid_amt($row->id); ?>

                    <tr>
                        <td><?php echo $row->id; ?></td>
                        <td><?php echo number_format($total_amt,2);?></td>
                        <td><?php echo @$supplier[$row->supplier_id]; ?></td>
                        <td style="font-weight: 600;" class="<?php if($row->payment_status == 'Pending'){echo 'text-danger';}else{echo 'text-success';}?>"><?php echo $row->payment_status; ?></td>
                        <td><?php echo $row->payment_date; ?></td>
                        <td><?php echo @$users[$row->payment_by]; ?></td>
                        <td><?php echo $row->expense_date; ?></td>
                        <td>
                            <a class="btn btn-warning btn-sm" href="<?php echo get_site_url('expenses/view/'.$row->id); ?>">
                                View
                            </a>

                            <?php if($row->payment_status == 'Paid'){ 

                                    if(get_session_data('role') == 1){?>

                                        <a class="btn btn-info loader-activate btn-sm" href="<?php echo get_site_url('expenses/edit/'.$row->id); ?>">Edit</a>
                                        
                                        <a class="btn btn-success btn-sm payAmount" data-id="<?php echo $row->id?>" data-paidAmt="<?php echo $total_paidyet?>" href="javascript:;">
                                             <?php if($row->payment_status == 'Pending'){echo 'Pay';}else{echo 'Paid';}?>
                                        </a>
                            <?php   } 

                                }else{ ?>

                                    <a class="btn btn-info loader-activate btn-sm" href="<?php echo get_site_url('expenses/edit/'.$row->id); ?>">
                                        Edit
                                    </a>
                                    
                                    <?php if($expensepay == 1){?>
                                        <a class="btn btn-success btn-sm payAmount" data-id="<?php echo $row->id?>" data-paidAmt="<?php echo $total_paidyet?>" href="javascript:;">
                                            <?php if($row->payment_status == 'Pending'){echo 'Pay';}else{echo 'Paid';}?>
                                        </a>
                                    <?php } ?>

                            <?php } ?>

                            <?php if($settingspp == 1){?>

                                <a class="btn btn-danger btn-sm" onclick="cancelex(<?php echo $row->id?>)" href="javascript:;">
                                    Cancel
                                </a>

                            <?php } ?>

                            
                           
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">
                        No record found.
                    </td>
                </tr>    
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th style="text-align:right">Total</th>
                <th style="text-align:left" id="amount-total"></th>
                <th colspan="6"></th>
            </tr>
        </tfoot>
    </table>
</section>

<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 400px;">
        <div class="modal-content">
            <form action="<?php echo get_site_url('expenses/pay_now'); ?>" method="post">
                <input type="hidden" name="rid" id="expid">
                <input type="hidden" name="totamts" id="totamt">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pay Amount</h5>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <label>Payment Date</label>
                            <input type="text" name="payment_date" id="paymentdate" class="form-control" required>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-12">
                            <label>Payment type</label>
                            <select name="payment_type" id="payType" class="form-control" required onchange="checkexpay()">
                                <option value="">Select one</option>
                                <?php 
                                //unset(8);
                                foreach ($paymenttypes as $key => $value){ 
                                    if($key !=8){?>
                                    <option value="<?php echo $key;?>" data-blnc="<?php echo isset($bankbblns[$key]) ? $bankbblns[$key] : 0;?>"><?php echo $value;?></option>
                                <?php }} ?>
                            </select>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-12">
                            <label>Amount </label>
                            <span class="pull-right"  id="paidAmt" style="font-size: 13px;color: red;"> 0.00</span>
                            <span class="pull-right" style="font-size: 13px;color: red;">Pending Amount: &nbsp;</span> 
                            <input type="text" name="amount" id="payamt" class="form-control" required onkeyup="checkexpay()">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Pay Now</button>
                </div>
            </form>
        </div>
    </div>
</div>


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
        const columnIdx = 1;
        const displayedData = table.rows({ filter: 'applied' }).data();
        let total = 0;

        displayedData.each(function (row) {
            total += parseFloat(row[columnIdx]) || 0;
        });

        $('#amount-total').text(total.toFixed(2));
    }


    $('.payAmount').on('click', function(){
        var exid = $(this).data('id'); 
        var paidAmt = $(this).data('paidamt') || 0; 

        const row = $(this).closest('tr');
        const paymentdate = row.find('td').eq(6).text();

        const totamts = row.find('td').eq(1).text();
        const totamt = totamts.replace(/,/g, '');

        var penamt = parseFloat(totamt) - parseFloat(paidAmt); 

        $('#payModal #expid').val(exid);
        $('#payModal #paymentdate').val(paymentdate);
        $('#payModal #totamt').val(totamt);
        $('#payModal #paidAmt').text(penamt);
        $('#payModal').modal('show');
    })


    

    $("input[name='payment_date']").datepicker({
        dateFormat: 'yy-mm-dd',
        //changeMonth: true,
        //changeYear: true,
    })


    $('#customPrintButton').on('click', function () {
       
        table.page.len(-1).draw();
        $('#table-db-js th:last-child, #table-db-js td:last-child').hide();

        setTimeout(() => {
            fzPrint('table-db-js', 'Report')

            $('#table-db-js th:last-child, #table-db-js td:last-child').show();

            table.page.len(10).draw(); 
        }, 500); 
    });


    $('#downloadCsv').on('click', function () {
        // Fetch all rows
        var allData = table.rows({ search: 'applied' }).data();

        // Generate CSV
        var csvData = [];
        csvData.push(["Expense No.", "Amount", "Supplier", "Status", "Payment Date", "Payment By", "Expense Date"].join(',')); // Header row
        
        allData.each(function (row) {
            // Extract salary and convert to numeric
            var salaryNumeric = row[1].replace(/[^0-9.]/g, ''); // Remove $ and commas

            // Add row to CSV
            csvData.push([
                row[0], // Name
                salaryNumeric, // Position
                row[2], // Office
                row[3], // Age
                row[4], // Start date
                row[5], // Start date
                row[6] // Start date
                
            ].join(','));
        });

        // Convert array to CSV string
        var csvString = csvData.join('\n');

        // Create a blob and trigger download
        var blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'expenses_report.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });


    
});


function checkexpay(){

    let bbalance = $('#payModal select[name="payment_type"] option:selected').data('blnc'); 

    var paidamt = $('#payModal #paidAmt').text() || 0;
    var inputval = $('#payModal #payamt').val() || 0;

    if (Number(inputval) > Number(bbalance)) {

        Swal.fire({
          title: "Error!",
          text: "Insufficient funds in this bank account",
          icon: "error"
        });

        $('#payModal #payamt').val(bbalance);

    }else{

        if (Number(inputval) > Number(paidamt)) {
            $('#payModal #payamt').val(paidamt);
        }else{
            $('#payModal #payamt').val(inputval);
        }
    }    
}

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

function cancelex(appid) {

    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this expense!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                type : 'POST',
                url: '<?php echo get_site_url('expenses/cancel'); ?>',
                data: {id: appid},
                success: function(result){

                    Swal.fire({
                      title: "Deleted!",
                      text: "Expense deleted.",
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


<div class="hidden" id="prescptnPrint">
    
</div>