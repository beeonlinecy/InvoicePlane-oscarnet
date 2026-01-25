<?php

    class Login extends CI_Controller {

        
    /*
        public function index()
        {
            //$this->load->view('auth/login');
        }

        public function auth()
        {
            echo '<pre>';
            var_dump($_POST);
            die('AUTH HIT');
            $email = trim($this->input->post('email'));
            $password = $this->input->post('password');

            if (!$email || !$password) {
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

            $this->session->set_userdata([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'logged_in' => true,
            ]);

            echo("Authenticated successfully");
            redirect('/dashboard');
        }

        */
        public function index()
{
    echo "INDEX HIT";
}

public function auth()
{
    echo "AUTH HIT";
}
        private function _fail($message)
        {
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