<?php

class Grafica_Rapida_Admin {
    public function init() {
        // Carregar classes principais
        require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'includes/class-grafica-rapida-acabamentos.php';

        // Carregar classes admin
        require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'admin/class-grafica-rapida-admin-menu.php';
        require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'admin/class-grafica-rapida-admin-page.php';
        require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'admin/class-grafica-rapida-admin-uploads.php';
        require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'admin/class-grafica-rapida-admin-acabamentos.php';

        // Inicializar classes admin
        $admin_menu = new Grafica_Rapida_Admin_Menu();
        $admin_menu->init();

        $admin_page = new Grafica_Rapida_Admin_Page();
        $admin_page->init();

        $admin_uploads = new Grafica_Rapida_Admin_Uploads();
        $admin_uploads->init();

        $admin_acabamentos = new Grafica_Rapida_Admin_Acabamentos();
        $admin_acabamentos->init();

        // Adicionar ações para estilos e scripts admin
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function enqueue_admin_styles($hook) {
        // Enfileirar estilos gerais do admin
        wp_enqueue_style('grafica-rapida-admin-style', GRAFICA_RAPIDA_PLUGIN_URL . 'admin/css/admin-style.css', array(), GRAFICA_RAPIDA_VERSION);
        
        // Enfileirar estilos específicos da página de uploads
        if ('grafica-rapida_page_grafica-rapida-uploads' === $hook) {
            wp_enqueue_style('grafica-rapida-admin-uploads', GRAFICA_RAPIDA_PLUGIN_URL . 'admin/css/admin-uploads.css', array(), GRAFICA_RAPIDA_VERSION);
        }

        // Enfileirar estilos específicos da página de acabamentos
        if ('grafica-rapida_page_grafica-rapida-acabamentos' === $hook) {
            wp_enqueue_style('grafica-rapida-admin-acabamentos', GRAFICA_RAPIDA_PLUGIN_URL . 'admin/css/admin-acabamentos.css', array(), GRAFICA_RAPIDA_VERSION);
        }
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script('grafica-rapida-admin-script', GRAFICA_RAPIDA_PLUGIN_URL . 'admin/js/admin-script.js', array('jquery'), GRAFICA_RAPIDA_VERSION, true);
    }
}
