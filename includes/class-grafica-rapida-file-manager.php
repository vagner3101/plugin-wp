<?php

class Grafica_Rapida_File_Manager {
    private $options;

    public function __construct() {
        $this->options = get_option('grafica_rapida_upload_options');
    }

    public function schedule_file_cleanup() {
        if (!wp_next_scheduled('grafica_rapida_file_cleanup')) {
            wp_schedule_event(time(), 'daily', 'grafica_rapida_file_cleanup');
        }
        add_action('grafica_rapida_file_cleanup', array($this, 'cleanup_old_files'));
    }

    public function cleanup_old_files() {
        $days = isset($this->options['delete_after']) ? intval($this->options['delete_after']) : 30;
        $upload_dir = wp_upload_dir();
        $target_dir = trailingslashit($upload_dir['basedir']) . $this->options['upload_folder'];
        $files = glob($target_dir . '*');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= $days * 24 * 60 * 60) {
                    unlink($file);
                }
            }
        }
    }

    public function unschedule_file_cleanup() {
        $timestamp = wp_next_scheduled('grafica_rapida_file_cleanup');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'grafica_rapida_file_cleanup');
        }
    }
}
