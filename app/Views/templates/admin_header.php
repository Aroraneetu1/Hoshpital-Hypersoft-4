<?php 

$permissions = unserialize(get_session_data('permissions'));

/*echo "<pre>";
print_r($permissions);
echo "</pre>";*/


$menu_array = array(
    'admin' => array(
        /*'change_password' => array(
            'name' => 'Change Password',
            'url' => get_site_url("admin/change_password"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),*/

        'user_list' => array(
            'name' => 'Users List',
            'url' => get_site_url("admin/user_list"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),

        'user_staff' => array(
            'name' => 'Add New User',
            'url' => get_site_url("admin/user_staff"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),

        'edit_user' => array(
            'name' => 'Edit User',
            'url' => get_site_url("admin/edit_user"),
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
    ),
    'providers' => array(
        'all' => array(
            'name' => 'Doctors List',
            'url' => get_site_url("providers/all"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'add' => array(
            'name' => 'Add New Doctor',
            'url' => get_site_url("providers/add"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'edit' => array(
            'name' => 'Edit Doctor',
            'url' => '',
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
        'all_schedules' => array(
            'name' => 'Schedules',
            'url' => get_site_url("providers/all_schedules"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'add_schedule' => array(
            'name' => 'Add Schedule',
            'url' => get_site_url("providers/add_schedule"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'edit_schedule' => array(
            'name' => 'Edit Schedule',
            'url' => get_site_url("providers/edit_schedule"),
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
    ),
    'consumers' => array(
        'all' => array(
            'name' => 'Patients List',
            'url' => get_site_url("consumers/all"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'add' => array(
            'name' => 'Add New Patient',
            'url' => get_site_url("consumers/add"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'edit' => array(
            'name' => 'Edit Patient',
            'url' => '',
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
        'history' => array(
            'name' => 'Patient History',
            'url' => '',
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
        'pay' => array(
            'name' => 'Payments',
            'url' => '',
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
    ),
    'appointments' => array(
        'all' => array(
            'name' => 'Appointments List',
            'url' => get_site_url("appointments/all"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'add' => array(
            'name' => 'Add New Appointment',
            'url' => get_site_url("appointments/add"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'edit' => array(
            'name' => 'Edit Appointment',
            'url' => '',
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
        'visit' => array(
            'name' => 'Visit',
            'url' => '',
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
        /*'laboratory' => array(
            'name' => 'Laboratory',
            'url' => get_site_url("appointments/laboratory/".$this->uri->segment(3)),
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),*/
        'display_token' => array(
            'name' => 'Display Token Number',
            'url' => get_site_url("appointments/display_token"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),

        'allinpatient' => array(
            'name' => 'In-Patient',
            'url' => get_site_url("appointments/allinpatient"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),

        'alloperation' => array(
            'name' => 'Operation',
            'url' => get_site_url("appointments/alloperation"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
    ),

    'expenses' => array(
        'all' => array(
            'name' => 'Expenses List',
            'url' => get_site_url("expenses/all"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'add' => array(
            'name' => 'Add New Expense',
            'url' => get_site_url("expenses/add"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'edit' => array(
            'name' => 'Edit Expense',
            'url' => '',
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
        'view' => array(
            'name' => 'View Expense',
            'url' => '',
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
        'expensepayment' => array(
            'name' => 'Expense Payment',
            'url' => get_site_url("expenses/expensepayment"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
    ),

    'finance' => array(
        
        'add' => array(
            'name' => 'Add New Bank',
            'url' => get_site_url("finance/add"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'edit' => array(
            'name' => 'Edit Bank',
            'url' => '',
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
        'all' => array(
            'name' => 'Manage Banks',
            'url' => get_site_url("finance/all"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'transactions' => array(
            'name' => 'Bank Transactions',
            'url' => get_site_url("finance/transactions"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'income_statement' => array(
            'name' => 'Income Statement',
            'url' => get_site_url("finance/income_statement"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
    ),

    'supplier' => array(
        'all' => array(
            'name' => 'Supplier List',
            'url' => get_site_url("supplier/all"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'add' => array(
            'name' => 'Add New Supplier',
            'url' => get_site_url("supplier/add"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'edit' => array(
            'name' => 'Edit Supplier',
            'url' => '',
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
    ),

    'service_types' => array(
        'all' => array(
            'name' => 'Service Types List',
            'url' => get_site_url("service_types/all"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'add' => array(
            'name' => 'Add New Service Type',
            'url' => get_site_url("service_types/add"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'edit' => array(
            'name' => 'Edit Service Type',
            'url' => '',
            'visible' => FALSE,
            'class' => 'loader-activate',
        ),
    ),
    'settings' => array(
        'report' => array(
            'name' => 'Daily Report',
            'url' => get_site_url("admin/report"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'sales_report' => array(
            'name' => 'Sales Report',
            'url' => get_site_url("admin/sales_report"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        /*'doctor_report' => array(
            'name' => 'Doctor Report',
            'url' => get_site_url("admin/doctor_report"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'management_report' => array(
            'name' => 'Management Report',
            'url' => get_site_url("admin/management_report"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'service_report' => array(
            'name' => 'Service Report',
            'url' => get_site_url("admin/service_report"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'payment_report' => array(
            'name' => 'Payment Report',
            'url' => get_site_url("admin/payment_report"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),
        'sms_payment_report' => array(
            'name' => 'SMS Payment Report',
            'url' => get_site_url("admin/sms_payment_report"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),*/

        'expense_report' => array(
            'name' => 'Expense Report',
            'url' => get_site_url("admin/expense_report"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),

        
        'debit_payment' => array(
            'name' => 'Debit Payments',
            'url' => get_site_url("admin/debit_payment"),
            'visible' => TRUE,
            'class' => 'loader-activate',
        ),

        

    ),

    
);
?> 

<!DOCTYPE HTML>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
   <!-- jQuery -->
<script type="text/javascript" src="<?= base_url('assets/theme/admin/js/jquery.js'); ?>"></script>

<!-- Bootstrap -->
<link href="<?= base_url('assets/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
<script type="text/javascript" src="<?= base_url('assets/bootstrap/bootstrap.min.js'); ?>"></script>

<!-- jQuery UI -->
<link href="<?= base_url('assets/js-plugins/jquery-ui/style.css'); ?>" rel="stylesheet">
<script type="text/javascript" src="<?= base_url('assets/js-plugins/jquery-ui/script.js'); ?>"></script>

<!-- Add/Remove Dual Select Box JS Plugin -->
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/js-plugins/add-remove-dual-box/listswap.css'); ?>" />
<script src="<?= base_url('assets/js-plugins/add-remove-dual-box/jquery.listswap.js'); ?>"></script>

<!-- PrintJS -->
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/js-plugins/print-js/print.min.css'); ?>" />
<script src="<?= base_url('assets/js-plugins/print-js/print.min.js'); ?>"></script>

<!-- Data Table -->
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/js-plugins/datatables/datatables.min.css'); ?>"/>
<script type="text/javascript" src="<?= base_url('assets/js-plugins/datatables/datatables.min.js'); ?>"></script>

<!-- Time Picker -->
<link href="<?= base_url('assets/js-plugins/time-picker/style.css'); ?>" rel="stylesheet">
<script type="text/javascript" src="<?= base_url('assets/js-plugins/time-picker/script.js'); ?>"></script>

<!-- Select2 -->
<link href="<?= base_url('assets/js-plugins/select2/select2.min.css'); ?>" rel="stylesheet" />
<script src="<?= base_url('assets/js-plugins/select2/select2.min.js'); ?>"></script>

<!-- FontAwesome -->
<link href="<?= base_url('assets/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Theme Styles -->
<link href="<?= base_url('assets/theme/admin/css/style.css'); ?>" rel="stylesheet" type="text/css" media="all"/>
<link href="<?= base_url('assets/theme/admin/css/nav.css'); ?>" rel="stylesheet" type="text/css" media="all"/>
<link href="https://fonts.googleapis.com/css?family=Carrois+Gothic+SC" rel="stylesheet" type="text/css">

<!-- Additional Scripts -->
<script type="text/javascript" src="<?= base_url('assets/theme/admin/js/login.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/admin/js/Chart.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/admin/js/jquery.easing.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/admin/js/jquery.ulslide.js'); ?>"></script>

<!-- Calendar -->
<link rel="stylesheet" href="<?= base_url('assets/theme/admin/css/clndr.css'); ?>" type="text/css" />
<script src="<?= base_url('assets/theme/admin/js/clndr.js'); ?>"></script>
<script src="<?= base_url('assets/theme/admin/js/underscore-min.js'); ?>"></script>
<script src="<?= base_url('assets/theme/admin/js/moment-2.2.1.js'); ?>"></script>
<script src="<?= base_url('assets/theme/admin/js/site.js'); ?>"></script>

<script type="text/javascript" src="<?= base_url('assets/theme/admin/js/responsive-nav.js'); ?>"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DateTime Picker Master -->
<link media="all" type="text/css" rel="stylesheet" href="<?= base_url('assets/datetimepicker-master/build/jquery.datetimepicker.min.css'); ?>">
<script src="<?= base_url('assets/datetimepicker-master/build/jquery.datetimepicker.full.js'); ?>"></script>
    

<style type="text/css">
.panel{
    border-radius: 0px;
}    
.form-control{
    border-radius: 0px;

}
.btn{
    border-radius: 0px;
}
input.form-control, select.form-control, textarea.form-control{
    background-color: rgba(155, 155, 155, 0.1);
}
</style>

</head>
<body>
    <div id="site-loader" style="position: fixed; z-index: 99999999; left: 0; right: 0; top: 0; bottom: 0; background-color: rgba(255,255,255,0.7); text-align: center;">
    <img src="<?= base_url('assets/images/loading-bar.gif'); ?>" alt="Loading...">
    </div>


    <div class="wrap">
        <div class="header hide-on-print">
            <div class="header_top">
                <div class="menu">
                    <a class="toggleMenu" href="#">
    <img src="<?= base_url('assets/theme/admin/images/nav.png'); ?>" alt="Navigation">
</a>

                    <ul class="nav">
                        <li class="active">
                            <a class="loader-activate" href="<?php echo get_site_url('admin'); ?>">
                                <i class="fa fa-home"></i>
                                Home
                            </a>
                        </li>
                        <li class="active">
                            <a class="loader-activate" href="<?php echo get_site_url('providers/all'); ?>">
                                <i class="fa fa-stethoscope"></i>
                                Doctors
                            </a>
                        </li>
                        <li class="active">
                            <a class="loader-activate" href="<?php echo get_site_url('consumers/all'); ?>">
                                <i class="fa fa-user"></i>
                                Patients
                            </a>
                        </li>
                        <li class="active">
                            <a class="loader-activate" href="<?php echo get_site_url('appointments/all'); ?>">
                                <i class="fa fa-clock-o"></i>
                                Appointments
                            </a>
                        </li>



                        <?php if(isset($permissions['expense']) && $permissions['expense'] == 1): ?>
    <li class="active">
        <a class="loader-activate" href="<?= base_url('expenses/all'); ?>">
            <i class="fa fa-money"></i>
            Expenses
        </a>
    </li>
<?php endif; ?>


<?php if(isset($permissions['finance']) && $permissions['finance'] == 1): ?>
    <li class="active">
        <a class="loader-activate" href="<?= base_url('finance/all'); ?>">
            <i class="fa fa-bank"></i>
            Finance
        </a>
    </li>
<?php endif; ?>

<?php if(isset($permissions['reports']) && $permissions['reports'] == 1): ?>
    <li class="active">
        <a class="loader-activate" href="<?= base_url('admin/report'); ?>">
            <i class="fa fa-file"></i>
            Reports
        </a>
    </li>
<?php endif; ?>

<?php if(isset($permissions['settings']) && $permissions['settings'] == 1): ?>
    <li class="active">
        <a class="loader-activate" href="<?= get_site_url('admin/settings'); ?>">
            <i class="fa fa-gear"></i>
            Settings
        </a>
    </li>
<?php endif; ?>

                        <!-- 
                        mail.png
                        <span class="messages">5</span> 
                        -->
                        <div class="clear"></div>
                    </ul>
                </div>
                <div class="profile_details">
                    <div id="loginContainer">
                        <a id="loginButton" class="">
                            <span>Me</span>
                            <!-- <span><?php/* echo ucfirst(get_session_data('first_name'));*/?></span> -->
                        </a>   
                        <div id="loginBox">
                            <form id="loginForm">
                                <fieldset id="body">
                                    <div class="user-info">
                                        <!-- <h4>Hello,<a href="#"> Username</a></h4> -->
                                        <ul>
                                            <!-- <li>
                                                <a class="loader-activate" href="<?php echo get_site_url(); ?>admin/user_list">
                                                    Users
                                                </a>
                                            </li>-->
                                            <li>
                                                <a class="" href="javascript:;" onclick="changepass()">
                                                    Change Password
                                                </a>
                                            </li> 
                                            <li>
                                                <a class="loader-activate" href="<?php echo get_site_url(); ?>admin/logout">
                                                    Logout
                                                </a>
                                            </li>
                                            <div class="clear"></div>
                                        </ul>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                    <div class="profile_img hidden">   
                    <a href="#"><img src="<?= base_url('assets/theme/admin/images/profile_img40x40.jpg'); ?>" alt="" /></a>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>


    <div class="wrap">
        <div class="row">
            <div class='col-lg-12 col-md-12 col-sm-12'>
                <section class="panel">
                    <header class="panel-heading" style="background-color: #eee;">
                        <?php if(!isset($active)){ $active = ''; } ?>
                        <?php if(isset($menu)): ?>
                            <?php if(isset($menu_array[$menu]) && is_array($menu_array[$menu]) && count($menu_array[$menu]) > 0): ?>
                                <?php foreach($menu_array[$menu] as $key => $value): ?>
                                    <?php if($value['visible'] == TRUE || $key == $active): ?>
                                        <a href="<?php echo $value['url']; ?>" class="btn <?php echo $value['class']; ?> <?php if($key == $active){ echo 'btn-success'; }else{ echo 'btn-default'; } ?>">
                                            <?php echo $value['name']; ?>         
                                        </a>
                                    <?php endif; ?>    
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </header>
                    <div class="panel-body">
                        <?php alert(); ?>


<style type="text/css">
.panel-heading a{
    margin-left: -4px !important;
}    
</style>