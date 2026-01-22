<?php
class Sso extends CI_Controller {
  public function login($company_id) {
    $this->load->model('Company_model');
    if ($this->Company_model->user_has_access($this->session->manager_user_id, $company_id)) {
      $company = $this->Company_model->get($company_id);
      // initiate InvoicePlane PHP session
      session_name('invoiceplane');
      session_start();
      $_SESSION['user_id'] = $company['system_user_id'];
      redirect($company['base_url'].'/dashboard');
    } else {
      // display error message or redirect to login page
    }
  }
}
?>