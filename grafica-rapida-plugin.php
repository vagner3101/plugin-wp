<?php
/**
 * Plugin Name: Gráfica Rápida Plugin
 * Plugin URI: https://seusite.com/grafica-rapida-plugin
 * Description: Um plugin para gerenciar uma loja virtual de gráfica rápida no WooCommerce.
 * Version: 1.0.0
 * Author: Seu Nome
 * Author URI: https://seusite.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grafica-rapida-plugin
 * Domain Path: /languages
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Definir constantes
define('GRAFICA_RAPIDA_VERSION', '1.0.0');
define('GRAFICA_RAPIDA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GRAFICA_RAPIDA_PLUGIN_URL', plugin_dir_url(__FILE__));

// Incluir as classes principais
require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'includes/class-grafica-rapida-activator.php';
require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'includes/class-grafica-rapida-deactivator.php';
require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'includes/class-grafica-rapida-admin.php';
require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'includes/class-grafica-rapida-public.php';

// Registrar hooks de ativação e desativação
register_activation_hook(__FILE__, array('Grafica_Rapida_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('Grafica_Rapida_Deactivator', 'deactivate'));

/**
 * Iniciar o plugin
 */
function run_grafica_rapida_plugin() {
    // Inicializar a classe admin
    $admin = new Grafica_Rapida_Admin();
    $admin->init();

    // Inicializar a classe public
    $public = new Grafica_Rapida_Public();
    $public->init();
}

// Executar o plugin
add_action('plugins_loaded', 'run_grafica_rapida_plugin');
