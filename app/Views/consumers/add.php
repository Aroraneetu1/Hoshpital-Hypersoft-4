<?php 
$permissions = unserialize(get_session_data('permissions'));
?>

<?php if($permissions['patients'] == 0) {  
    echo no_access_msg();
} else { ?>

<div class="form">
    <form action="" class="cmxform form-horizontal form-example" method="post">
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Given Name</label>
                <input type="text" class="form-control" name="first_name" value="<?= old('first_name'); ?>"> 
                <span class="error"><?= $validator->getError("first_name"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Middle Name</label>
                <input type="text" class="form-control" name="middle_name" value="<?= old('middle_name'); ?>"> 
                <span class="error"><?= $validator->getError("middle_name"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Family Name</label>
                <input type="text" class="form-control" name="last_name" value="<?= old('last_name'); ?>"> 
                <span class="error"><?= $validator->getError("last_name"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Gender</label>
                <select class="form-control" name="gender"> 
                    <option value="M" <?= old('gender') == 'M' ? 'selected' : ''; ?>>Male</option>
                    <option value="F" <?= old('gender') == 'F' ? 'selected' : ''; ?>>Female</option>
                </select>
                <span class="error"><?= $validator->getError("gender"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>DOB</label>
                <input type="text" class="form-control" name="dob" value="<?= old('dob'); ?>">
                <span class="error"><?= $validator->getError("dob"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Address</label>
                <input type="text" class="form-control" name="address" value="<?= old('address'); ?>"> 
                <span class="error"><?= $validator->getError("address"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>City/Village</label>
                <input type="text" class="form-control" name="city" value="<?= old('city'); ?>"> 
                <span class="error"><?= $validator->getError("city"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>State/Province</label>
                <input type="text" class="form-control" name="state" value="<?= old('state'); ?>"> 
                <span class="error"><?= $validator->getError("state"); ?></span>
            </div>
            <?php $countries = get_country_array(); ?>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Country</label>
                <select class="form-control" name="country">
                    <option selected="selected" value="SO">Somalia</option>
                    <?php foreach($countries as $key => $value): ?>
                        <option value="<?= $key; ?>" <?= old('country') == $key ? 'selected' : ''; ?>>
                            <?= $value; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error"><?= $validator->getError("country"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Postal Code</label>
                <input type="text" class="form-control" name="postal_code" value="<?= old('postal_code'); ?>"> 
                <span class="error"><?= $validator->getError("postal_code"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Phone Number</label>
                <input type="text" class="form-control" name="phone_number" value="<?= old('phone_number'); ?>"> 
                <span class="error"><?= $validator->getError("phone_number"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Alternate Phone Number</label>
                <input type="text" class="form-control" name="alternate_phone_number" value="<?= old('alternate_phone_number'); ?>"> 
                <span class="error"><?= $validator->getError("alternate_phone_number"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>&nbsp;</label>
                <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                    Add New Patient
                </button>    
            </div>
        </div>
   </form>
</div>

<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
    $("input[name='dob']").datepicker({
        dateFormat: "d MM yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "1950:c"
    });
});
</script>
