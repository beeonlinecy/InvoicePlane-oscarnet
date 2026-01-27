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

        // Загружаем базу данных, если она еще не загружена
        $this->load->database();

        // Получаем список компаний пользователя
        $companies = $this->db->select('c.*, uc.role')
            ->from('companies c')
            ->join('company_users uc', 'uc.company_id = c.id')
            ->where('uc.user_id', $this->session->userdata('user_id'))
            ->get()
            ->result();

        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('email'),
            'companies' => $companies,
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

        // Проверяем права доступа текущего пользователя к компании
        if (!$this->Company_model->user_has_access($this->session->userdata('user_id'), $company['id'])) {
            show_error('Access denied', 403);
        }

        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 120); // 2 минуты

        $this->Sso_model->createToken($this->session->userdata('user_id'), $company['id'], $token, $expires);

        // Редирект в IP
        // Разбиваем URL на части и удаляем 'manager', если он есть в конце
        $base_parts = explode('/', rtrim(base_url(), '/'));
        if (strtolower(end($base_parts)) === 'manager') {
            array_pop($base_parts);
        }
        $root_url = implode('/', $base_parts);

        redirect($root_url . '/companies/' . $company['db_name'] . '/sso?token=' . $token);
    }
}


?>
