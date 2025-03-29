<?php 
error_reporting(0);
$permissions = unserialize(get_session_data('permissions'));
$settingspp = isset($permissions['settings']) ? $permissions['settings'] : 0;

$get_clinic_info = get_clinic_info();

$products = get_key_value_array('products', 'id', array('name'));
$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));
//$providers = get_key_value_array('users', 'id', array('first_name', 'last_name'));
//$providers = get_key_value_array('users', 'id', array('first_name', 'last_name'));

/*echo '<pre>';
print_r($chk_payment_status);
echo '</pre>';*/

$uri = service('uri');
$segment = $uri->getSegment(3);


?>
<style>
    @media print {
        .hide-on-print {
            display: none;
        }

        .receipt {
            display: block;
            margin-top: 0;
        }

        tr{
            padding: 5px;
        }
    }

    strong, th{
        font-weight: 600;
    }
</style>
<section class="hide-on-print">
    
    <?php include('inpatient_div.php'); ?>
    <div class="form">
        <form action="<?php echo get_site_url("appointments/pay_inpatient");?>" method="post">

        <input type="hidden" name="inpatient_id" value="<?php echo service('uri')->getSegment(3); ?>">
            <input type="hidden" name="uniq_id" value="<?php echo $inpatients->uniq_id;?>">
            <input type="hidden" name="appion_id" value="<?php echo $inpatients->appointment_id;?>">
            <input type="hidden" name="room_id" value="<?php echo $inpatients->room_no;?>">

            <div class="row">
                <div class="col-md-6">
                    <p><strong>In-Patienta Id:</strong> <?php echo $inpatients->uniq_id;?></p>
                    <p><strong>Patient Name:</strong> <?php echo @$consumers[$inpatients->patient_id];?></p>
                </div>
                <div class="col-md-6">
                    
                    <a type="button" id="btndr" class="btn btn-info btn-sm pull-right hidden" href="<?php echo get_site_url("appointments/discharge/".service('uri')->getSegment(3));?>"> Discharge Report</a>
                    <a type="button" class="btn btn-danger btn-sm pull-right" onclick="fzPrint('sectiondata', 'Report')" style="margin-right: 6px;"> Receipt</a>
                    <button value="save" name="submit" type="submit" class="btn btn-success btn-sm pull-right" style="margin-right: 6px;"> <?php if($inpatients->pay_status != 'Pending'){echo 'All Paid';}else{echo 'Pay';}?> </button>
                </div>
            </div>

            <div class="row"  style="margin-top: 10px;">
                <div class="col-md-12" >
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <!-- <th style="width: 2%;"><i class="fa fa-trash text-danger fa-lg"></i></th> -->
                                <th style="width: 2%;">#</th>
                                <th>Service Name</th>
                                <th style="width: 12%;">Price</th>
                                <th style="width: 12%;">Discount</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <tr>
                                <td>1.</td>
                                <td>Room Rate</td>
                                <td>
                                    <input type="text" id="rrprice" name="pricerr" class="form-control tprice" placeholder="Price" value="<?php echo round(floatval($inpatients->room_rate_amt), 2); ?>"                                    readonly>
                                </td>
                                <td></td>
                            </tr>
                                    
                            <?php

                            $tot_need_to_pay = $inpatients->room_rate_amt;
                            $tot_price = $inpatients->room_rate_amt;
                            $tot_discunt = 0;
                            
                            $icnt = 1;
                            if(!empty($inpatients_items)){ 
                                    foreach ($inpatients_items as $key => $value) { 
                                        $icnt++;
                                        $tot_price += $value->subtotal;
                                        $tot_discunt += $value->discount; 

                                        $tot_need_to_pay += $value->subtotal; ?>

                                        <tr>
                                            <!-- <td>
                                                <?php /*if($settingspp == 1){ ?>
                                                    <a href="<?php echo get_site_url("appointments/removelb/".$value->id."/".$value->id)?>"><i class="fa fa-trash text-danger fa-lg"></i></a>
                                                <?php } */?>
                                            </td> -->
                                            <td><?php echo $icnt;?>.</td>
                                            <td><?php if(isset($products[$value->product_id])){ echo $products[$value->product_id]; }else{echo "";}?></td>
                                            <td>
                                                <input type="text" id="price<?php echo $value->id?>" name="price[<?php echo $value->product_id?>]" class="form-control tprice" placeholder="Price" value="<?php echo round($value->subtotal,2)?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="discount[<?php echo $value->product_id?>]" placeholder="Discount" value="<?php echo round($value->discount,2)?>" data-orgprice="<?php echo $value->subtotal?>" maxlength="<?php echo $value->subtotal?>" onchange="applydiscount(this, <?php echo $value->id?>)">
                                            </td>
                                        </tr>
                                    
                            <?php } } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td>Total Payment Due: </td>
                                <td id="duepay" style="text-align: left;padding-left: 12px !important;"><?php echo round(floatval($tot_price), 2); ?>                                </td>
                                <td style="text-align: left;padding-left: 12px !important;"><?php echo round($tot_discunt,2);?></td>
                               
                            </tr>

                                <?php 
                                $total_paid_yet = 0;
                                if(!empty($paymentss)){
                                    foreach ($paymentss as $kk => $vv) { 

                                        $total_paid_yet += $vv->payment_type_amount; ?>
                                        <tr>
                                            <td></td>
                                            <td>
                                                Payment Type: <?php echo $vv->receipt_id;?>
                                                <select class="form-control" name="payment_type[]" style="width: 25%;display: inline;float: right;">
                                                    <option value="">Select One</option>
                                                    <?php foreach ($paymentTypes as $k => $v) { ?>
                                                        <option value="<?php echo $v->id;?>" <?php if($vv->payment_type == $v->id){echo 'selected';}?>><?php echo $v->name;?></option>
                                                    <?php } ?>
                                                </select></td>
                                            <td style="text-align: right;">
                                                <input type="text" class="form-control payamt" readonly name="payment_type_amount[]" value="<?php echo $vv->payment_type_amount?>">
                                            </td>
                                            <td style="text-align: right;"></td>
                                            
                                        </tr>
                                <?php } } ?>

                                <?php  

                                    $msg = '';
                                    $msgcls = '';
                                    $amt_left = floatval($tot_need_to_pay) - floatval($total_paid_yet);
                                    if($amt_left == 0){
                                        $msg = '<span class="text-success">All Paid</span>';
                                        $msgcls = 'hidden';
                                    }
                                ?>


                            <tr>
                                <td></td>
                                <td>
                                    Payment Type: <?php echo $msg;?>
                                    <select class="form-control <?php echo $msgcls?>" name="payment_type[]" style="width: 25%;display: inline;float: right;" required>
                                        <option value="">Select One</option>
                                        <?php foreach ($paymentTypes as $k => $v) { ?>
                                            <option value="<?php echo $v->id;?>"><?php echo $v->name;?></option>
                                        <?php } ?>
                                    </select></td>
                                <td style="text-align: right;">
                                    <input type="text" class="form-control <?php echo $msgcls?>" onkeyup="paymentInput(this)" id="payInput" name="payment_type_amount[]" value="" required>
                                </td>
                                <td style="text-align: right;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>


        </form>
    </div>

