<?php
// путь к CodeIgniter IP
require 'index.php'; // или другой путь к bootstrap IP

$token = $_GET['token'] ?? '';
$company_id = 1; // или определить из текущей директории IP

$this->load->model('Sso_model');
$user_id = $this->Sso_model->validateToken($token, $company_id);

if ($user_id) {
    $this->session->set_userdata([
        'logged' => true,
        'user_id' => $user_id
    ]);
    redirect('dashboard'); // или основной маршрут IP
} else {
    show_error('Invalid or expired token', 403);
}
?>