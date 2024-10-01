<?php

class Grafica_Rapida_Public {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name = 'grafica-rapida', $version = '1.0.0') {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function init() {
        // Carregar classes public
        require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'public/class-grafica-rapida-public-functions.php';
        require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'public/class-grafica-rapida-product-fields.php';

        // Inicializar classes public
        $public_functions = new Grafica_Rapida_Public_Functions();
        $public_functions->init();

        // Inicializar e registrar shortcodes para campos de produto
        $product_fields = new Grafica_Rapida_Product_Fields($this->plugin_name, $this->version);
        $this->register_shortcodes($product_fields);

        // Registrar ação AJAX para upload de arte
        add_action('wp_ajax_upload_arte', array($this, 'upload_arte'));
        add_action('wp_ajax_nopriv_upload_arte', array($this, 'upload_arte'));

        // Adicionar script para lidar com o AJAX no frontend
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    private function register_shortcodes($product_fields) {
        add_shortcode('grafica_rapida_tipo_venda', array($product_fields, 'tipo_venda_shortcode'));
        add_shortcode('grafica_rapida_criacao_arte', array($product_fields, 'criacao_arte_shortcode'));
        add_shortcode('grafica_rapida_gabaritos', array($product_fields, 'gabaritos_shortcode'));
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name . '-public', plugin_dir_url(__FILE__) . 'js/grafica-rapida-public.js', array('jquery'), $this->version, true);
        wp_localize_script($this->plugin_name . '-public', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }

    public function upload_arte() {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($_FILES['arte'], $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            // Obter as opções de upload
            $options = get_option('grafica_rapida_upload_options');
            $allowed_types = isset($options['allowed_types']) ? explode(',', $options['allowed_types']) : array('jpg', 'jpeg', 'png', 'pdf');
            $max_size = isset($options['max_size']) ? $options['max_size'] * 1024 * 1024 : 5 * 1024 * 1024; // Converter MB para bytes

            // Verificar o tipo de arquivo
            $file_type = wp_check_filetype(basename($movefile['file']), null);
            if (!in_array($file_type['ext'], $allowed_types)) {
                wp_send_json_error('Tipo de arquivo não permitido.');
                return;
            }

            // Verificar o tamanho do arquivo
            if (filesize($movefile['file']) > $max_size) {
                wp_send_json_error('O arquivo excede o tamanho máximo permitido.');
                return;
            }

            // Mover o arquivo para a pasta correta
            $upload_dir = wp_upload_dir();
            $new_file_path = $upload_dir['basedir'] . '/grafica-rapida-uploads/' . basename($movefile['file']);
            
            if (!file_exists($upload_dir['basedir'] . '/grafica-rapida-uploads/')) {
                wp_mkdir_p($upload_dir['basedir'] . '/grafica-rapida-uploads/');
            }

            if (rename($movefile['file'], $new_file_path)) {
                wp_send_json_success('Arquivo enviado com sucesso.');
            } else {
                wp_send_json_error('Erro ao mover o arquivo para a pasta final.');
            }
        } else {
            wp_send_json_error($movefile['error']);
        }
    }
}
