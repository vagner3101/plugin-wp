<?php

class Grafica_Rapida_Admin {
    public function init() {
        // Carregar classes admin
        require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'admin/class-grafica-rapida-admin-menu.php';
        require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'admin/class-grafica-rapida-admin-page.php';

        // Inicializar classes admin
        $admin_menu = new Grafica_Rapida_Admin_Menu();
        $admin_menu->init();

        $admin_page = new Grafica_Rapida_Admin_Page();
        $admin_page->init();

        // Adicionar ações para estilos e scripts admin
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style('grafica-rapida-admin-style', GRAFICA_RAPIDA_PLUGIN_URL . 'assets/css/admin-style.css', array(), GRAFICA_RAPIDA_VERSION);
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script('grafica-rapida-admin-script', GRAFICA_RAPIDA_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), GRAFICA_RAPIDA_VERSION, true);
    }
}
