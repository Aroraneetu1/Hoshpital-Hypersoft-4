<?php 

    $consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));
    $paymenttypes = get_key_value_array('payment_types', 'id', array('name'));

    /*echo '<pre>';
    print_r($debitpay);
    echo '</pre>';*/
?>
<style type="text/css">
    
    b{
        font-weight: 600;
    }
</style>
<section id="unseen">

<h2><b>Patient Name: <?php echo $consumers[service('uri')->getSegment(3)]; ?></b></h2>
    <table class="table table-bordered table-striped table-condensed table-db-js">
        <thead>
            <tr>
                <th>Receipt No.</th>
                <th>Service Name </th>
                <th>Date / Time</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($debitpay): ?>
                <?php foreach($debitpay as $row): ?>
                    <tr>    
                        <td><?php echo 'REC-'.$row['receipt_id']; ?></td>
                        <td><?php echo $row['servicename']; ?></td>
                        <td><?php echo $row['payment_datetime']; ?></td>
                        <td><?php echo number_format($row['payment_type_amount'],2);?></td>
                        <td>
                            <a class="btn btn-success btn-sm payAmount" data-pamt="<?php echo $row['payment_type_amount']?>" data-rid="<?php echo $row['receipt_id']?>" href="javascript:;">Pay</a>
                        </td>
                        
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            
        </tbody>
    </table>
</section>

<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 400px;">
        <div class="modal-content">
            <form action="<?php echo get_site_url('consumers/debitpay'); ?>" method="post">
                <input type="hidden" name="rid" id="recepid">
                <input type="text" name="patient_id" value="<?php echo $consumers[service('uri')->getSegment(3)]; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Debit Payment</h5>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <label>Pay amount</label>
                            <input type="text" name="payment_amt" data-amt="0" id="payamt" class="form-control" onkeyup="paymentInput(this)" required>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-12">
                            <label>Payment type</label>
                            <select name="payment_type" class="form-control" required>
                                <option value="">Select one</option>
                                <?php 
                                //unset(8);
                                foreach ($paymenttypes as $key => $value){ 
                                    if($key !=8){?>
                                    <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                <?php }} ?>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary" >Pay Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".table-db-js").DataTable({
            paging: false,
            info: false,
        });

        $(".dataTables_wrapper .dataTables_filter input").css("border", "none");
        $(".dataTables_wrapper .dataTables_filter input").css("background-color", "#eee");
        $(".dataTables_wrapper .dataTables_filter input").css("height", "35px");
        $(".dataTables_wrapper .dataTables_filter input").css("margin-bottom", "10px");


        $('.payAmount').on('click', function(){

            var amt = $(this).data('pamt'); 
            var rid = $(this).data('rid'); 

            $('#payModal #recepid').val(rid);
            $('#payModal #payamt').val(amt);

            $("#payModal #payamt").attr("data-amt", amt);

            $('#payModal').modal('show');
        })
    });

    function paymentInput(that){

        var amount = $("#payModal #payamt").attr("data-amt") || 0;
        var inputval = that.value || 0;

        //alert(inputval);
        //alert(amount);

        if (Number(inputval) > Number(amount)) {
            $('#payModal #payamt').val(amount);
        }else{
            $('#payModal #payamt').val(inputval);
        }
    }
</script>