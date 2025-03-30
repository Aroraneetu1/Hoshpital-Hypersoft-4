<?php 
$permissions = unserialize(get_session_data('permissions'));
$validation = $validation ?? \Config\Services::validation();
?>

<style>
    .form-group {
        margin-bottom: 10px;
    }
</style>

<?php if ($permissions['doctors'] == 0): ?>
    <?= no_access_msg(); ?>
<?php else: ?>

<div class="form">
    <form action="" method="post" class="cmxform form-horizontal form-example">
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>First Name</label>
                <input type="text" class="form-control" name="first_name" value="<?= old('first_name') ?>"> 
                <span class="error"><?= $validation->getError('first_name') ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Last Name</label>
                <input type="text" class="form-control" name="last_name" value="<?= old('last_name') ?>"> 
                <span class="error"><?= $validation->getError('last_name') ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Gender</label>
                <select class="form-control" name="gender"> 
                    <option <?= old('gender') == "M" ? "selected" : "" ?> value="M">Male</option>
                    <option <?= old('gender') == "F" ? "selected" : "" ?> value="F">Female</option>
                </select>
                <span class="error"><?= $validation->getError('gender') ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>DOB</label>
                <input type="text" class="form-control" name="dob" value="<?= old('dob') ?>">
                <span class="error"><?= $validation->getError('dob') ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Email ID</label>
                <input type="text" class="form-control" name="email" value="<?= old('email') ?>"> 
                <span class="error"><?= $validation->getError('email') ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Address</label>
                <input type="text" class="form-control" name="address" value="<?= old('address') ?>"> 
                <span class="error"><?= $validation->getError('address') ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>State/Province</label>
                <input type="text" class="form-control" name="state" value="<?= old('state') ?>"> 
                <span class="error"><?= $validation->getError('state') ?></span>
            </div>
            <?php $countries = get_country_array(); ?>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Country</label>
                <select class="form-control" name="country">
                    <option selected="selected" value="SO">Somalia</option>
                    <?php foreach ($countries as $key => $value): ?>
                        <option <?= old('country') == $key ? "selected" : "" ?> value="<?= $key; ?>">
                            <?= $value; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error"><?= $validation->getError('country') ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Postal Code</label>
                <input type="text" class="form-control" name="postal_code" value="<?= old('postal_code') ?>"> 
                <span class="error"><?= $validation->getError('postal_code') ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Room Number</label>
                <input type="text" class="form-control" name="room_number" value="<?= old('room_number') ?>"> 
                <span class="error"><?= $validation->getError('room_number') ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Phone Number</label>
                <input type="text" class="form-control" name="phone_number" value="<?= old('phone_number') ?>"> 
                <span class="error"><?= $validation->getError('phone_number') ?></span>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Education</label>
                <input type="text" class="form-control" name="provider_education" value="<?= old('provider_education') ?>"> 
                <span class="error"><?= $validation->getError('provider_education') ?></span>
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

<?php endif; ?>

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
