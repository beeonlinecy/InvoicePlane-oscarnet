<?php
class Sso_model extends CI_Model {

    public function createToken($user_id, $company_id, $token, $expires_at)
    {
        $this->db->insert('sso_tokens', [
            'user_id' => $user_id,
            'company_id' => $company_id,
            'token' => $token,
            'expires_at' => $expires_at
        ]);
    }

    public function validateToken($token, $company_id)
    {
        $row = $this->db->where([
            'token' => $token,
            'company_id' => $company_id,
            'used' => 0
        ])->get('sso_tokens')->row_array();

        if (!$row) return false;
        if (strtotime($row['expires_at']) < time()) return false;

        // Отметим как использованный
        $this->db->where('id', $row['id'])->update('sso_tokens', ['used' => 1]);
        return $row['user_id'];
    }
}

?>