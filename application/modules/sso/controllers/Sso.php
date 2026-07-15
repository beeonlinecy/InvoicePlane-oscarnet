<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sso extends Base_Controller {

    public function index()
    {
        $token = $this->input->get('token');
        if (!$token)
        {
            $this->session->set_flashdata('alert_danger', 'Токен авторизации отсутствует.');
            redirect('sessions/login');
        }

        $this->load->model('sso_model');

        // Проверяем токен локально
        $token_row = $this->sso_model->validate_token($token);

        if (!$token_row) {
            log_message('error', 'SSO Login failed: Invalid or expired token (' . $token . ')');
            $this->session->set_flashdata('alert_danger', 'Неверный или просроченный токен авторизации.');
            redirect('https://invonos.com/manager');
        }

        // Получаем пользователя по ID из токена
        $ip_user_id = isset($token_row->user_id) ? (int) $token_row->user_id : 0;
        $user = ($ip_user_id > 0) ? $this->sso_model->get_user_by_id($ip_user_id) : null;

        if (!$user) {
            log_message('error', 'SSO Login failed: Linked InvoicePlane user not found (ID: ' . $ip_user_id . ')');
            $this->session->set_flashdata('alert_danger', 'Связанный пользователь InvoicePlane не найден.');
            redirect('https://invonos.com/manager');
        }

        // Авторизация
        $this->session->set_userdata([
            'user_id'   => $user->user_id,
            'user_name' => $user->user_name,
            'user_type' => $user->user_type,
            'logged_in' => true,
            'login_via_sso' => true
        ]);

        // Удаляем использованный токен
        $this->sso_model->delete_token($token);

        redirect('dashboard');
    }
}