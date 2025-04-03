<?php 

$consumers = get_key_value_array('consumers', 'id', ['first_name', 'last_name']);
$products = get_key_value_array('products', 'id', ['name']);
$services = get_key_value_array('service_types', 'id', ['name']);
$services_amount = get_key_value_array('service_types', 'id', ['amount']);
$payment_types = get_key_value_array('payment_types', 'id', ['name']);
$uri = service('uri'); 

?>

<style type="text/css">
.fz-col-filter {
    font-weight: normal; 
    width: 100%; 
    border: none; 
    background-color: #eee; 
    height: 35px;
    display: block;
}
.paginate_button {
    padding: 0.2em 0.3em !important;
}
.next {
    background: transparent;
}
.dataTables_paginate {
    width: 20%;
}
.dataTables_length {
   display: none;
}
strong, th {
    font-weight: 600;
}
</style>

<div class="row" style="margin-right: 10px;">
    <div class="col-md-12">         
        <a type="button" class="btn btn-danger pull-right hide-on-print" onclick="fzPrint('feereceipt', 'Report');" style="margin-left: 10px;">
            <i class="fa fa-print" style="margin-right:5px;"></i> Print
        </a>
        <a type="button" class="btn btn-primary pull-right" onclick="window.history.back()">
            <i class="fa fa-reply" style="margin-right:5px;"></i> Go back
        </a>
    </div>
</div>

<div class="container print-section" id="feereceipt">
    <?php echo get_print_header();?>

    <div style="text-align: center; margin-bottom: 20px; font-size: 20px;">
        <strong>
        <?php 
$uri = service('uri');
echo ($uri->getSegment(4) == 'fee') ? "Appointment / Consultation Fee" : "Payment Invoice"; 
?>
        </strong>
    </div>

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            <p><strong>Patient ID:</strong> <?php echo get_formated_id('P', $appointment->consumer_id); ?></p>
            <p><strong>Patient Name:</strong> <?php echo $consumers[$appointment->consumer_id] ?? ''; ?></p>
            <p><strong>Appointment No:</strong> <?php echo $services[$appointment->service_id] ?? ''; ?> - <?php echo $appointment->token_number; ?></p>
                <p><strong>Receipt No:</strong> REC-<?php echo $uri->getSegment(5); ?></p>
            <p><strong>Date:</strong> <?php echo date('d M, Y'); ?></p>
        </div>
    </div>

    <div class="row">
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
                    <tbody>
                        <?php 
                        $tot_price = 0;
                        $count = 0;
                        
                        if ($uri->getSegment(4) == 'fee') { 
                            $tot_price = $services_amount[$appointment->service_id] ?? 0;
                        ?>
                            <tr>
                                <th>1</th>
                                <td>Appointment / Consultation Fee (<?php echo $services[$appointment->service_id] ?? ''; ?>)</td>
                                <td><?php echo number_format($tot_price, 2); ?></td>
                            </tr>
                        <?php 
                        } elseif ($uri->getSegment(4) == 'lab' && !empty($rows)) { 
                            foreach ($rows as $value) { 
                                if ($value->receipt_no == $uri->getSegment(5)) { 
                                    $tot_price += $value->lt_price; 
                                    $count++; 
                        ?>
                            <tr>
                                <td><?php echo $count; ?></td>
                                <td><?php echo $products[$value->product_id] ?? "Consultations Fee"; ?></td>
                                <td><?php echo number_format($value->lt_price, 2); ?></td>
                            </tr>
                        <?php 
                                } 
                            } 
                        } 
                        ?>
                    </tbody>
                    <tfoot>
                        <?php if ($uri->getSegment(4) == 'lab' && !empty($paymentss)) {
                            foreach ($paymentss as $vv) {
                                if ($vv->receipt_id == $uri->getSegment(5)) { ?>
                                    <tr>
                                        <td></td>
                                        <td style="float: right !important; border: none;">
                                            Payment Type: <?php echo $payment_types[$vv->payment_type] ?? ''; ?>
                                        </td>
                                        <td><?php echo number_format($vv->payment_type_amount, 2); ?></td>
                                    </tr>
                        <?php 
                                } 
                            } 
                        } ?>
                        <tr>
                            <th></th>
                            <td><strong>Total</strong></td>
                            <td><strong><?php echo number_format($tot_price, 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
