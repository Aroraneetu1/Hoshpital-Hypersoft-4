<?php 

$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name')); 
//$providers = get_key_value_array('users', 'id', array('first_name', 'last_name')); 
$services = get_key_value_array('service_types', 'id', array('name'));
$productname = get_key_value_array('products', 'id', array('name')); 

?>

<style type="text/css">
    
    .table td, th {
        padding: 3px 8px !important;
    }
</style>
<div class="form">

    <?php include('inpatient_div.php'); ?>
    <hr>
    <form action="" class="cmxform form-horizontal form-example" method="post">

        <div class="row">
            <div class="col-md-5">

                <div class="row">
                    <div class="col-md-6">
                        <label>Admission Date</label>
                        <input name="check_in" id="dateFrom" class="form-control" value="<?php echo $inpatients->check_in;?>">
                    </div>

                    <div class="col-md-6">
                        <label>Discharge Date</label>
                        <input name="check_out" id="dateTo" class="form-control" value="<?php echo $inpatients->check_out;?>">
                    </div>

                    
                </div>
                <div class="row" style="margin-top: 12px;">
                    <div class="col-md-6">
                        <label>Patients</label>
                        <input type="hidden" name="appointment_id" id="app_id" value="<?php echo $inpatients->appointment_id;?>">
                        <select name="patient_id" class="form-control" required onchange="getappid(this)">
                            <option value="">Please Select</option>
                            <?php if(count($inpatientlist) > 0): ?>
                                <?php foreach($inpatientlist as $val): ?>
                                    <option value="<?php echo $val->consumer_id; ?>"  data-appid="<?php echo $val->id; ?>" <?php if($inpatients->patient_id == $val->consumer_id){echo 'selected';}?>><?php echo @$consumers[$val->consumer_id] .' - '.@$services[$val->service_id].' - '.$val->token_number; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?> 
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Room Number</label>
                        <select name="room_no" id="roomNo" class="form-control" required onchange="getroomrate()" style="pointer-events: none;appearance:none;">
                            <option value="">Please Select</option>
                            <?php if(count($rooms) > 0): ?>
                                <?php foreach($rooms as $val): ?>
                                    <option  value="<?php echo $val->id; ?>" data-val="<?php echo $val->room_rate; ?>" <?php if($inpatients->room_no == $val->id){echo 'selected';}?>>
                                        <?php echo $val->room_number.' - '.$val->room_name; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?> 
                        </select>
                    </div>
                </div>
                <div class="row" style="margin-top: 12px;">
                    <div class="col-md-6">
                        <label>Room Rate</label>
                        <input name="room_rate" id="roomRate" class="form-control" onchange="getroomrate()" value="<?php echo $inpatients->room_rate;?>">
                        <input name="room_rate_amt" id="roomRateamt" type="hidden" value="<?php echo $inpatients->room_rate_amt;?>">
                        <input name="dayDiff" id="rdayDiff" type="hidden" value="<?php echo $inpatients->dayDiff;?>">
                    </div>

                    <div class="col-md-6">
                        <label>Admission Reason</label>
                        <input name="admission_reason" class="form-control" value="<?php echo $inpatients->admission_reason;?>">
                        
                    </div>
                </div>
                <div class="row" style="margin-top: 12px;">
                    <div class="col-md-6">
                        <label>Select Doctor</label>
                        <select name="doctor_id" class="form-control" required>
                            <option value="">Please Select</option>
                            <?php if(count($doctors) > 0): ?>
                                <?php foreach($doctors as $val): ?>
                                    <option value="<?php echo $val->id; ?>" <?php if($inpatients->doctor_id == $val->id){echo 'selected';}?>>
                                        <?php echo $val->first_name.' '.$val->last_name; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?> 
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Discharge Summary</label>
                        <textarea class="form-control" name="remark" rows="3"><?php echo $inpatients->remark;?></textarea>
                    </div>
                </div>
                <div class="row" style="margin-top: 12px;">
                    <div class="col-md-12">
                        <label>Filter by Product Name</label>
                        <input class="form-control" onkeyup="getproduct(this.value)">
                    </div>
                </div>
                <table class="table table-bordered table-sm table-responsive" style="margin-top: 5px;">
                    <thead class="text-primary">
                        <th>Product</th>
                        <th>Price</th>
                        <th style="width:1%;"><a href="javascript:;"><i class="fa fa-plus-circle fa-lg"></i></a></th>
                        <th style="width:1%;"><a href="javascript:;"><i class="fa fa-minus-circle fa-lg"></i></a></th>
                    </thead>
                    <tbody id="resdata">
                         
                    </tbody>
                </table>

            </div>
            <div class="col-md-7">

                <table class="table table-bordered table-sm my-table" style="margin-top: 20px;">
                    <thead class="panel-footer">
                        <th><b>Product</b></th>
                        <th style="width: 90px;"><b>Price</b></th>
                        <th style="width: 90px;"><b>Quantity</b></th>
                        <th style="width: 90px;"><b>Subtotal</b></th>
                        <th style="width: 10px;"><b><a href="javascript:;" class="text-danger"><i class="fa fa-trash fa-lg"></i></a></b></th>
                    </thead>
                    <tbody id="addproducts">

                        <?php 
                        $totamt = 0;
                        if(!empty($inpatients_items)){
                                foreach ($inpatients_items as $k => $v){ 
                                    $totamt += $v->subtotal;
                                ?>
                                    <tr class="remall rem<?php echo $v->product_id;?>">

                                        <td><input name="product_id[]" value="<?php echo $v->product_id;?>" type="hidden"> 
                                            <?php echo $productname[$v->product_id];?></td>

                                        <td><input readonly name="price[]" id="p<?php echo $v->product_id;?>" onkeyup="recalc(<?php echo $v->product_id;?>)" type="text" value="<?php echo $v->price;?>" class="form-control" required></td>

                                        <td><input readonly name="qty[]" id="q<?php echo $v->product_id;?>" onkeyup="recalc(<?php echo $v->product_id;?>)" type="text" value="<?php echo $v->qty;?>" class="form-control" required></td>

                                        <td><input readonly name="sub_amount[]" id="subt<?php echo $v->product_id;?>" type="text" value="<?php echo $v->subtotal;?>" class="form-control subtot" required></td>

                                        <td class="text-primary plus-parent">
                                            <a href="javascript: void(0);" class="text-danger" onclick="removeitemdb(<?php echo $v->product_id;?>, <?php echo $v->id;?>)">
                                            <i class="fa fa-trash fa-lg"></i></a>
                                        </td>
                                    </tr>

                            
                        <?php } } ?>
                        
                    </tbody>
                    <tfoot class="panel-footer">
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="font-weight: bold;">Total</td>
                            <td style="font-weight: bold;" id="gtotal"><?php echo number_format($totamt,2);?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="pos-checkout">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="javascript: void(0);" onclick="clearAll()" class="btn btn-danger pull-left">
                                Clear all
                            </a>
                            
                            <button name="submit" type="submit" class="btn btn-primary pull-right">
                                Save Changes
                            </button>
                        </div>  
                    </div>  
                </div>

            </div>
        </div>
    </form>
