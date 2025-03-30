<div class="form">
    <form onsubmit="$('#destination_services option').prop('selected', true);" action="" class="cmxform form-horizontal form-example" method="post">
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Doctor</label>
                <select class="form-control" name="provider_id">
                    <option value="">Please Select Doctor</option>
                    <?php if($providers): ?>
                        <?php foreach($providers as $provider): ?>
                            <?php if($row->provider_id == $provider->id): ?>
                            <option <?php if($row->provider_id == $provider->id){ echo 'selected'; } ?> value="<?php echo $provider->id; ?>">
                                <?php echo $provider->first_name.' '.$provider->last_name; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="error"><?php echo $validation->getError("provider_id"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Scheduling From</label>
                <input type="text" class="form-control" name="s_date" value="<?php echo $row->s_date; ?>"> 
                <span class="error"><?php echo $validation->getError("s_date"); ?></span>
            </div>
        </div>  
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Scheduling To</label>
                <input type="text" class="form-control" name="e_date" value="<?php echo $row->e_date; ?>"> 
                <span class="error"><?php echo $validation->getError("e_date"); ?></span>
            </div>
        </div>    
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Start Time</label>
                <input type="text" class="form-control" name="s_start" value="<?php echo $row->s_start; ?>"> 
                <span class="error"><?php echo $validation->getError("s_start"); ?></span>
            </div>
        </div>            
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>End Time</label>
                <input type="text" class="form-control" name="s_end" value="<?php echo $row->s_end; ?>"> 
                <span class="error"><?php echo $validation->getError("s_end"); ?></span>
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
    $("input[name='s_date'], input[name='e_date']").datepicker({
        dateFormat: "d MM yy",
        changeMonth: true,
        changeYear: true,
        minDate: "0",
    });

    $("input[name='s_start'], input[name='s_end']").timepicki({
        show_meridian: true,
        min_hour_value: 1,
        max_hour_value: 12,
        custom_classes: "fz-class",
        step_size_hours: 1,
        step_size_minutes: 30,
        increase_direction: "up",
        disable_keyboard_mobile: true,
    });
});
</script>