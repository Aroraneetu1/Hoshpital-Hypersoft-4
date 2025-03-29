<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <link rel="shortcut icon" href="img/favicon.png">

    <title>AMZ | Forget Password</title>

    <!-- Bootstrap core CSS -->
    <link href="<?= base_url('assets/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/bootstrap/bootstrap-reset.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/bootstrap/bootstrap-theme.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/font-awesome/font-awesome.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/material-dashboard.css') ?>" rel="stylesheet">

    <script type="text/javascript" src="<?= base_url('assets/theme/admin/js/jquery.js') ?>"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head>

  <body class="login-body">

    <div class="container">
      <?php 
      // echo form_open(cms_current_url(), array('class'=>'registration'))
       ?>
      <?php
      //  alert();
       ?>
        <h2 class="registration-heading">Forget Password</h2>
        <div class="login-wrap">
            <input type="email" name="email" class="form-control" placeholder="Email" autofocus required>
            <?php
            //  echo form_error('email')
            ?>
            <br>
            <button class="btn btn-lg sign-btn btn-block" type="submit">Submit</button>


        </div>

      </form>

    </div>


  </body>
</html>
