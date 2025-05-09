<?php
 $validation = \Config\Services::validation();
?>
<div class="form">
    <form action="" class="cmxform form-horizontal form-example" method="post">
        <div class="col-lg-4 col-md-6 col-sm-6 col-xm-12">
            <div class="form-group">
                <label>Service Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo set_value("name"); ?>"> 
                <span class="error"><?php echo  $validation->getError("name"); ?></span>
            </div>
            <div class="form-group">
                <label>Service Duration (In Minutes)</label>
                <input type="text" class="form-control" name="duration" value="<?php echo set_value("duration"); ?>"> 
                <span class="error"><?php echo  $validation->getError("duration"); ?></span>
            </div>
            <div class="form-group">
                <label>Service Amount</label>
                <input type="text" class="form-control" name="amount" value="<?php echo set_value("amount"); ?>"> 
                <span class="error"><?php echo  $validation->getError("amount"); ?></span>
            </div>
            <div class="form-group">
                <label>Service Description</label>
                <textarea rows="4" class="form-control" name="description"><?php echo set_value("description"); ?></textarea>
                <span class="error"><?php echo  $validation->getError("description"); ?></span>
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                    Add New Service Type
                </button>
            </div>
        </div>
    </form>
</div>