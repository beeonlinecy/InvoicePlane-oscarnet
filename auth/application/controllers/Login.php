<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->library(['session']);
    }

    /**
     * Форма логина
     */
    public function index()
    {
        // Уже залогинен — отправляем дальше
        if ($this->session->userdata('user_id')) {
            redirect('/companies/demo');
        }

        $this->load->view('login');
    }

    /**
     * Обработка POST логина
     */
    public function auth()
    {
        $email    = trim($this->input->post('email', true));
        $password = (string)$this->input->post('password');

        if (!$email || !$password) {
            $this->session->set_flashdata('error', 'Email and password required');
            redirect('/auth/login');
        }

        // ⚠️ пример: таблица ip_users (как в InvoicePlane)
        $user = $this->db
            ->where('email', $email)
            ->where('active', 1)
            ->get('ip_users')
            ->row();

        if (!$user || !password_verify($password, $user->password)) {
            $this->session->set_flashdata('error', 'Invalid credentials');
            redirect('/auth/login');
        }

        // ✅ Успешный логин
        $this->session->set_userdata([
            'user_id'    => $user->user_id,
            'user_email' => $user->email,
            'logged_in'  => true,
        ]);

        // Куда редиректить после логина
        redirect('/companies/demo');
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/auth/login');
    }
}
