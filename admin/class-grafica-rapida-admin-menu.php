<?php

class Grafica_Rapida_Admin_Menu {
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Gráfica Rápida',
            'Gráfica Rápida',
            'manage_options',
            'grafica-rapida',
            array($this, 'display_admin_page'),
            'dashicons-store',
            30
        );

        add_submenu_page(
            'grafica-rapida',
            'Uploads',
            'Uploads',
            'manage_options',
            'grafica-rapida-uploads',
            array($this, 'display_uploads_page')
        );
    }

    public function display_admin_page() {
        $admin_page = new Grafica_Rapida_Admin_Page();
        $admin_page->render_page();
    }

    public function display_uploads_page() {
        $uploads_page = new Grafica_Rapida_Admin_Uploads();
        $uploads_page->render_page();
    }
}
