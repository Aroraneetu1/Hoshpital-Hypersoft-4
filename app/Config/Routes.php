<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'LoginController::admin');
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
    $routes->get('debit_payment', 'AdminController::debitPayment');
    $routes->get('sales_report', 'AdminController::salesReport');
    $routes->get('expense_report', 'AdminController::expenseReport');
    $routes->get('doctor_report', 'AdminController::doctorReport');
    $routes->get('management_report', 'AdminController::managementReport');
    $routes->get('service_report', 'AdminController::serviceReport');
    $routes->get('payment_report', 'AdminController::paymentReport');
    $routes->get('sms-payment_report', 'AdminController::smsPaymentReport');
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
    $routes->post('get_operation_products', 'AppointmentController::get_operation_products');
    $routes->post('remove_operation_products', 'AppointmentController::remove_operation_products');
    $routes->post('delete_operation', 'AppointmentController::delete_operation');
    $routes->get('alloperation', 'AppointmentController::alloperation');
    $routes->get('viewoperation/(:num)', 'AppointmentController::viewoperation/$1');
    $routes->get('payoperation/(:num)', 'AppointmentController::payoperation/$1');
    $routes->post('pay_operation', 'AppointmentController::pay_operation');
    $routes->get('addoperation', 'AppointmentController::addoperation');
    $routes->post('addoperation', 'AppointmentController::addoperation');
    $routes->get('editoperation/(:num)', 'AppointmentController::editoperation/$1');
    $routes->post('editoperation/(:num)', 'AppointmentController::editoperation/$1');
    $routes->get('display_token', 'AppointmentController::display_token');
    $routes->get('add', 'AppointmentController::add');
    $routes->post('add', 'AppointmentController::add');
    $routes->get('edit/(:num)', 'AppointmentController::edit/$1');
    $routes->post('edit/(:num)', 'AppointmentController::edit/$1');
    $routes->post('ajax_get_available_providers', 'AppointmentController::ajaxGetAvailableProviders');
    $routes->post('cancel_visit', 'AppointmentController::cancel_visit');
    $routes->get('removelb/(:num)/(:num)', 'AppointmentController::removelb/$1/$2');
    $routes->post('lab_result', 'AppointmentController::lab_result');
    $routes->post('pay_result', 'AppointmentController::pay_result');
    $routes->post('lab_services', 'AppointmentController::lab_services');
    $routes->get('laboratory/(:num)', 'AppointmentController::laboratory/$1');
    $routes->get('pay/(:num)/(:any)', 'AppointmentController::pay/$1/$2');
    $routes->get('pay/(:num)', 'AppointmentController::pay/$1');
    $routes->get('receipt/(:num)', 'AppointmentController::receipt/$1');
    $routes->post('end_visit', 'AppointmentController::end_visit');
    $routes->get('visit/(:num)/(:any)', 'AppointmentController::visit/$1/$2');
    $routes->post('visit/(:num)/(:any)', 'AppointmentController::visit/$1/$2');
    $routes->get('visit/(:num)', 'AppointmentController::visit/$1');
    
});

$routes->group('providers', function ($routes) {
    $routes->get('schedules', 'ProviderController::schedules');
    $routes->get('all/(:num)', 'ProviderController::all/$1');
    $routes->get('all', 'ProviderController::all');
    $routes->match(['get', 'post'], 'add', 'ProviderController::add');
    $routes->get('delete/(:num)', 'ProviderController::delete/$1');
    $routes->get('edit/(:num)', 'ProviderController::edit/$1'); 
    $routes->post('edit/(:num)', 'ProviderController::edit/$1');
    $routes->get('all_schedules', 'ProviderController::all_schedules');
    $routes->match(['get', 'post'], 'add_schedule', 'ProviderController::add_schedule');
    $routes->match(['get', 'post'], 'edit_schedule/(:num)', 'ProviderController::edit_schedule/$1');
    $routes->get('delete_schedule/(:num)', 'ProviderController::delete_schedule/$1');

});

$routes->group('consumers', function ($routes) {
    $routes->get('all', 'ConsumerController::all');
    $routes->get('export_excel', 'ConsumerController::export_excel');
    $routes->get('add', 'ConsumerController::add');
    $routes->post('add', 'ConsumerController::add');
    $routes->get('edit/(:num)', 'ConsumerController::edit/$1');
    $routes->post('edit/(:num)', 'ConsumerController::edit/$1');
    $routes->get('history/(:num)', 'ConsumerController::history/$1');
    $routes->get('pay/(:num)', 'ConsumerController::pay/$1');
    $routes->post('debitpay', 'ConsumerController::debitpay');
    $routes->get('change_status/(:num)/(:num)', 'ConsumerController::change_status/$1/$2');
});

$routes->group('expenses', function ($routes) {
    $routes->get('all', 'ExpenseController::all');
    $routes->get('expensepayment', 'ExpenseController::expensePayment');
    $routes->post('pay_now', 'ExpenseController::payNow');
    $routes->get('add', 'ExpenseController::add');
    $routes->post('add', 'ExpenseController::add');
    $routes->post('cancel', 'ExpenseController::cancel');
    $routes->match(['get', 'post'], 'edit/(:num)', 'ExpenseController::edit/$1');
    $routes->post('remove_expense_products', 'ExpenseController::remove_expense_products');
    $routes->post('get_expense_peoducts', 'ExpenseController::get_expense_products');
    $routes->get('view/(:num)', 'ExpenseController::view/$1');
});

$routes->group('finance', function($routes) {
    $routes->get('all', 'FinanceController::all');
    $routes->match(['get', 'post'], 'add', 'FinanceController::add');
    $routes->match(['get', 'post'], 'edit/(:num)', 'FinanceController::edit/$1');
    $routes->post('ajax_btransections', 'FinanceController::ajax_btransections');
    $routes->get('transactions', 'FinanceController::transactions');
    $routes->post('btransfr_now', 'FinanceController::btransfr_now');
    $routes->get('income_statement', 'FinanceController::income_statement');

});

$routes->group('service_types', function ($routes) {
    $routes->get('all/(:num)', 'ServiceTypeController::all/$1');
    $routes->get('all', 'ServiceTypeController::all');
    $routes->get('add', 'ServiceTypeController::add'); 
    $routes->post('add', 'ServiceTypeController::add'); 
    $routes->get('edit/(:num)', 'ServiceTypeController::edit/$1');
    $routes->post('edit/(:num)', 'ServiceTypeController::edit/$1');
});

$routes->group('supplier', function ($routes) {
    $routes->get('all', 'SupplierController::all');
    $routes->get('add', 'SupplierController::add');
    $routes->post('add', 'SupplierController::add');
    $routes->get('edit/(:num)', 'SupplierController::edit/$1');
    $routes->post('edit/(:num)', 'SupplierController::edit/$1');
    $routes->post('cancel', 'SupplierController::cancel');
});

