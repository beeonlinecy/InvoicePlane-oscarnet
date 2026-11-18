<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Mdl_Backup extends CI_Model
{
    private $backup_path;

    public function __construct()
    {
        parent::__construct();
        $this->backup_path = APPPATH . 'cache/backups/';
    }

    public function get_all()
    {
        if ( ! is_dir($this->backup_path)) {
            return [];
        }

        $paths = glob($this->backup_path . 'backup_*.zip');
        if ($paths === false) {
            throw new RuntimeException('Backup files could not be listed.');
        }

        $backups = [];
        foreach ($paths as $path) {
            $filename = basename($path);
            if ( ! $this->is_valid_filename($filename) || ! is_file($path) || is_link($path)) {
                continue;
            }

            $backup = $this->get($filename);
            if ($backup !== null) {
                $backups[] = $backup;
            }
        }

        usort($backups, function ($first, $second) {
            return $second->created_at <=> $first->created_at;
        });

        return $backups;
    }

    public function get($id)
    {
        if ( ! $this->is_valid_filename($id)) {
            return;
        }

        $path = $this->backup_path . $id;
        if ( ! is_file($path) || is_link($path)) {
            return;
        }

        $size       = filesize($path);
        $created_at = filemtime($path);
        if ($size === false || $created_at === false) {
            throw new RuntimeException('Backup file metadata could not be read.');
        }

        return (object) [
            'id'         => $id,
            'name'       => $id,
            'path'       => $path,
            'size'       => $size,
            'created_at' => $created_at,
        ];
    }

    public function delete($id)
    {
        $backup = $this->get($id);

        return $backup !== null && unlink($backup->path);
    }

    private function is_valid_filename($filename)
    {
        return is_string($filename)
            && preg_match('/\Abackup_[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9]{2}-[0-9]{2}-[0-9]{2}(?:_[a-f0-9]{8})?\.zip\z/', $filename) === 1;
    }
}