</section>

<div class="hidden">
    <div class="receipt" id="sectiondata" style="width: 80mm;text-align: center;">
        <div><b style="font-size: 16px;"><center><?php echo $get_clinic_info->name;?><br><?php echo $get_clinic_info->contact;?></center></b></div>
        
        <div style="border-top: 1px dashed #000;margin-top: 10px;margin-bottom: 10px;"></div>
        
        <div style="font-size: 12px;text-align: left;">
            <p style="margin-top: 10px;margin-bottom: 10px;">Receipt Number: <?php echo $inpatients->uniq_id;?></p>
            <p style="margin-top: 10px;margin-bottom: 10px;">Patient: <?php echo @$consumers[$inpatients->patient_id];?></p>
            <p style="margin-top: 10px;margin-bottom: 10px;">Date: <?php echo date('d/m/Y H:i')?></p>
        </div>
        
        <div style="border-top: 1px dashed #000;margin-top: 10px;margin-bottom: 10px;"></div>

        <table style="width: 100%;font-size: 12px;border-collapse: collapse;border-spacing: 0 10px;">
            <tr>
                <td><strong>Item</strong></td>
                <td><strong>Qty</strong></td>
                <td><strong>Price</strong></td>
                <td><strong>Total</strong></td>
            </tr>
            <tr>
                <td colspan="4" style="visibility: hidden;">&nbsp;</td>
            </tr>
           
            <tr>
                <td>Room Rate</td>
                <td><?php echo number_format($inpatients->dayDiff,2)?></td>
                <td><?php echo number_format(floatval($inpatients->room_rate), 2)?></td>
                <td><?php echo number_format(floatval($inpatients->room_rate_amt),2)?></td>
            </tr>
            <tr>
                <td colspan="4" style="visibility: hidden;">&nbsp;</td>
            </tr>
            <?php 
            $payamt = $total_paid_yet;
            $totamt = $inpatients->room_rate_amt;
            if(!empty($inpatients_items)){ 
                    foreach ($inpatients_items as $key => $value) { $totamt += $value->subtotal; ?>
                        <tr>
                            <td><?php echo @$products[$value->product_id];?></td>
                            <td><?php echo $value->qty;?></td>
                            <td><?php echo $value->price;?></td>
                            <td><?php echo $value->subtotal;?></td>
                            
                        </tr>
                        <tr>
                            <td colspan="4" style="visibility: hidden;">&nbsp;</td>
                        </tr>
            <?php } } ?>

        </table>

        <div style="border-top: 1px dashed #000;margin-top: 10px;margin-bottom: 10px;"></div>

        <div style="font-size: 12px;text-align: right;">
            <center style="margin-top: 10px;margin-bottom: 10px;">Paid Amount: <?php echo number_format($payamt,2);?></center>
            <center style="margin-top: 10px;margin-bottom: 10px;">Discount: <?php echo number_format($tot_discunt,2);?></center>
            <center><b style="margin-top: 10px;margin-bottom: 10px;">Total: <?php echo number_format(floatval($totamt),2);?></b></center>
            
        </div>

        <div style="border-top: 1px dashed #000;margin-top: 10px;margin-bottom: 20px;"></div>

        <div>
            <span><center>Thank you for visiting us<br>Powered by Hyper-Soft</center></span>
        </div>
    </div>
