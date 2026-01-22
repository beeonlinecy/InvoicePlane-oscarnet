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
}

?>