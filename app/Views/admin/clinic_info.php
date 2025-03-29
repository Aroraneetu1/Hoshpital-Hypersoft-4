<?php 
/*echo '<pre>';
print_r($info);
echo '</pre>';*/
?>

<section id="unseen">
    <div class="form">
        <form action="" class="cmxform form-horizontal form-example" method="post" enctype="multipart/form-data">
            <div class="col-lg-4 col-md-6 col-sm-6 col-xm-12">
                <div class="form-group">
                    <label>Hospital Name</label>
                    <input type="text" class="form-control" name="name" value="<?php echo $info->name; ?>"> 
                    <span class="error"><?php echo form_error("name"); ?></span>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" class="form-control" name="address" value="<?php echo $info->address; ?>"> 
                    <span class="error"><?php echo form_error("address"); ?></span>
                </div>
                <div class="form-group">
                    <label>Contact</label>
                    <input type="text" class="form-control" name="contact" value="<?php echo $info->contact; ?>"> 
                    <span class="error"><?php echo form_error("contact"); ?></span>
                </div>

                <div class="form-group">
                    <label>Upload Logo</label>
                    <input type="file" class="form-control" name="logo" > 
                    <span class="error"><?php echo form_error("logo"); ?></span>
                </div>

                <div class="form-group">
                    <label> 
                        <input type="hidden" name="cron_job" value="0">  
                        <input type="checkbox" name="cron_job" value="1" <?php if($info->cron_job == 1){echo 'checked';} ?>>  Run cronjob automatically</label>
                     
                </div>
                
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                        Save Changes
                    </button>
                </div>
            </div>

            <div class="col-md-1"></div>
            <div class="col-md-4">

                <div class="form-group">
                    <img src="<?php echo str_replace('index.php/', '', get_site_url().''.$info->logo)?>" alt="Hospital Logo" style="width: 150px;">
                </div>

            </div>
        </form>
    </div>

</section>