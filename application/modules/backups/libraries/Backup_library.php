<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Backup_library {

    private $backup_path = './uploads/backups/';

    public function create_backup() {
        if (!is_dir($this->backup_path)) {
            mkdir($this->backup_path, 0777, true);
        }

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.zip';
        $zip = new ZipArchive();
        $zip->open($this->backup_path . $filename, ZipArchive::CREATE);

        // 1. База данных
        $CI =& get_instance();
        $db = $CI->db;
        $result = $db->query('SHOW TABLES')->result_array();
        $dump = "";
        foreach ($result as $row) {
            $table = array_values($row)[0];
            $res = $db->query("SELECT * FROM `$table`");
            $create = $db->query("SHOW CREATE TABLE `$table`")->row_array();
            $dump .= $create['Create Table'] . ";\n\n";
            foreach ($res->result_array() as $line) {
                $vals = array_map([$db, 'escape'], array_values($line));
                $dump .= "INSERT INTO `$table` VALUES (" . implode(',', $vals) . ");\n";
            }
            $dump .= "\n\n";
        }
        $zip->addFromString('database.sql', $dump);

        // 2. Только uploads/
        $this->add_folder_to_zip('./uploads/', $zip, 'uploads');

        $zip->close();
        return $filename;
    }

    private function add_folder_to_zip($folder, &$zip, $local_name) {
        if (!is_dir($folder)) return;
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder));
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $path = $file->getRealPath();
                $zip->addFile($path, $local_name . '/' . substr($path, strlen($folder)));
            }
        }
    }
}
