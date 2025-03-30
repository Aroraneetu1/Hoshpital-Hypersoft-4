<?php 
$permissions = unserialize(get_session_data('permissions'));
?>
<style type="text/css">
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
    table.dataTable tbody th, table.dataTable tbody td {
        padding: 2px 6px !important;
    }
    .dataTables_filter{
        display: none !important;
    }
</style>
<section id="unseen">

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-lg-9">
            <!-- <form action="<?php echo get_site_url('finance/transactions'); ?>" method="get" > -->
                <input value="<?php echo $from; ?>" type="text" autocomplete="off" name="from" placeholder="From" required style="margin-right: 10px; width: 180px; height: 35px; border: none; background-color: #eee; padding-left: 15px;" >
                <input value="<?php echo $to; ?>" type="text" autocomplete="off"  name="to" placeholder="To" required style="margin-right: 10px; width: 180px; height: 35px; border: none; background-color: #eee; padding-left: 15px;" >
                <!-- <input type="submit" name="submit" value="Search" class="btn btn-success"> -->
            <!-- </form> -->
        </div>
        <div class="col-lg-3">
            <a href="javascript:;" id="downloadCsv" class="btn btn-info pull-right">Download Excel</a>
            <a href="javascript:;" id="customPrintButton" class="btn btn-danger pull-right" style="margin-right: 5px;">Print</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th>Bank Name</th>
                        <!-- <th>Branch Code</th> -->
                        <!-- <th>Bank Phone</th> -->
                        <th>Bank Account</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($banks): ?>
                        <?php foreach($banks as $row):  ?>
                            <tr>
                            	<td><?php echo $row->bank_name; ?></td>
                                <!-- <td><?php echo $row->branch_code; ?></td> -->
                                <!-- <td><?php echo $row->bank_phone; ?></td> -->
                                <td><?php echo $row->bank_account; ?></td>
                                <td>
                                    <?php //if($permissions['patients'] == 1){?> 

                                        <a class="btn btn-primary btn-sm" onclick="btransections(<?php echo $row->id?>)" href="javascript:;">
                                            Transactions
                                        </a>
                                        
                                    <?php //} ?>
                                </td>
                            </tr>
                        <?php endforeach; ?> 
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-8">
            <table class="table table-bordered table-striped table-condensed table-db-js" id="tableprint">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Debit Amount</th>
                        <th>Credit Amount</th>
                        <th>Balance Amount</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody id="appbdata">

                </tbody>
            </table>
        </div>
    </div>
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

        var table = $(".table-db-js").DataTable({
            paging: true,
            info: false,
            sorting: false,
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: '_all' }
            ],
        });

        $('#customPrintButton').on('click', function () {

            fzPrint('tableprint', 'Report')
       
            /*table.page.len(-1).draw();
            //$('#table-db-js th:last-child, #table-db-js td:last-child').hide();

            setTimeout(() => {
                fzPrint('table-db-js', 'Report')

                //$('#table-db-js th:last-child, #table-db-js td:last-child').show();

                table.page.len(10).draw(); 
            }, 500); */
        });


        $('#downloadCsv').on('click', function () {
            var csvData = [];
            
            csvData.push(["Date", "Reference", "Debit Amount", "Credit Amount", "Balance Amount", "Remark"].join(','));

            
            $('#tableprint tbody tr').each(function () {
                var rowData = [];

                
                $(this).find('td').each(function () {
                    var text = $(this).text().trim();
                    
                    text = '"' + text.replace(/"/g, '""') + '"';
                    rowData.push(text);
                });

                csvData.push(rowData.join(','));
            });

            
            var csvString = csvData.join('\n');

            
            var blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            var url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'transaction_report.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });


    })
    
    function btransections(appid) {

        //let dataTable = $(".table-db-js").DataTable();

        var from = $('input[name="from"]').val();
        var to = $('input[name="to"]').val();

        $.ajax({
            type : 'POST',
            url: '<?php echo get_site_url('finance/ajax_btransections'); ?>',
            data: {bn_id: appid,from: from,to: to},
            success: function(result){

                $('#appbdata tr').remove();
                if(result != 0){

                    //dataTable.destroy();

                    $('#appbdata tr').remove();
                    $('#appbdata').html(result);

                    /*$(".table-db-js").DataTable({
                        paging: true,
                        info: false,
                        searching: false,
                        pageLength: 10,
                        sorting: false,
                        columnDefs: [
                            { orderable: false, targets: '_all' }
                        ],
                    });*/
                }
            }
        })

    }
</script>