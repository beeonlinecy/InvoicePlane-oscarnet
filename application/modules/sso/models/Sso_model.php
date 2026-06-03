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

    /**
     * Проверить существование и срок действия токена в локальной таблице
     *
     * @param string $token
     * @return object|null
     */
    public function validate_token($token)
    {
        return $this->db->where('token', $token)
            ->where('expires >=', date('Y-m-d H:i:s'))
            ->get('ip_sso_tokens')
            ->row();
    }

    /**
     * Удалить использованный токен
     */
    public function delete_token($token)
    {
        $this->db->where('token', $token)->delete('ip_sso_tokens');
    }
}