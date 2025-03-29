<div class="form">
    <form action="" class="cmxform form-horizontal form-example" method="post">
        <div class="col-lg-4 col-md-6 col-sm-6 col-xm-12">
            <div class="form-group">
                <label>Old Password</label>
                <input type="password" class="form-control" name="opwd" value="<?php echo set_value("opwd"); ?>"> 
                <span class="error"><?php echo form_error("opwd"); ?></span>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" class="form-control" name="npwd" value="<?php echo set_value("npwd"); ?>"> 
                <span class="error"><?php echo form_error("npwd"); ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" class="form-control" name="cpwd" value="<?php echo set_value("cpwd"); ?>"> 
                <span class="error"><?php echo form_error("cpwd"); ?></span>
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                    Change Password
                </button>
            </div>
        </div>
    </form>
</div>