<?php

    class Login extends CI_Controller {

    
        public function index()
        {
            $this->load->view('auth/login');
        }

        public function auth()
        {
            log_message('debug', 'AUTH HIT');
            $email = trim($this->input->post('email'));
            $password = $this->input->post('password');
            log_message('debug', 'EMAIL: ' . $email);

            if (!$email || !$password) {
                log_message('error', 'Empty credentials');
                return $this->_fail('Email and password required');
            }

            $this->load->model('User_model');
            $user = $this->User_model->findByEmail($email);

            if (!$user || !password_verify($password, $user->password_hash)) {
                
                return $this->_fail('Invalid credentials');
            }

            if ($user->status !== 'active') {
                return $this->_fail('Account is not active');
            }
            log_message('debug', 'LOGIN OK');

            $this->session->set_userdata([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'logged' => true,
            ]);

            // DEBUG:
            //var_dump($this->session->userdata());
            //exit;

            echo("Authenticated successfully");
            redirect('/dashboard');
        }

       private function _fail($message)
        {
            log_message('error', $message);
            $this->session->set_flashdata('error', $message);
            redirect('/login');
        }

        public function logout()
        {
            $this->session->sess_destroy();
            redirect('/login');
        }
    }
?>