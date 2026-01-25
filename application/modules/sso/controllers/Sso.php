<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sso extends MX_Controller {

    public function login()
    {
        $token = $this->input->get('token');
        if (!$token) {
            show_error('Token missing', 400);
        }

        // подключаем manager БД
        $manager_db = $this->load->database('manager', TRUE);

        $row = $manager_db
            ->where('token', $token)
            ->where('used', 0)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->get('sso_tokens')
            ->row();

        if (!$row) {
            show_error('Invalid or expired token', 403);
        }

        // помечаем токен использованным
        $manager_db
            ->where('id', $row->id)
            ->update('sso_tokens', ['used' => 1]);

        // грузим IP user
        $this->load->model('users/mdl_users');
        $user = $this->mdl_users->get_by_id($row->ip_user_id);

        if (!$user) {
            show_error('IP user not found', 404);
        }

        // IP-логин (как делает sessions)
        $this->session->set_userdata([
            'user_id'   => $user->user_id,
            'user_name' => $user->user_name,
            'user_type' => $user->user_type,
            'logged_in' => true
        ]);

        redirect('dashboard');
    }
}