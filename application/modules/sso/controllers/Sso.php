<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sso extends Base_Controller {

    public function index()
    {
        $token = $this->input->get('token');
        if (!$token)
        {
            //log_message('error', 'SSO Login failed: Token missing from request.');
            die('SSO Login failed: Token missing from request.');
            //$this->session->set_flashdata('alert_danger', 'Токен авторизации отсутствует.');
            //redirect('sessions/login');
        }

        // подключаем manager БД
        $manager_db = $this->load->database('manager', TRUE);

        // Определяем ID текущей компании, чтобы убедиться, что токен предназначен для нее.
        // Мы предполагаем, что в таблице `companies` менеджера есть колонка `db_name`,
        // которая соответствует имени БД текущего инстанса InvoicePlane.
        $company_db_name = $this->db->database;
        $company = $manager_db->where('db_name', $company_db_name)->get('companies')->row();

        if (!$company) {
            //log_message('error', 'SSO Login failed: Company not found in manager DB for database ' . $company_db_name);
            die('SSO Login failed: Company not found in manager DB for database ' . $company_db_name);
            //$this->session->set_flashdata('alert_danger', 'Компания не найдена в системе управления.');
            //redirect('sessions/login');
        }

        $row = $manager_db
            ->where('token', $token)
            ->where('company_id', $company->id) // Проверка, что токен выдан для этой компании
            ->where('used', 0)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->get('sso_tokens')
            ->row();

        if (!$row) {
            log_message('error', 'SSO Login failed: Invalid or expired token (' . $token . ') for company ID ' . $company->id);
            die('SSO Login failed: Invalid or expired token.');
            $this->session->set_flashdata('alert_danger', 'Неверный или просроченный токен авторизации.');
            //redirect('sessions/login');
        }

        // помечаем токен использованным
        $manager_db
            ->where('id', $row->id)
            ->update('sso_tokens', ['used' => 1]);

        // Загружаем пользователя InvoicePlane по ID, сохраненному в токене
        $this->load->model('users/mdl_users');
        $user = (isset($row->ip_user_id)) ? $this->mdl_users->get_by_id($row->ip_user_id) : null;

        if (!$user) {
            $log_user_id = (isset($row->ip_user_id)) ? $row->ip_user_id : 'none';
            log_message('error', 'SSO Login failed: Linked InvoicePlane user not found (ID: ' . $log_user_id . ')');
            die('SSO Login failed: Linked InvoicePlane user not found.');
            $this->session->set_flashdata('alert_danger', 'Связанный пользователь InvoicePlane не найден.');
            //redirect('sessions/login');
        }

        // IP-логин (как делает sessions)
        $this->session->set_userdata([
            'user_id'   => $user->user_id,
            'user_name' => $user->user_name,
            'user_type' => $user->user_type,
            'logged_in' => true
        ]);

        //redirect('dashboard');
    }
}