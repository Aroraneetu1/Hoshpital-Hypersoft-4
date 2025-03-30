<div class="form">
    <form action="" class="cmxform form-horizontal form-example" method="post">
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Given Name</label>
                <input type="text" class="form-control" name="first_name" value="<?php echo $row->first_name; ?>"> 
                <span class="error"><?php echo $validator->getError("first_name"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Middle Name</label>
                <input type="text" class="form-control" name="middle_name" value="<?php echo $row->middle_name; ?>"> 
                <span class="error"><?php echo $validator->getError("middle_name"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Family Name</label>
                <input type="text" class="form-control" name="last_name" value="<?php echo $row->last_name; ?>"> 
                <span class="error"><?php echo $validator->getError("last_name"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Gender</label>
                <select class="form-control" name="gender"> 
                    <option <?php if($row->gender == "M"){ echo "selected"; } ?> value="M">Male</option>
                    <option <?php if($row->gender == "F"){ echo "selected"; } ?> value="F">Female</option>
                </select>
                <span class="error"><?php echo $validator->getError("gender"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label col-lg-2">DOB</label>
                <input type="text" class="form-control" name="dob" value="<?php echo $row->dob; ?>">
                <span class="error"><?php echo $validator->getError("dob"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Address</label>
                <input type="text" class="form-control" name="address" value="<?php echo $row->address; ?>"> 
                <span class="error"><?php echo $validator->getError("address"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>City/Village</label>
                <input type="text" class="form-control" name="city" value="<?php echo $row->city; ?>"> 
                <span class="error"><?php echo $validator->getError("city"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>State/Province</label>
                <input type="text" class="form-control" name="state" value="<?php echo $row->state; ?>"> 
                <span class="error"><?php echo $validator->getError("state"); ?></span>
            </div>
            <?php $countries = get_country_array(); ?>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Country</label>
                <select class="form-control" name="country">
                    <option selected="selected" value="SO">Somalia</option>
                    <?php foreach($countries as $key => $value): ?>
                        <option <?php if($row->country == $key){ echo "selected"; } ?> value="<?php echo $key; ?>">
                            <?php echo $value; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error"><?php echo $validator->getError("country"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Postal Code</label>
                <input type="text" class="form-control" name="postal_code" value="<?php echo $row->postal_code; ?>"> 
                <span class="error"><?php echo $validator->getError("postal_code"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Phone Number</label>
                <input type="text" class="form-control" name="phone_number" value="<?php echo $row->phone_number; ?>"> 
                <span class="error"><?php echo $validator->getError("phone_number"); ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Alternate Phone Number</label>
                <input type="text" class="form-control" name="alternate_phone_number" value="<?php echo $row->alternate_phone_number; ?>"> 
                <span class="error"><?php echo $validator->getError("alternate_phone_number"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>&nbsp;</label>
                <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                	Save Changes
                </button>
            </div>
        </div>
   </form>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("input[name='dob']").datepicker({
        dateFormat: "d MM yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "1950:c",
    });
});
</script>