</div>
<script type="text/javascript">

    $(document).ready(function(){
        var today = new Date('<?php echo $inpatients->check_out;?>');
        var tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);

        $("#dateFrom").datetimepicker({
            format: "Y-m-d H:i",
            minDate: today,
            onClose: function (selectedDateTime) {
                if (selectedDateTime) {
                    var selectedDateObj = new Date(selectedDateTime);
                    var nextDay = new Date(selectedDateObj);
                    nextDay.setDate(selectedDateObj.getDate() + 1);

                    $("#dateTo").datetimepicker('setOptions', { minDate: nextDay });
                    getroomrate();
                }
            }
        }).datetimepicker("setDate", today);

        $("#dateTo").datetimepicker({
            format: "Y-m-d H:i",
            minDate: tomorrow,
            onClose: function (selectedDateTime) {
                if (selectedDateTime) {
                    var selectedDateObj = new Date(selectedDateTime);
                    $("#dateFrom").datetimepicker('setOptions', { maxDate: selectedDateObj });
                    getroomrate();
                }
            }
        }).datetimepicker("setDate", tomorrow);

        /*$("#dateFrom").datepicker({
            dateFormat: "yy-mm-dd",
            maxDate: today,
            onSelect: function(selectedDate) {
                var selectedDateObj = new Date(selectedDate);
                var nextDay = new Date(selectedDateObj);
                nextDay.setDate(selectedDateObj.getDate() + 1);

                $("#dateTo").datepicker("option", "minDate", nextDay);
                //$("#dateTo").datepicker("setDate", nextDay);

                getroomrate();
            }
        })

        $("#dateTo").datepicker({
            dateFormat: "yy-mm-dd",
            minDate: tomorrow,
            onSelect: function(selectedDate) {
                var selectedDateObj = new Date(selectedDate);
                $("#dateFrom").datepicker("option", "maxDate", selectedDateObj);

                getroomrate();
            }
        });*/
    });

    function getappid(that){
        var app_id = $(that).find(":selected").attr("data-appid") || 0;
        $('#app_id').val(app_id);
    }
    
    function getroomrate(){
        var amt = $('#roomRate').val(); 
        if(amt == 0){
            var amt = $('#roomNo').find(":selected").attr("data-val") || 0;
        }
        let admissionDate = new Date($("#dateFrom").val());
        let dischargeDate = new Date($("#dateTo").val());

        let timeDiff = dischargeDate - admissionDate;
        let dayDiff = timeDiff / (1000 * 60 * 60 * 24);

        var roomrate = parseFloat(amt) *  parseFloat(dayDiff); 
        $('#roomRate').val(amt);
        $('#roomRateamt').val(roomrate);
        $('#rdayDiff').val(dayDiff);
    }

    function additem(key){

        var product_id = key;
        var product_name = $('#pname'+key).text();
        var product_price = $('#pprice'+key).text() || 0;
        var product_qty = '1.00';
        var product_subtots = parseFloat(product_price) * parseFloat(product_qty);
        var product_subtot = product_subtots.toFixed(2);

        /*alert(product_id);
        alert(product_name);
        alert(product_price);
        alert(product_qty);
        alert(product_subtot);*/

        var className = 'rem'+product_id+'';
        if (!$("tr").hasClass(className)) {
            
            var tr = '<tr class="remall rem'+product_id+'">';
                tr +='<td><input name="product_id[]" value="'+product_id+'" type="hidden"> '+product_name+'</td>';
                tr +='<td><input readonly name="price[]" id="p'+product_id+'" onkeyup="recalc('+product_id+')" type="text" value="'+product_price+'" class="form-control" required></td>';
                tr +='<td><input readonly name="qty[]" id="q'+product_id+'" onkeyup="recalc('+product_id+')" type="text" value="'+product_qty+'" class="form-control" required></td>';
                tr +='<td><input readonly name="sub_amount[]" id="subt'+product_id+'" type="text" value="'+product_subtot+'" class="form-control subtot" required></td>';
                tr +='<td class="text-primary plus-parent">';
                    tr +='<a href="javascript: void(0);" class="text-danger" onclick="removeitem('+product_id+')">';
                    tr +='<i class="fa fa-trash fa-lg"></i></a>';
                tr +='</td>';
            tr +='</tr>';

            $('#addproducts').append(tr);
            recalctotal();

        }else{

            var id = product_id;
            var q = $('#q'+id).val() || 0;
            var nqs = parseFloat(q) + parseInt(1);
            var nq = nqs.toFixed(2);
            $('#q'+id).val(nq);

            var p = $('#p'+id).val() || 0;
            var product_subtots = parseFloat(p) * parseFloat(nq);
            var product_subtot = product_subtots.toFixed(2);
            $('#subt'+id).val(product_subtot);
            recalctotal();
        }
    }

    function minsitem(key){

        var product_id = key;

        var id = product_id;
        var q = $('#q'+id).val() || 0;
        var nqs = parseFloat(q) - parseInt(1);
        var nq = nqs.toFixed(2);
        $('#q'+id).val(nq);

        var p = $('#p'+id).val() || 0;
        var product_subtots = parseFloat(p) * parseFloat(nq);
        var product_subtot = product_subtots.toFixed(2);
        $('#subt'+id).val(product_subtot);
        recalctotal();
    }

    function removeitemdb(id, rid){

        $.ajax({
            type : 'POST',
            url: '<?php echo get_site_url('appointments/remove_inpatients_products'); ?>',
            data: {id: rid},
            success: function(result){

                $('.rem'+id).remove();
                recalctotal();
            }
        })
    }

    function recalc(id){
        var p = $('#p'+id).val() || 0;
        var q = $('#q'+id).val() || 0;
        var product_subtots = parseFloat(p) * parseFloat(q);
        var product_subtot = product_subtots.toFixed(2);
        $('#subt'+id).val(product_subtot);
        recalctotal();
    }

    function clearAll(){
        $('.remall').remove();
        recalctotal();
    }

    function removeitem(id){
        $('.rem'+id).remove();
        recalctotal();
    }

    function recalctotal(){
        let total = 0;
        $('.my-table .subtot').each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        $('#gtotal').text(total.toFixed(2));
    }

    getproduct('');
    function getproduct(pro){

        $.ajax({
            type : 'POST',
            url: '<?php echo get_site_url('appointments/get_inpatients_products'); ?>',
            data: {keyword: pro},
            success: function(result){

                $('#resdata tr').remove();
                $('#resdata').append(result);
            }
        })
    }
</script>