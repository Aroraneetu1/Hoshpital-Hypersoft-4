<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class LoginController extends Controller
{
    public function __construct()
    {
        helper(['url', 'form','email','elfin_helper']); // Load necessary helpers
        $this->elfin_model = model('App\Models\ElfinModel'); // Assuming you have an ElfinModel
        $this->session = Services::session();
        $this->validation = Services::validation();
        // Assuming you have a function to set timezone in your helper or library.
        if (function_exists('set_timezone')) {
            set_timezone();
        }
    }

    public function admin()
    {
        if (get_the_current_user(1)) {
            return redirect()->to(get_site_url('admin'));
        }

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if ($this->request->is('post') && $this->validate($rules)) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $status = $this->elfin_model->login($username, $password, 1);

            if ($status) {
                return redirect()->to(get_site_url('admin'));
            } else {
                return redirect()->to(get_site_url('login/admin'));
            }
        }

        return view('login/admin');
    }

    public function forgotpassword()
    {
        $rules = [
            'email' => 'required|valid_email',
        ];

        if ($this->request->is('post') && $this->validate($rules)) {
            $email = $this->request->getPost('email');
            $where = ['email' => $email];
            $user = $this->elfin_model->get_row('users', $where);

            if (empty($user)) {
                $this->session->setFlashdata('error_msg', 'Invalid email, Please enter registered email.');
                return redirect()->to(site_url('login/forgotpassword'));
            }

            if ($user->status == 0) {
                $this->session->setFlashdata('error_msg', 'Sorry your account is inactive.');
                return redirect()->to(site_url('login/forgotpassword'));
            }

            // Insert secret key in database
            $secret_key = sha1($email);
            $data = ['secret_key' => $secret_key];
            $this->elfin_model->update('users', $data, $where);

            // Send mail to user
            $subject = 'Reset your password on AMZ';
            $from = ['no-reply@amzsimple.com' => 'AMZ'];
            $username = $user->firstname . ' ' . $user->lastname;
            $html = $this->template_for_forget_password($username, $email, $secret_key); // Ensure this method exists.

            // Use CodeIgniter's Email Service or your custom mail function
            // Example using CodeIgniter's Email Service:
            $emailService = Services::email();
            $emailService->setFrom($from['no-reply@amzsimple.com'], $from['AMZ']);
            $emailService->setTo($email);
            $emailService->setSubject($subject);
            $emailService->setMessage($html);

            if (!$emailService->send()) {
                // Handle email sending failure
                // log_message('error', 'Email sending failed: ' . $emailService->printDebugger());
            }

            // Or, using your custom mail function (send_email_by_mailgun):
            // send_email_by_mailgun($email, $subject, $html, 'no-reply@amzsimple.com');

            $this->session->setFlashdata('success_msg', 'Please check your email to reset password.');
            return redirect()->to(site_url('login/forgotpassword'));
        }

        return view('login/forgotpassword');
    }

    // Assuming you have a template_for_forget_password method:
    private function template_for_forget_password($username, $email, $secret_key)
    {
        // Construct your email template here.
        // Replace with your actual implementation.
        return "Hello $username, please reset your password using this link: " . site_url("resetpassword?email=$email&key=$secret_key");

    }

    public function forget_password_response($secret_key)
    {
        $where = ['secret_key' => $secret_key];
        $user = $this->elfin_model->get_row('users', $where);

        if (empty($user)) {
            $this->session->setFlashdata('error_msg', 'Email link has been expired, please enter email.');
            return redirect()->to(site_url('login/forgotpassword'));
        }

        if ($this->request->getPost('password')) {
            $pwd = sha1($this->request->getPost('password'));
            $where = ['secret_key' => $secret_key];
            $data = ['password' => $pwd, 'secret_key' => ''];
            $this->elfin_model->update('users', $data, $where);

            $this->session->setFlashdata('success_msg', 'Password has been reset successfully, please login..!');
            return redirect()->to(site_url('login/admin'));
        }

        return view('login/reset_forget_password');
    }
}