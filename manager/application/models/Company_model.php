<?php
class Company_model extends CI_Model {
  public function get_user_companies($user_id) {
    $this->db->select('c.*');
    $this->db->from('companies c');
    $this->db->join('user_companies uc', 'c.id = uc.company_id');
    $this->db->where('uc.user_id', $user_id);
    return $this->db->get()->result_array();
  }
  
  public function user_has_access($user_id, $company_id) {
    $this->db->select('1');
    $this->db->from('user_companies');
    $this->db->where('user_id', $user_id);
    $this->db->where('company_id', $company_id);
    return ($this->db->count_all_results() > 0);
  }
  
  public function get($company_id) {
    $query = $this->db->get_where('companies', array('id' => $company_id));
    return $query->row_array();
  }

  public function getBySlug($slug) {
      $query = $this->db->get_where('companies', array('slug' => $slug));
      return $query->row_array();
  }
}

?>