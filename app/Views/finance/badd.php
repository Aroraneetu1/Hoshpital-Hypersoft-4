<?php 

$permissions = unserialize(get_session_data('permissions'));
$validation = $validation ?? \Config\Services::validation();

$categoryConfig = config('Category'); 
$yes_no_option = $categoryConfig->yes_no_option; 

//if($permissions['patients'] == 0){  

    //echo no_access_msg(); 


// }else{ ?>

<div class="form">
    <form action="" class="cmxform form-horizontal form-example" method="post">
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Bank Name</label>
                <input type="text" class="form-control" name="bank_name" value="<?php echo set_value("bank_name"); ?>"> 
                <span class="error"><?php echo $validation->getError("bank_name"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Branch Code</label>
                <input type="text" class="form-control" name="branch_code" value="<?php echo set_value("branch_code"); ?>"> 
                <span class="error"><?php echo $validation->getError("branch_code"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Bank Phone</label>
                <input type="text" class="form-control" name="bank_phone" value="<?php echo set_value("bank_phone"); ?>"> 
                <span class="error"><?php echo $validation->getError("bank_phone"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Bank Account</label>
                <input type="text" class="form-control" name="bank_account" value="<?php echo set_value("bank_account"); ?>">
                <span class="error"><?php echo $validation->getError("bank_account"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Opening Balance</label>
                <input type="text" class="form-control" name="opening_balance" value="<?php echo set_value("opening_balance"); ?>"> 
                <span class="error"><?php echo $validation->getError("opening_balance"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Current Balance</label>
                <input type="text" class="form-control" name="current_balance" value="<?php echo set_value("current_balance"); ?>"> 
                <span class="error"><?php echo $validation->getError("current_balance"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Main Bank Account</label>
                <select class="form-control" name="main_account">
                    <?php foreach($yes_no_option as $key => $value): ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error"><?php echo $validation->getError("main_account"); ?></span>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Payment Type</label>
                <select class="form-control" name="payment_type">
                     <option value="">Select One</option>
                    <?php foreach($paymenttypes as $key => $value): ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error"><?php echo $validation->getError("payment_type"); ?></span>
            </div>
        </div>
        
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>&nbsp;</label>
                <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                    Add New Bank
                </button>    
            </div>
        </div>
   </form>
</div>

<?php //} ?>
<script type="text/javascript">
/*$(document).ready(function(){
    $("input[name='dob']").datepicker({
        dateFormat: "d MM yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "1950:c",
    });
});*/
</script>