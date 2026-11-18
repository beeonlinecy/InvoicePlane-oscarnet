<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Backup extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mdl_backup');
        $this->load->library('Backup_library');
    }

    public function index()
    {
        $this->layout->set('backups', $this->Mdl_backup->get_all());
        $this->layout->buffer('content', 'backup/index');
        $this->layout->render();
    }

    public function create()
    {
        if ($this->input->method(true) !== 'POST') {
            show_404();
        }

        try {
            $filename = $this->backup_library->create_backup();
            $this->session->set_flashdata('alert_success', trans('backup_created') . ': ' . $filename);
        } catch (RuntimeException $exception) {
            log_message('error', 'Backup creation failed: ' . $exception->getMessage());
            $this->session->set_flashdata('alert_error', trans('backup_failed'));
        }

        redirect('backup');
    }

    public function download($id)
    {
        $file = $this->Mdl_backup->get($id);
        if ( ! $file || ! is_file($file->path)) {
            show_404();
        }

        $this->load->helper('download');
        force_download($file->path, null);
    }

    public function delete($id)
    {
        if ($this->input->method(true) !== 'POST') {
            show_404();
        }

        $file = $this->Mdl_backup->get($id);
        if ( ! $file) {
            show_404();
        }

        if ($this->Mdl_backup->delete($id)) {
            $this->session->set_flashdata('alert_success', trans('backup_deleted'));
        } else {
            log_message('error', 'Could not delete backup archive: ' . $file->path);
            $this->session->set_flashdata('alert_error', trans('backup_delete_failed'));
        }

        redirect('backup');
    }
}
