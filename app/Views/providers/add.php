<?php 

$permissions = unserialize(get_session_data('permissions'));
$validation = $validation ?? \Config\Services::validation();
?>
<style type="text/css">
    .form-group {
        margin-bottom: 10px;
    }
</style>

<?php if($permissions['doctors'] == 0){  

    echo no_access_msg(); ?>


<?php }else{ ?>

<div class="form">
    <form onsubmit="$('#destination_services option').prop('selected', true);" action="" class="cmxform form-horizontal form-example" method="post">
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>First Name</label>
                <input type="text" class="form-control" name="first_name" value="<?php echo set_value("first_name"); ?>"> 
                <span class="error"><?php echo $validation->getError("first_name"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Last Name</label>
                <input type="text" class="form-control" name="last_name" value="<?php echo set_value("last_name"); ?>"> 
                <span class="error"><?php echo $validation->getError("last_name"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Gender</label>
                <select class="form-control" name="gender"> 
                    <option <?php echo set_select("gender", "M"); ?> value="M">Male</option>
                    <option <?php echo set_select("gender", "F"); ?> value="F">Female</option>
                </select>
                <span class="error"><?php echo $validation->getError("gender"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>DOB</label>
                <input type="text" class="form-control" name="dob" value="<?php echo set_value("dob"); ?>">
                <span class="error"><?php echo $validation->getError("dob"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Email ID</label>
                <input type="text" class="form-control" name="email" value="<?php echo set_value("email"); ?>"> 
                <span class="error"><?php echo $validation->getError("email"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Address</label>
                <input type="text" class="form-control" name="address" value="<?php echo set_value("address"); ?>"> 
                <span class="error"><?php echo $validation->getError("address"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>State/Province</label>
                <input type="text" class="form-control" name="state" value="<?php echo set_value("state"); ?>"> 
                <span class="error"><?php echo $validation->getError("state"); ?></span>
            </div>
            <?php $countries = get_country_array(); ?>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Country</label>
                <select class="form-control" name="country">
                    <option selected="selected" value="SO">Somalia</option>
                    <?php foreach($countries as $key => $value): ?>
                        <option <?php echo set_select("country", $key); ?> value="<?php echo $key; ?>">
                            <?php echo $value; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error"><?php echo $validation->getError("country"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Postal Code</label>
                <input type="text" class="form-control" name="postal_code" value="<?php echo set_value("postal_code"); ?>"> 
                <span class="error"><?php echo $validation->getError("postal_code"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Room Number</label>
                <input type="text" class="form-control" name="room_number" value="<?php echo set_value("room_number"); ?>"> 
                <span class="error"><?php echo $validation->getError("room_number"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Phone Number</label>
                <input type="text" class="form-control" name="phone_number" value="<?php echo set_value("phone_number"); ?>"> 
                <span class="error"><?php echo $validation->getError("phone_number"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Education</label>
                <input type="text" class="form-control" name="provider_education" value="<?php echo set_value("provider_education"); ?>"> 
                <span class="error"><?php echo $validation->getError("provider_education"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xm-12">
                <label>Services</label>
                <select id="source_services" data-search="All Services">
                    <?php if($services): ?>
                        <?php foreach($services as $service): ?>
                            <option value="<?php echo $service->id; ?>">
                                <?php echo $service->name; ?>
                            </option>
                        <?php endforeach; ?>    
                    <?php endif; ?>    
                </select>
                <select name="services[]" multiple="multiple" id="destination_services" data-search="Selected Services">
                </select>
                <span class="error"><?php echo $validation->getError("services"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>&nbsp;</label>
                <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                    Add New Doctor
                </button>    
            </div>
        </div>
   </form>
</div>

<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
    $('#source_services, #destination_services').listswap({
        truncate: true,
        is_scroll : true,
        height: 150,
        label_add:'Add',
        label_remove: 'Remove',
        add_class: '',
    });

    $("input[name='dob']").datepicker({
        dateFormat: "d MM yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "1950:c",
    });
});
</script>