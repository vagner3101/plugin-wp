<?php

class Grafica_Rapida_Public_Functions {
    public function init() {
        // Inicializar funções públicas
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts'));
    }

    public function enqueue_public_styles() {
        // Enfileirar estilos para o frontend, se necessário
    }

    public function enqueue_public_scripts() {
        // Enfileirar scripts para o frontend, se necessário
    }

    // Adicione mais métodos para funcionalidades públicas conforme necessário
}
