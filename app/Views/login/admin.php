<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <link href="<?= base_url('assets/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/bootstrap/bootstrap-reset.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/bootstrap/bootstrap-theme.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/font-awesome/font-awesome.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/material-dashboard.css') ?>" rel="stylesheet">

    <script type="text/javascript" src="<?= base_url('assets/theme/admin/js/jquery.js') ?>"></script>

    <style type="text/css">
        span.error {
            background-color: #fff;
            color: red;
        }

        .card-header, .btn-primary {
            background: #BD2138 !important;
        }
    </style>

</head>
<body>

<div id="site-loader"
     style="position: fixed; z-index: 99999999; left: 0; right: 0; top: 0; bottom: 0; background-color: rgba(255, 255, 255, 0.7); text-align: center;">
    <img src="<?= base_url('assets/images/loading-bar.gif') ?>" alt="Loading...">
</div>

<div class="container">
<!-- <div class="row">
    <div class='col-lg-4 offset-lg-4 col-md-4 offset-md-4 col-sm-6 offset-sm-3'>
        <div class="weather">
            <div class="column_right_grid sign-in" style="padding-top: 0px;">
                <h3>Login</h3>
                <?php alert(); ?>
                <div class="sign_in">
                    <form action="<?= base_url('login') ?>" method="post">
                        <?= csrf_field(); ?> 
                        <span>
                            <i><img src="<?= base_url('assets/theme/admin/images/user.png'); ?>" alt=""></i>
                            <input name="username" type="text" placeholder="Username" value="<?= old('username'); ?>" autofocus>
                        </span>
                        <?php if (isset($validation) && $validation->hasError('username')) : ?>
                            <div class="text-danger"><?= $validation->getError('username'); ?></div>
                        <?php endif; ?>

                        <span>
                            <i><img src="<?= base_url('assets/theme/admin/images/lock.png'); ?>" alt=""></i>
                            <input name="password" type="password" placeholder="Password" value="<?= old('password'); ?>">
                        </span>
                        <?php if (isset($validation) && $validation->hasError('password')) : ?>
                            <div class="text-danger"><?= $validation->getError('password'); ?></div>
                        <?php endif; ?>

                        <input type="submit" class="my-button" value="Login">
                    </form>    
                </div>
                <div style="color: #fff; padding-top: 30px; padding-bottom: 10px; display: block;">
                    © <?= date('Y'); ?> | <?= get_site_name(); ?>
                </div>
            </div>
        </div>
    </div>
</div> -->
    <div class="row">
        <div class="col-md-4 col-md-offset-4" style="padding-top: 7%;">
            <div class="card">
                <div class="card-header" data-background-color="purple">
                    <h4 class="title text-center">User Login</h4>
                </div>
                <div class="card-content">
                    <form action="<?= get_site_url('login/admin') ?>" method="post">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input name="username" class="form-control" type="text" placeholder="Username"
                                           value="<?= set_value('username') ?>" autofocus>
                                    <p class="error"><?= validation_show_error('username') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Password</label>
                                    <input name="password" class="form-control" type="password" placeholder="Password"
                                           value="<?= set_value('password') ?>">
                                    <p class="error"><?= validation_show_error('password') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">

                            </div>
                            <div class="col-xs-6 text-right">
                                <button type="submit" class="btn btn-primary">
                                    Login Now
                                </button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).on("submit", "form", function () {
        $("#site-loader").show();
    });

    $(window).on('load', function () {
        $("#site-loader").hide();
    });
</script>

</body>
</html>