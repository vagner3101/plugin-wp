<?php

class Grafica_Rapida_Activator {
    public static function activate() {
        // Configurar a tarefa de exclusão automática de arquivos
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-grafica-rapida-file-manager.php';
        $file_manager = new Grafica_Rapida_File_Manager();
        $file_manager->schedule_file_cleanup();

        // Criar a pasta de uploads se não existir
        $upload_dir = wp_upload_dir();
        $grafica_rapida_upload_dir = $upload_dir['basedir'] . '/grafica-rapida-uploads';
        if (!file_exists($grafica_rapida_upload_dir)) {
            wp_mkdir_p($grafica_rapida_upload_dir);
        }

        // Definir opções padrão
        $default_options = array(
            'allowed_types' => 'jpg,jpeg,png,pdf',
            'max_size' => 10,
            'upload_folder' => 'grafica-rapida-uploads',
            'delete_after' => 30
        );
        update_option('grafica_rapida_upload_options', $default_options);

        // Criar a tabela de acabamentos
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-grafica-rapida-acabamentos.php';
        $acabamentos = new Grafica_Rapida_Acabamentos();
        $acabamentos->create_table();
    }
}
