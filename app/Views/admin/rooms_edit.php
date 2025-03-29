<section id="unseen">

    <?php include('rooms_div.php'); ?>

    <div class="form">
        <form action="" class="cmxform form-horizontal form-example" method="post">

            <div class="row">
                <div class="col-md-4">
                
                    <label>Room Name</label>
                    <input type="text" class="form-control" name="room_name" value="<?php echo $row->room_name; ?>"> 
                    <span class="error"><?php echo form_error("room_name"); ?></span>
                </div>

                <div class="col-md-4">
                    <label>Room Number</label>
                    <input type="text" class="form-control" name="room_number" value="<?php echo $row->room_number; ?>"> 
                    <span class="error"><?php echo form_error("room_number"); ?></span>
                </div>
                <div class="col-md-4">
                    <label>Category</label>
                    <select class="form-control" name="room_category"> 
                        <option value="">Select one</option>
                        <?php if(!empty($roomc)){ foreach ($roomc as $key => $value) { ?>
                            <option value="<?php echo $value->id;?>" <?php if($row->room_category == $value->id){echo 'selected';}?>><?php echo $value->name;?></option>
                        <?php } } ?>
                    </select>
                    <span class="error"><?php echo form_error("room_category"); ?></span>
                </div>
                
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4">
                    <label>Description</label>
                    <input type="text" class="form-control" name="description" value="<?php echo $row->description; ?>"> 
                    <span class="error"><?php echo form_error("description"); ?></span>
                </div>

                <div class="col-md-4">
                    <label>Room Rate</label>
                    <input type="text" class="form-control" name="room_rate" value="<?php echo $row->room_rate; ?>"> 
                    <span class="error"><?php echo form_error("room_rate"); ?></span>
                </div>
                
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>


</section>