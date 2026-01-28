<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sso_model extends CI_Model
{
    /**
     * Получить пользователя InvoicePlane по ID
     *
     * @param int $user_id
     * @return object|null
     */
    public function get_user_by_id($user_id)
    {
        return $this->db->where('user_id', $user_id)
            ->get('ip_users')
            ->row();
    }
}