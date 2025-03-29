<div class="container">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="margin-bottom: 30px;">    
            <div class="column_middle_grid1">
                <div class="profile_picture">
                    <a href="javascript:void(0);">
                        <img src="<?= base_url('assets/images/provider.png'); ?>">
                    </a>                 
                    <div class="profile_picture_name">
                        <h2>Doctors</h2>
                    </div>
                    <span>
                        <a class="loader-activate" href="<?= site_url('providers/add'); ?>">
                            <img src="<?= base_url('assets/theme/admin/images/follow_user.png'); ?>"> 
                        </a>
                    </span>
                </div>
                <div class="articles_list">
                    <ul>
                        <li>
                            <a class="red loader-activate" href="<?= site_url('providers/all'); ?>">
                                <i class="fa fa-search"></i> View
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="purple"> 
                                <label class="label label-success">
                                    <?= get_providers_count(); ?>    
                                </label>
                            </a>
                        </li>
                        <li>
                            <a class="yellow loader-activate" href="<?= site_url('providers/add'); ?>">
                                <i class="fa fa-plus"></i> Add
                            </a>
                        </li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="margin-bottom: 30px;">
            <div class="weather">
                <h3><i class="fa fa-calendar"></i> &nbsp; Appointments</h3>
                <div class="today_temp">
                    <div class="temp">
                        <figure>
                            Awaiting
                            <span><?= get_appointments_count(1); ?></span>
                        </figure>
                    </div>
                </div>
                <div class="temp_list">
                    <ul>
                        <li>
                            <a href="javascript:void(0);" style="text-decoration: none;">
                                <span class="day_name">
                                    <i class="fa fa-check"></i> &nbsp; &nbsp; Completed
                                </span>
                                <label class="label label-success">
                                    <?= get_appointments_count(3); ?>
                                </label>
                                <div class="clear"></div>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" style="text-decoration: none;">
                                <span class="day_name">
                                    <i class="fa fa-medkit"></i> &nbsp; &nbsp; Active
                                </span>
                                <label class="label label-primary">
                                    <?= get_appointments_count(2); ?>
                                </label>
                                <div class="clear"></div>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" style="text-decoration: none;">
                                <span class="day_name">
                                    <i class="fa fa-times"></i> &nbsp; &nbsp; Canceled
                                </span>
                                <label class="label label-danger">
                                    <?= get_appointments_count(0); ?>
                                </label>
                                <div class="clear"></div>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" style="text-decoration: none;">
                                <span class="day_name">
                                    <i class="fa fa-ambulance"></i> &nbsp; &nbsp; Total
                                </span>
                                <label class="label label-warning">
                                    <?= get_appointments_count(FALSE); ?>
                                </label>
                                <div class="clear"></div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="margin-bottom: 30px;">    
            <div class="column_middle_grid1">
                <div class="profile_picture">
                    <a href="javascript:void(0);">
                        <img src="<?= base_url('assets/images/consumer.png'); ?>">
                    </a>                 
                    <div class="profile_picture_name">
                        <h2>Patients</h2>
                    </div>
                    <span>
                        <a class="loader-activate" href="<?= site_url('consumers/add'); ?>">
                            <img src="<?= base_url('assets/theme/admin/images/follow_user.png'); ?>"> 
                        </a>
                    </span>
                </div>
                <div class="articles_list">
                    <ul>
                        <li>
                            <a class="red loader-activate" href="<?= site_url('consumers/all'); ?>"> 
                                <i class="fa fa-search"></i> View
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="purple"> 
                                <label class="label label-success">
                                    <?= get_consumers_count(); ?>    
                                </label>
                            </a>
                        </li>
                        <li>
                            <a class="yellow loader-activate" href="<?= site_url('consumers/add'); ?>"> 
                                <i class="fa fa-plus"></i> Add
                            </a>
                        </li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
    </div>       
</div>
