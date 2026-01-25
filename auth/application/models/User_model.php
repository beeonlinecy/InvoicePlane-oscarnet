<?php
class User_model extends CI_Model {

  public function validate($data) {
    $this->db->where('email', $data['email']);
    $query = $this->db->get('users');
    if (password_verify($data['password'], $query->row()->password_hash)) {
      return $query->row_array();
    } else {
      return false;
    }
  }

  public function findByEmail(string $email)
    {
        return $this->db
            ->where('email', $email)
            ->limit(1)
            ->get('users')
            ->row();
    }
    
}

?>