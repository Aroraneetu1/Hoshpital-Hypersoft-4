    
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">

            <a href="<?php echo get_site_url('appointments/alloperation'); ?>" class="btn loader-activate <?php if($this->uri->segment(2) == 'alloperation'){echo 'btn-success';}else{echo 'btn-default';} ?>">All Operation</a>

            <a href="<?php echo get_site_url('appointments/addoperation'); ?>" class="btn loader-activate <?php if($this->uri->segment(2) == 'addoperation'){echo 'btn-success';}else{echo 'btn-default';} ?>">Add New Operation</a>

            <?php if($this->uri->segment(2) == 'viewoperation'){ ?>

            	<a href="<?php echo get_site_url('appointments/viewoperation/'.$this->uri->segment(3)); ?>" class="btn loader-activate <?php if($this->uri->segment(2) == 'viewoperation'){echo 'btn-success';}else{echo 'btn-default';} ?>">View Operation</a>

            <?php } ?>

            <?php if($this->uri->segment(2) == 'payoperation'){ ?>

                <a href="<?php echo get_site_url('appointments/payoperation/'.$this->uri->segment(3)); ?>" class="btn loader-activate <?php if($this->uri->segment(2) == 'payoperation'){echo 'btn-success';}else{echo 'btn-default';} ?>">Pay Operation</a>

            <?php } ?>

            <?php if($this->uri->segment(2) == 'editoperation'){ ?>

                <a href="<?php echo get_site_url('appointments/editoperation/'.$this->uri->segment(3)); ?>" class="btn loader-activate <?php if($this->uri->segment(2) == 'editoperation'){echo 'btn-success';}else{echo 'btn-default';} ?>">Edit Operation</a>

            <?php } ?>

            <?php if($this->uri->segment(2) == 'discharge'){ ?>

                <a href="<?php echo get_site_url('appointments/discharge/'.$this->uri->segment(3)); ?>" class="btn loader-activate <?php if($this->uri->segment(2) == 'discharge'){echo 'btn-success';}else{echo 'btn-default';} ?>">Discharge</a>

            <?php } ?>
        </div>
    </div>