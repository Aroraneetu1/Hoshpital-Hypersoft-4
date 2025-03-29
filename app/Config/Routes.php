<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('login/admin', 'LoginController::admin');
$routes->post('login/admin', 'LoginController::admin');
$routes->match(['get', 'post'], 'login/forgotpassword', 'LoginController::forgotpassword');
$routes->get('login/reset_forget_password', 'LoginController::forget_password_response');

$routes->group('admin', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'AdminController::index');  
    $routes->get('settings', 'AdminController::settings');
    $routes->get('profile', 'AdminController::profile');
    $routes->post('profile', 'AdminController::profile');
    $routes->get('user_list', 'AdminController::user_list');
    $routes->get('edit_user/(:num)', 'AdminController::edit_user/$1');
    $routes->post('edit_user/(:num)', 'AdminController::edit_user/$1');
    $routes->get('delete/(:num)', 'AdminController::delete/$1');
    $routes->post('change_timezone', 'AdminController::change_timezone');
    $routes->get('logout', 'AdminController::logout');
    $routes->get('report', 'AdminController::report');
    $routes->get('debit-payment', 'AdminController::debitPayment');
    $routes->get('sales-report', 'AdminController::salesReport');
    $routes->get('expense-report', 'AdminController::expenseReport');
    $routes->get('doctor-report', 'AdminController::doctorReport');
    $routes->get('management-report', 'AdminController::managementReport');
    $routes->get('service-report', 'AdminController::serviceReport');
    $routes->get('payment-report', 'AdminController::paymentReport');
    $routes->get('sms-payment-report', 'AdminController::smsPaymentReport');
    $routes->get('payment_types', 'AdminController::paymentTypes');
    $routes->get('add_payment_types', 'AdminController::addPaymentType');
    $routes->post('add_payment_types', 'AdminController::addPaymentType');
    $routes->get('edit_payment_type/(:num)', 'AdminController::editPaymentType/$1');
    $routes->post('edit_payment_type/(:num)', 'AdminController::editPaymentType/$1');
    $routes->post('delete_payment_type', 'AdminController::deletePaymentType');
    $routes->get('rooms', 'AdminController::rooms');
    $routes->post('delete_room', 'AdminController::deleteRoom');
    $routes->match(['get', 'post'], 'arooms', 'AdminController::arooms');
    $routes->match(['get', 'post'], 'erooms/(:num)', 'AdminController::erooms/$1'); 
    $routes->get('crooms', 'AdminController::crooms'); 
    $routes->match(['get', 'post'], 'croomsadd', 'AdminController::croomsadd');
    $routes->post('delete_room_category', 'AdminController::delete_room_category');
    $routes->get('category', 'AdminController::category');
    $routes->get('new_category', 'AdminController::new_category');
    $routes->post('new_category', 'AdminController::new_category');
    $routes->get('edit_category/(:num)', 'AdminController::edit_category/$1');
    $routes->post('edit_category/(:num)', 'AdminController::edit_category/$1');
    $routes->post('delete_category', 'AdminController::delete_category');
    $routes->get('clinic_info', 'AdminController::clinic_info');  
    $routes->post('clinic_info', 'AdminController::clinic_info');

    $routes->get('products', 'AdminController::products');  
    $routes->get('new_product', 'AdminController::new_product');  
    $routes->post('new_product', 'AdminController::new_product');
    $routes->match(['get', 'post'], 'edit_product/(:num)', 'AdminController::edit_product/$1'); 
    $routes->post('delete_product', 'AdminController::delete_product');
});

$routes->group('appointments', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('all', 'AppointmentController::all');
    $routes->get('allinpatient', 'AppointmentController::allinpatient');
    $routes->match(['get', 'post'], 'addinpatient', 'AppointmentController::addinpatient');
    $routes->match(['get', 'post'], 'payinpatient', 'AppointmentController::pay_inpatient');
    $routes->get('payoperation/(:num)', 'AppointmentController::payoperation/$1');
    $routes->post('pay_inpatient', 'AppointmentController::pay_inpatient');
    $routes->get('payinpatient/(:num)', 'AppointmentController::payinpatient/$1');
    $routes->get('viewinpatient/(:num)', 'AppointmentController::viewInpatient/$1');
    $routes->get('discharge/(:num)', 'AppointmentController::discharge/$1');
    $routes->get('editinpatient/(:num)', 'AppointmentController::editinpatient/$1');
    $routes->post('editinpatient/(:num)', 'AppointmentController::editinpatient/$1');
    $routes->post('get_inpatients_products', 'AppointmentController::get_inpatients_products');
    $routes->post('remove_inpatients_products', 'AppointmentController::remove_inpatients_products');
    $routes->post('delete_inpatient', 'AppointmentController::delete_inpatient');

});
