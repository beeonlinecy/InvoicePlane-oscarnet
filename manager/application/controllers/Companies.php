<?php
class Companies extends CI_Controller {
  public function index() {
    $this->load->model('Company_model');
    $data['companies'] = $this->Company_model->get_user_companies($this->session->manager_user_id);
    $this->load->view('companies', $data);
  }
}

?>