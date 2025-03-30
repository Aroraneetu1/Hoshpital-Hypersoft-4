<?php 
$permissions = unserialize(get_session_data('permissions'));

/*$bankss = get_key_value_array('banks', 'payment_type', array('id'));

echo '<pre>';
print_r($bankss);
echo '<pre>';*/
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
</style>
<section id="unseen">
    <table class="table table-bordered table-striped table-condensed table-db-js">
        <thead>
            <tr>
                <th>Bank Name</th>
                <th>Branch Code</th>
                <th>Bank Phone</th>
                <th>Bank Account</th>
                <th>Opening Balance</th>
                <th>Curr Balance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($banks): ?>
                <?php foreach($banks as $row):  ?>
                    <tr>
                    	<td><?php echo $row->bank_name; ?></td>
                        <td><?php echo $row->branch_code; ?></td>
                        <td><?php echo $row->bank_phone; ?></td>
                        <td><?php echo $row->bank_account; ?></td>
                        <td><?php echo number_format($row->opening_balance,2);?></td>
                        <td><?php echo number_format($row->current_balance,2);?></td>
                        <td>
                            <?php //if($permissions['patients'] == 1){?> 

                                <a class="btn btn-info btn-sm loader-activate" href="<?php echo get_site_url('finance/edit/'.$row->id); ?>">
                                    Edit
                                </a>
                                <a class="btn btn-danger btn-sm bnktransfer" data-id="<?php echo $row->id?>" href="javascript:;">
                                    Bank Transfer
                                </a>
                                
                            <?php //} ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
            <?php else: ?>
                <tr>
                    <td colspan="7">
                        No record found.
                    </td>
                </tr>    
            <?php endif; ?>
        </tbody>
    </table>
</section>

<div class="modal fade" id="btpayModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 400px;">
        <div class="modal-content">
            <form action="<?php echo get_site_url('finance/btransfr_now'); ?>" method="post">
                <input type="hidden" name="frm_bnid" id="bnkid">
                <input type="hidden" name="frm_cblnc" id="totcblnc">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Bank Transfer</h5>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <label>Banks</label>
                            <select name="trn_bankid" id="trnBankid" class="form-control" required>
                                <option value="">Select bank</option>
                                <?php if($banks): ?>
                                    <?php foreach($banks as $row):  ?>
                                        <option value="<?php echo $row->id; ?>"><?php echo $row->bank_name; ?></option>
                                    <?php endforeach; ?>
                                 <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-12">
                            <label>Amount</label>
                            <input type="text" name="tamount" id="payamount" class="form-control" onkeyup="checkcblnc(this)" required>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".table-db-js").DataTable({
            paging: true,
            info: false,
            pageLength: 10,
            sorting: false,
            columnDefs: [
                { orderable: false, targets: '_all' }
            ],
        });

        $(".dataTables_wrapper .dataTables_filter input").css("border", "none");
        $(".dataTables_wrapper .dataTables_filter input").css("background-color", "#eee");
        $(".dataTables_wrapper .dataTables_filter input").css("height", "35px");
        $(".dataTables_wrapper .dataTables_filter input").css("margin-bottom", "10px");


        $('.bnktransfer').on('click', function(){
            var bnkid = $(this).data('id'); 

            const row = $(this).closest('tr');
            const ttotcblnc = row.find('td').eq(5).text();
            const totcblnc = ttotcblnc.replace(/,/g, '');


            $('#btpayModal #bnkid').val(bnkid);
            $('#btpayModal #totcblnc').val(totcblnc);
            $('#btpayModal select#trnBankid option[value="'+bnkid+'"]').attr('disabled',true);
            $('#btpayModal').modal('show');
        })

        $('#btpayModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset(); 
            $('#btpayModal select#trnBankid option').attr('disabled',false);
        });

        
    });

    function checkcblnc(that){

        var inpblnc = that.value;
        var cblnc = $('#btpayModal #totcblnc').val();

        if(parseFloat(inpblnc) > parseFloat(cblnc)){
            $('#btpayModal #payamount').val(cblnc);
        }
    }
</script>