<?php 

$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));
$products = get_key_value_array('products', 'id', array('name'));
$services = get_key_value_array('service_types', 'id', array('name'));
$services_amount = get_key_value_array('service_types', 'id', array('amount'));
$payment_types = get_key_value_array('payment_types', 'id', array('name'));


/*echo '<pre>';
print_r($paymentss);
echo '</pre>';*/

?>

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
strong, th{
    font-weight: 600;
}
</style>
<div class="row" style="margin-right: 10px;">
    <div class="col-md-12" >         
        <a type="button" class="btn btn-danger pull-right hide-on-print" onclick="fzPrint('feereceipt', 'Report');" style="margin-left: 10px;"><i class="fa fa-print" style="margin-right:5px;"></i> Print</a>
        <a type="button" class="btn btn-primary pull-right" onclick="window.history.back()"><i class="fa fa-reply" style="margin-right:5px;"></i> Go back</a>
        <!-- <a type="button" class="btn btn-danger pull-right hide-on-print" onclick="window.print();"><i class="fa fa-print" style="margin-right:5px;"></i> Print</a> -->
    </div>
</div>
<div class="container print-section" id="feereceipt">
    <!-- <div class="bg-primary text-center" style="margin-bottom: 15px;padding:10px;">
        <h2>Hospital Name</h2>
        <p>123 Business St, City, Country</p>
        <p>Email: info@hospital.com | Phone: +123-456-7890</p>
    </div> -->

    <?php echo get_print_header();?>

    <?php if($this->uri->segment(4) == 'fee'){ ?>

        <div style="text-align: center; margin-bottom: 20px;font-size: 20px;"><strong>Appointment / Consultation Fee</strong></div>

    <?php }else{ ?>

        <div style="text-align: center; margin-bottom: 20px;font-size: 20px;"><strong>Payment Invoice</strong></div>

    <?php }?>

    <!-- Receipt Information -->
    <div class="row" style="margin-bottom: 15px;">
        
        <div class="col-md-12">
            <p><strong>Patient ID:</strong> <?php echo get_formated_id('P', $appointment->consumer_id); ?></p>
            <p><strong>Patient Name:</strong> <?php echo $consumers[$appointment->consumer_id];?></p>
            <!-- <p><strong>Service:</strong> <?php echo $services[$appointment->service_id];?></p> -->
            <p><strong>Appointment No:</strong> <?php echo $services[$appointment->service_id];?> - <?php echo $appointment->token_number;?></p>
            <p><strong>Receipt No:</strong> REC-<?php echo $this->uri->segment(5);?></p>
            <p><strong>Date:</strong> <?php echo date('d M, Y')?></p>
        
        </div>
        
    </div>

    <!-- Itemized List -->
    
    <div class="row" >
        
        <div class="col-md-12" style="margin-top: 25px;">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <td>#</td>
                            <td><strong>Item</strong></td>
                            <td><strong>Price</strong></td>
                        </tr>
                    </thead>

                    <?php if($this->uri->segment(4) == 'fee'){ ?>

                        <tbody>
                            <tr>
                                <th>1</th>
                                <td>Appointment / Consultation Fee (<?php echo $services[$appointment->service_id];?>)</td>
                                <td><?php echo number_format($services_amount[$appointment->service_id],2);?></td>
                            </tr>
                            <? 
                            $tot_price = $services_amount[$appointment->service_id]; ?>
                            
                            
                        </tbody>
                        <tfoot>
                            <!-- <tr>
                                <th></th>
                                <td><strong>Subtotal</strong></td>
                                <td><strong><?php //echo number_format($tot_price,2)?></strong></td>
                            </tr> -->
                            <tr>
                                <th></th>
                                <td><strong>Total</strong></td>
                                <td><strong><?php echo number_format($tot_price,2)?></strong></td>
                            </tr>
                        </tfoot>

                    <? }else if($this->uri->segment(4) == 'lab'){ ?>

                        <tbody>
                            
                            <? 
                            $tot_price = 0; 
                            $count = 0;
                            if(!empty($rows)){ 
                                foreach ($rows as $key => $value) { 
                                    if($value->receipt_no == $this->uri->segment(5)) { $tot_price += $value->lt_price; $count++; ?>
                                    <tr>
                                        <td><?php echo $count;?></td>
                                        <td><?php if(isset($products[$value->product_id])){ echo $products[$value->product_id]; }else{echo "Consultations Fee";}?></td>
                                        <td><?php echo number_format($value->lt_price,2)?></td>
                                    </tr>
                            <?php } } } ?>
                            
                        </tbody>
                        <tfoot>
                            <!-- <tr>
                                <th></th>
                                <td><strong>Subtotal</strong></td>
                                <td><strong><?php //echo number_format($tot_price,2)?></strong></td>
                            </tr> -->
                            
                            <?php if(!empty($paymentss)){
                                foreach ($paymentss as $kk => $vv) {
                                    if($vv->receipt_id == $this->uri->segment(5)){ ?>
                                        <tr>
                                            <td></td>
                                            <td style="float: right !important;border: none;">Payment Type: <?php echo @$payment_types[$vv->payment_type]?></td>
                                            <td><?php echo number_format($vv->payment_type_amount,2)?></td>
                                        </tr>
                            <?php } } } ?>

                            <tr>
                                <th></th>
                                <td><strong>Total</strong></td>
                                <td><strong><?php echo number_format($tot_price,2)?></strong></td>
                            </tr>
                        </tfoot>

                    <? } ?>
                </table>
            </div>
        </div>

    </div>
    
</div>

