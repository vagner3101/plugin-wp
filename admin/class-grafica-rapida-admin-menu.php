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
    }

    public function display_admin_page() {
        $admin_page = new Grafica_Rapida_Admin_Page();
        $admin_page->render_page();
    }
}
