<?php
class Auth extends CI_Controller {
  public function login() {
    $this->load->model('User_model');
    if ($user = $this->User_model->validate($this->input->post())) {
      $this->session->set_userdata('manager_user_id', $user['id']);
      redirect('/manager/companies');
    } else {
      // display error message or redirect to login page
    }
  }
}

?>