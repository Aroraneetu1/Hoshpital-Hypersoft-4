<?php 
$provider_services = json_decode($row->provider_services, true);
if (!is_array($provider_services)) {
    $provider_services = [];
}
?>

<div class="form">
    <form onsubmit="$('#destination_services option').prop('selected', true);" action="" method="post" class="cmxform form-horizontal form-example">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <div class="col-lg-4">
                <label>Given Name</label>
                <input type="text" class="form-control" name="first_name" value="<?= old('first_name', $row->first_name) ?>"> 
                <span class="error"><?= $validation->getError('first_name') ?? '' ?></span>
            </div>
            <div class="col-lg-4">
                <label>Family Name</label>
                <input type="text" class="form-control" name="last_name" value="<?= old('last_name', $row->last_name) ?>"> 
                <span class="error"><?= $validation->getError('last_name') ?? '' ?></span>
            </div>
            <div class="col-lg-4">
                <label>Gender</label>
                <select class="form-control" name="gender"> 
                    <option value="M" <?= old('gender', $row->gender) == "M" ? 'selected' : '' ?>>Male</option>
                    <option value="F" <?= old('gender', $row->gender) == "F" ? 'selected' : '' ?>>Female</option>
                </select>
                <span class="error"><?= $validation->getError('gender') ?? '' ?></span>
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-4">
                <label>DOB</label>
                <input type="text" class="form-control" name="dob" value="<?= old('dob', $row->dob) ?>">
                <span class="error"><?= $validation->getError('dob') ?? '' ?></span>
            </div>
            <div class="col-lg-4">
                <label>Email ID</label>
                <input type="email" class="form-control" name="email" value="<?= old('email', $row->email) ?>"> 
                <span class="error"><?= $validation->getError('email') ?? '' ?></span>
            </div>
            <div class="col-lg-4">
                <label>Address</label>
                <input type="text" class="form-control" name="address" value="<?= old('address', $row->address) ?>"> 
                <span class="error"><?= $validation->getError('address') ?? '' ?></span>
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-4">
                <label>State/Province</label>
                <input type="text" class="form-control" name="state" value="<?= old('state', $row->state) ?>"> 
                <span class="error"><?= $validation->getError('state') ?? '' ?></span>
            </div>
            <?php $countries = get_country_array(); ?>
            <div class="col-lg-4">
                <label>Country</label>
                <select class="form-control" name="country">
                    <?php foreach ($countries as $key => $value): ?>
                        <option value="<?= $key ?>" <?= old('country', $row->country) == $key ? 'selected' : '' ?>>
                            <?= $value ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error"><?= $validation->getError('country') ?? '' ?></span>
            </div>
            <div class="col-lg-4">
                <label>Postal Code</label>
                <input type="text" class="form-control" name="postal_code" value="<?= old('postal_code', $row->postal_code) ?>"> 
                <span class="error"><?= $validation->getError('postal_code') ?? '' ?></span>
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-4">
                <label>Room Number</label>
                <input type="text" class="form-control" name="room_number" value="<?= old('room_number', $row->room_number) ?>"> 
                <span class="error"><?= $validation->getError('room_number') ?? '' ?></span>
            </div>
            <div class="col-lg-4">
                <label>Phone Number</label>
                <input type="text" class="form-control" name="phone_number" value="<?= old('phone_number', $row->phone_number) ?>"> 
                <span class="error"><?= $validation->getError('phone_number') ?? '' ?></span>
            </div>
            <div class="col-lg-4">
                <label>Education</label>
                <input type="text" class="form-control" name="provider_education" value="<?= old('provider_education', $row->provider_education) ?>"> 
                <span class="error"><?= $validation->getError('provider_education') ?? '' ?></span>
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-12">
                <label>Services</label>
                <select id="source_services" data-search="All Services">
                    <?php foreach ($services as $service): ?>
                        <?php if (!in_array($service->id, $provider_services)): ?>
                            <option value="<?= $service->id ?>"><?= $service->name ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>

                <select name="services[]" multiple="multiple" id="destination_services" data-search="Selected Services">
                    <?php foreach ($services as $service): ?>
                        <?php if (in_array($service->id, $provider_services)): ?>
                            <option value="<?= $service->id ?>"><?= $service->name ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <span class="error"><?= $validation->getError('services') ?? '' ?></span>
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary form-control">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#source_services, #destination_services').listswap({
        truncate: true,
        is_scroll: true,
        height: 150,
        label_add: 'Add',
        label_remove: 'Remove'
    });

    $("input[name='dob']").datepicker({
        dateFormat: "d MM yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "1950:c"
    });
});
</script>
