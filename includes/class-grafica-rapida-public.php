<?php

class Grafica_Rapida_Public {
    public function init() {
        // Carregar classes public
        require_once GRAFICA_RAPIDA_PLUGIN_DIR . 'public/class-grafica-rapida-public-functions.php';

        // Inicializar classes public
        $public_functions = new Grafica_Rapida_Public_Functions();
        $public_functions->init();
    }
}