</div>
<!--============== For Print only ===============-->
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

<script type="text/javascript">

    $(document).ready(function () {

        var tprice = 0;
        $('.tprice').each(function() {
            var price = $(this).val() || 0; 
            tprice += parseFloat(price) || 0;
        });

        var payamtss = 0;
        $('.payamt').each(function() {
            var payamts = $(this).val() || 0; 
            payamtss += parseFloat(payamts) || 0;
        });

        var duepay = parseFloat(tprice) - parseFloat(payamtss); 

        $('#duepay').text(duepay.toFixed(2));

        var msg = '<?php echo $msg?>';
        if(msg !=''){
            $('#btndr').removeClass('hidden');
        }
    });

    function applydiscount(that, id){

        var val = that.value || 0; 

        var orgp = $(that).data('orgprice'); 

        if(parseFloat(val) > parseFloat(orgp)){
            val = orgp;
            $(that).val(val);
        }

        var newprice = parseFloat(orgp) - parseFloat(val); 

        var nprice = newprice.toFixed(2);

        $('#price'+id).val(nprice);

        var tprice = 0;
        $('.tprice').each(function() {
            var price = $(this).val() || 0; 
            tprice += parseFloat(price) || 0;
        });

        var payamt = 0;
        $('.payamt').each(function() {
            var prices = $(this).val() || 0; 
            payamt += parseFloat(prices) || 0;
        });

        var duepay = parseFloat(tprice) - parseFloat(payamt); 

        $('#duepay').text(duepay.toFixed(2));
    }

    function paymentInput(that){

        var tprice = 0;
        $('.tprice').each(function() {
            var price = $(this).val() || 0; 
            tprice += parseFloat(price) || 0;
        });

        var totpaid = <?php echo $total_paid_yet?>;

        var maxLength = parseFloat(tprice) - parseFloat(totpaid);
        var val = that.value;

        if (val > maxLength) {
            $('#payInput').val(maxLength);
        }
    }

</script>