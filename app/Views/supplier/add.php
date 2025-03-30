<?php
 $validation = \Config\Services::validation();
?>
<div class="form">
    <form action="" class="cmxform form-horizontal form-example" method="post">
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Supplier Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo set_value("name"); ?>"> 
                <span class="error"><?php echo $validation->getError("name"); ?></span>
            </div>
        </div>
        <div class="form-group">
            
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Address</label>
                <input type="text" class="form-control" name="address" value="<?php echo set_value("address"); ?>"> 
                <span class="error"><?php echo $validation->getError("address"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Contact</label>
                <input type="text" class="form-control" name="contact" value="<?php echo set_value("contact"); ?>"> 
                <span class="error"><?php echo $validation->getError("contact"); ?></span>
            </div>
        </div>
        
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>&nbsp;</label>
                <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                    Add New Supplier
                </button>    
            </div>
        </div>
   </form>
</div>