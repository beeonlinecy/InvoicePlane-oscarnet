<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Backup extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mdl_backup');
        $this->load->library('Backup_library');
    }

    public function index() {
        $backups = $this->Mdl_backup->get_all();
        $this->layout->set('backups', $backups);
        $this->layout->buffer('content', 'backup/index');
        $this->layout->render();
    }

    public function create() {
        $file = $this->backup_library->create_backup();
        $this->Mdl_backup->add($file);
        $this->session->set_flashdata('alert_success', 'Backup created: '.$file);
        redirect('backup');
    }

    public function download($id) {
        $file = $this->Mdl_backup->get($id);
        if ($file && file_exists($file->path)) {
            $this->load->helper('download');
            force_download($file->path, NULL);
        } else {
            show_404();
        }
    }

    public function delete($id) {
        $file = $this->Mdl_backup->get($id);
        if ($file && file_exists($file->path)) {
            unlink($file->path);
            $this->Mdl_backup->delete($id);
            $this->session->set_flashdata('alert_success', 'Backup deleted.');
        }
        redirect('backup');
    }
}