<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keyword" content="">
    <!-- <link rel="icon" href="<?php echo base_url() ?>assets/fav.png"> -->
    <title><?php echo PROJECT_NAME ?></title>
    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url() ?>assets/theme/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/theme/admin/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="<?php echo base_url() ?>assets/theme/admin/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url() ?>assets/theme/admin/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/theme/admin/css/style-responsive.css" rel="stylesheet" />
    <style type="text/css">
      .error{
        height: 15px;
        display: block;
      }
      .error p{
        color:#FF0000;
        font-size: 12px;
        text-align: left;
      }
      .form-control{
        margin-bottom: 0px !important; 
        color: #666;
      }
      .form-group{
        margin-bottom: 20px;
      }
      .alert{
        margin-top:30px; 
        margin-bottom:-80px;
      }
      .registration-heading{
        background-color: #F67A6E !important;
      }
      .login-body{
        background-color: #ccc;
      }
      .btn-success{
        color: #fff !important;
      }
    </style> 
</head>
<body class="login-body">
    <div class="container">
        <div class='col-md-4 col-md-offset-4'>
          <?php alert(); ?>
          <?php echo form_open(cms_current_url(), array('class'=>'registration')) ?>
          <h2 class="registration-heading">
            <b>RESET PASSWORD</b>
        </h2>
          <div class="login-wrap">
            <div class="form-group">
              <input type='password' name="password" placeholder="NEW PASSWORD" class="form-control" required>
              <span style="color:#FF0000;"><?php echo form_error('email')?></span>
            </div>

            <div class='form-group'>
                <button class="btn btn-lg btn-block btn-danger" type="submit">
                  RESET PASSWORD
              </button>
              
            </div>
          </div>
          <?php echo form_close(); ?>
      </div>    
    </div>
</body>
</html>

