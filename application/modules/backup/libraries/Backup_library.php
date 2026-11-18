<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Backup_library
{
    private $backup_path;

    public function __construct()
    {
        $this->backup_path = APPPATH . 'cache/backups/';
    }

    public function create_backup()
    {
        if ( ! class_exists('ZipArchive')) {
            throw new RuntimeException('The PHP Zip extension is required to create backups.');
        }

        if ( ! is_dir($this->backup_path) && ! mkdir($this->backup_path, 0700, true) && ! is_dir($this->backup_path)) {
            throw new RuntimeException('The backup directory could not be created.');
        }

        $filename     = 'backup_' . date('Y-m-d_H-i-s') . '_' . bin2hex(random_bytes(4)) . '.zip';
        $archive_path = $this->backup_path . $filename;
        $zip          = new ZipArchive();
        $result       = $zip->open($archive_path, ZipArchive::CREATE | ZipArchive::EXCL);

        if ($result !== true) {
            throw new RuntimeException('The backup archive could not be opened.');
        }

        $archive_completed = false;
        $dump_path         = null;

        try {
            $temporary_dump_path = tempnam($this->backup_path, 'database_');
            if ($temporary_dump_path === false) {
                throw new RuntimeException('The database dump file could not be created.');
            }
            $dump_path = $temporary_dump_path;

            $this->create_database_dump($dump_path);
            if ( ! $zip->addFile($dump_path, 'database.sql')) {
                throw new RuntimeException('The database dump could not be added to the archive.');
            }

            $uploads_path = FCPATH . 'uploads';
            if ( ! is_dir($uploads_path)) {
                throw new RuntimeException('The uploads directory does not exist.');
            }

            $this->add_folder_to_zip($uploads_path, $zip, 'uploads');

            if ( ! $zip->close()) {
                throw new RuntimeException('The backup archive could not be finalized.');
            }

            $archive_completed = true;
        } finally {
            if ( ! $archive_completed) {
                $zip->close();
                if (is_file($archive_path) && ! unlink($archive_path)) {
                    log_message('error', 'Could not remove incomplete backup archive: ' . $archive_path);
                }
            }

            if ($dump_path !== null && is_file($dump_path) && ! unlink($dump_path)) {
                log_message('error', 'Could not remove temporary database dump: ' . $dump_path);
            }
        }

        return $filename;
    }

    private function create_database_dump($path)
    {
        $stream = fopen($path, 'wb');
        if ($stream === false) {
            throw new RuntimeException('The database dump file could not be opened.');
        }

        try {
            $CI           = & get_instance();
            $tables_query = $CI->db->query('SHOW TABLES');
            if ($tables_query === false) {
                throw new RuntimeException('The database tables could not be listed.');
            }

            foreach ($tables_query->result_array() as $table_row) {
                $table_values = array_values($table_row);
                if (empty($table_values[0])) {
                    continue;
                }

                $table         = $table_values[0];
                $escaped_table = $CI->db->escape_identifiers($table);
                $create_query  = $CI->db->query('SHOW CREATE TABLE ' . $escaped_table);
                if ($create_query === false) {
                    throw new RuntimeException('A database table definition could not be read.');
                }

                $create_values = array_values($create_query->row_array());
                if ( ! isset($create_values[1])) {
                    throw new RuntimeException('A database table definition is incomplete.');
                }
                $this->write_to_stream($stream, $create_values[1] . ";\n\n");

                $rows_query = $CI->db->query('SELECT * FROM ' . $escaped_table);
                if ($rows_query === false) {
                    throw new RuntimeException('Database table rows could not be read.');
                }

                foreach ($rows_query->result_array() as $row) {
                    $values = array_map([$CI->db, 'escape'], array_values($row));
                    $this->write_to_stream(
                        $stream,
                        'INSERT INTO ' . $escaped_table . ' VALUES (' . implode(',', $values) . ");\n"
                    );
                }

                $this->write_to_stream($stream, "\n");
            }
        } finally {
            fclose($stream);
        }
    }

    private function write_to_stream($stream, $content)
    {
        if (fwrite($stream, $content) !== mb_strlen($content, '8bit')) {
            throw new RuntimeException('The database dump could not be written.');
        }
    }

    private function add_folder_to_zip($folder, &$zip, $local_name)
    {
        $folder = realpath($folder);
        if ($folder === false) {
            throw new RuntimeException('The uploads directory could not be read.');
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            if ( ! $file->isFile() || $file->isLink()) {
                continue;
            }

            $path = $file->getRealPath();
            if ($path === false) {
                throw new RuntimeException('An uploaded file could not be read.');
            }

            $relative_path = mb_substr($path, mb_strlen($folder, '8bit') + 1, null, '8bit');
            $archive_name  = $local_name . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $relative_path);
            if ( ! $zip->addFile($path, $archive_name)) {
                throw new RuntimeException('An uploaded file could not be added to the archive.');
            }
        }
    }
}
