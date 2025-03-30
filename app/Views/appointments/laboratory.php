<?php 

$permissions = unserialize(get_session_data('permissions'));

$products = get_key_value_array('products', 'id', array('name'));
$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));
$providers = get_key_value_array('users', 'id', array('first_name', 'last_name'));

/*echo '<pre>';
print_r($chk_payment_status);
echo '</pre>';*/

$receipt_array = array();
$chk_payment_status = array();
if(!empty($rows) && is_array($rows)){
    foreach ($rows as $key => $value) {
        $receipt_array[$value->receipt_no] = 'REC-'.$value->receipt_no;

        $chk_payment_status[] = $value->payment_status;
    }
}

/*echo '<pre>';
print_r($chk_payment_status);
echo '</pre>';*/

?>

<div class="form">

    <form action="<?php echo get_site_url("appointments/lab_result");?>" method="post">
        <input type="hidden" name="appointment_id" value="<?= service('uri')->getSegment(3) ?>">
            <div class="col-md-3">
                <!-- <?php /*if(get_session_data('role') != 'Lab'){ ?>
                    <select class="form-control" id="receiptNo">
                        <option value="">Select one</option>
                        <?php if(!empty($receipt_array) && is_array($receipt_array)){ foreach ($receipt_array as $key => $value) { ?>
                            <option value="<?php echo $key;?>"><?php echo $value;?></option>
                        <?php } } ?>
                    </select>
                <?php } */?> -->
            </div>

            <div class="col-md-2">

                
                <?php if(get_session_data('role') != 'Lab'){ ?>
                    <!-- <a type="button" class="btn btn-danger" onclick="labReceipt();"> Receipt</a>
                    <button value="save" name="submit" type="submit" class="btn btn-success" > Pay </button> -->
                <?php } ?>

            </div>

            <div class="col-md-7">
                
            <?php if(service('uri')->getSegment(3) != '') { ?>

                    <?php if(!in_array(0, $chk_payment_status)){ ?>

                        <?php if(get_session_data('role') != 'Receptionist'){ ?>

                            <button value="save" name="submit" type="submit" class="btn btn-success pull-right" style="margin-left: 5px;" <?php if(empty($rows)){echo 'disabled';}?>> Save Changes </button>

                        <?php } ?>
                    <?php } ?>

                    <a type="button" class="btn btn-danger pull-right" onclick="fzPrint('labResult', 'Report');"> Print</a>
                    

                    

                <?php } ?>
            </div>
           

            <div class="col-md-12" style="margin-top: 10px;">

                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th style="width: 2%;"><i class="fa fa-trash text-danger fa-lg"></i></th>
                            <th style="width: 15%;">Service Name</th>
                            <!-- <th style="width: 12%;">Price</th>
                            <th style="width: 12%;">Discount</th> -->
                            <th>Result</th>
                            <th style="width: 15%;">Normal Range</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $tot_price = 0;
                        $tot_discunt = 0;
                        if (!empty($rows)) { 
                            foreach ($rows as $key => $value) { 
                                if ($value->payment_status == 0) { 
                                    $tot_price += $value->lt_price;
                                } 
                                $tot_discunt += $value->discount;
                        ?>
                            <tr>
                                <td>
                                    <?php if ($value->payment_status == 0 || get_session_data('role') == 1) { ?>
                                        <a href="<?php echo get_site_url("appointments/removelb/" . $value->id . "/" . $value->appointment_id) ?>">
                                            <i class="fa fa-trash text-danger fa-lg"></i>
                                        </a>
                                    <?php } ?>
                                </td>
                                <td><?php echo $products[$value->product_id] ?></td>
                                <td>
                                    <input type="text" class="form-control" name="result[<?php echo $value->product_id ?>]" placeholder="Result" value="<?php echo $value->result ?>" 
                                    <?php if (get_session_data('role') == 'Receptionist') { echo 'readonly'; } ?>>
                                </td>
                                <td>
                                    <input type="text" class="form-control" placeholder="Normal Range" value="<?php echo $value->lt_normal_range ?>" readonly>
                                </td>
                            </tr>
                        <?php 
                            }  // **Closing foreach loop**
                        }  // **Closing if condition**
                        ?>
                    </tbody>

                    <!-- <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="text-align: right;"><?php echo number_format($tot_price,2);?></td>
                            <td style="text-align: right;"><?php echo number_format($tot_discunt,2);?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot> -->
                </table>
            </div>
       
    </form>
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
<div class="hidden">
    <div class="container1 lab-result-container" id="labResult" >

        <?php echo get_print_header();?>

        <div style="text-align: center; margin-bottom: 20px;font-size: 20px;"><strong>Lab Result</strong></div>
        <div style="text-align: left; margin-bottom: 15px;">
            <p style="margin: 0;"><strong>Prescribing Doctor:</strong> <?php echo $providers[$appointment->provider_id]; ?></p>
            <p style="margin: 0;"><strong>Patient ID:</strong> <?php echo get_formated_id('P', $appointment->consumer_id); ?></p>
            <p style="margin: 0;"><strong>Patient Name:</strong> <?php echo $consumers[$appointment->consumer_id]; ?></p>
            <p style="margin: 0;"><strong>Date:</strong> <?php echo date('d M, Y')?></p>
        </div>

        
        <table style="width: 100%; margin-bottom: 50px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; text-align: left;padding: 6px;">TEST NAME</th>
                    <th style="border: 1px solid #000; text-align: left;padding: 6px;">RESULT</th>
                    <th style="border: 1px solid #000; text-align: left;padding: 6px;">NORMAL RANGE</th>
                </tr>
            </thead>
            <tbody>

                <?php if(!empty($rows)){ 
                    foreach ($rows as $key => $value) { ?>
                        
                        <tr>
                            <td style="border: 1px solid #000;padding: 6px;"><?php echo $products[$value->product_id]?></td>
                            <td style="border: 1px solid #000;padding: 6px;"><?php echo $value->result?></td>
                            <td style="border: 1px solid #000;padding: 6px;"><?php echo $value->lt_normal_range?></td>
                            
                        </tr>
                    
                <?php } } ?>
                
            </tbody>
        </table>

        
        <div style="position: fixed;bottom: 20px;left: 65px;width: 100%;text-align: left;">
            <p><strong>Doctor’s Signature:</strong><span style="border-bottom: 1px solid #000;"> <?php echo $providers[$appointment->provider_id]; ?></span></p>
        </div>
    </div>
</div>

<script type="text/javascript">
    
    /*function print_lab() {
        $('.container1').css('display','block');
        setTimeout(function(){ $('.container1').css('display','none');}, 1000);
        window.print();
    }

    $(document).ready(function () {

        window.onafterprint = function () {
           $('.container1').css('display','none');
        };
    });*/

    function labReceipt(){
        var receiptNo = $('#receiptNo').val();
        if(receiptNo !=''){
            window.location.href = '<?php echo get_site_url("appointments/receipt/".$appointment->id."/lab/")?>'+receiptNo;
        }
    }

    function applydiscount(that, id){

        var val = that.value || 0; 

        var orgp = $(that).data('orgprice'); 

        var newprice = parseFloat(orgp) - parseFloat(val); 

        var nprice = newprice.toFixed(2);

        $('#price'+id).val(nprice);

    }
</script>