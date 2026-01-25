<?php
class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged')) {
            redirect('/login');
        }
    }


    public function index()
    {
        if (!$this->session->userdata('logged')) {
            redirect('login');
        }

        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('email'),
        ];
        $this->load->view('dashboard/index', $data);
    }
    
    public function sso($slug)
    {
        $this->load->model('Company_model');
        $this->load->model('Sso_model');

        $company = $this->Company_model->getBySlug($slug);
        if (!$company) {
            show_error('Company not found', 404);
        }

        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 120); // 2 минуты

        $this->Sso_model->createToken($this->session->userdata('user_id'), $company['id'], $token, $expires);

        // Редирект в IP
        redirect(base_url('../companies/'.$company['db_name'].'/sso?token='.$token));
    }
}


?>

