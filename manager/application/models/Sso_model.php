<?php
    class Sso_model extends CI_Model {

        public function createToken($user_id, $company_id, $token, $expires) {
            $data = [
                'user_id' => $user_id,
                'company_id' => $company_id,
                'token' => $token,
                'expires_at' => $expires,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('sso_tokens', $data);
        }

        public function validateToken($token) {
            $this->db->where('token', $token);
            $this->db->where('expires_at >=', date('Y-m-d H:i:s'));
            $query = $this->db->get('sso_tokens');
            return $query->row_array();
        }

        public function invalidateToken($token) {
            $this->db->where('token', $token);
            $this->db->delete('sso_tokens');
        }
    }
?>