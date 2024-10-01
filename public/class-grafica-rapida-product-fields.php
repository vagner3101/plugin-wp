<?php

class Grafica_Rapida_Product_Fields {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_upload_arte', array($this, 'handle_upload_arte'));
        add_action('wp_ajax_nopriv_upload_arte', array($this, 'handle_upload_arte'));
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name . '-product-fields', plugin_dir_url(__FILE__) . 'js/product-fields.js', array('jquery'), $this->version, true);
        wp_localize_script($this->plugin_name . '-product-fields', 'grafica_rapida_vars', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
        wp_enqueue_style($this->plugin_name . '-product-fields', plugin_dir_url(__FILE__) . 'css/product-fields.css', array(), $this->version, 'all');
    }

    public function tipo_venda_shortcode() {
        global $product;
        if (!$product) {
            return '';
        }

        $tipo_venda = get_post_meta($product->get_id(), '_tipo_venda', true);
        if (empty($tipo_venda) || $tipo_venda === 'nenhum') {
            return '';
        }

        $output = '<div class="grafica-rapida-tipo-venda">';

        switch ($tipo_venda) {
            case 'metro_quadrado':
                $output .= $this->render_metro_quadrado($product);
                break;
            case 'metro_linear':
                $output .= $this->render_metro_linear($product);
                break;
            case 'quantidade':
                $output .= $this->render_quantidade($product);
                break;
            case 'acabamentos':
                $output .= $this->render_acabamentos($product);
                break;
        }

        $output .= '</div>';

        return $output;
    }

    private function render_metro_quadrado($product) {
        $largura_minima = get_post_meta($product->get_id(), '_largura_minima_m2', true);
        $largura_maxima = get_post_meta($product->get_id(), '_largura_maxima_m2', true);
        $valor_minimo = get_post_meta($product->get_id(), '_valor_minimo_m2', true);

        $output = '<div class="grafica-rapida-metro-quadrado">';
        $output .= '<h4>Metro Quadrado</h4>';
        $output .= '<div class="campos-container">';
        $output .= '<div class="campo-wrapper">';
        $output .= '<label for="largura_m2">Largura (cm):</label>';
        $output .= '<input type="number" id="largura_m2" name="largura" min="' . esc_attr($largura_minima) . '" max="' . esc_attr($largura_maxima) . '" required>';
        $output .= '<p class="campo-info">Largura mínima: ' . esc_html($largura_minima) . 'cm, máxima: ' . esc_html($largura_maxima) . 'cm</p>';
        $output .= '</div>';
        $output .= '<div class="campo-wrapper">';
        $output .= '<label for="altura_m2">Altura (cm):</label>';
        $output .= '<input type="number" id="altura_m2" name="altura" min="1" required>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<input type="hidden" name="valor_minimo" value="' . esc_attr($valor_minimo) . '">';
        $output .= '<input type="hidden" name="preco_original" value="' . esc_attr($product->get_price()) . '">';
        $output .= '</div>';

        return $output;
    }

    private function render_metro_linear($product) {
        $larguras_padrao = explode('/', get_post_meta($product->get_id(), '_larguras_padrao_ml', true));
        $altura_minima = get_post_meta($product->get_id(), '_altura_minima_ml', true);
        $altura_maxima = get_post_meta($product->get_id(), '_altura_maxima_ml', true);
        $valor_minimo = get_post_meta($product->get_id(), '_valor_minimo_ml', true);

        $output = '<div class="grafica-rapida-metro-linear">';
        $output .= '<h4>Metro Linear</h4>';
        $output .= '<div class="campos-container">';
        $output .= '<div class="campo-wrapper">';
        $output .= '<label for="largura_ml">Largura:</label>';
        $output .= '<select id="largura_ml" name="largura">';
        foreach ($larguras_padrao as $largura) {
            $output .= '<option value="' . esc_attr(trim($largura)) . '">' . esc_html(trim($largura)) . ' cm</option>';
        }
        $output .= '</select>';
        $output .= '</div>';
        $output .= '<div class="campo-wrapper">';
        $output .= '<label for="altura_ml">Altura (cm):</label>';
        $output .= '<input type="number" id="altura_ml" name="altura" min="' . esc_attr($altura_minima) . '" max="' . esc_attr($altura_maxima) . '" required>';
        $output .= '<p class="campo-info">Altura mínima: ' . esc_html($altura_minima) . 'cm, máxima: ' . esc_html($altura_maxima) . 'cm</p>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<input type="hidden" name="valor_minimo" value="' . esc_attr($valor_minimo) . '">';
        $output .= '<input type="hidden" name="preco_original" value="' . esc_attr($product->get_price()) . '">';
        $output .= '</div>';

        return $output;
    }

    private function render_quantidade($product) {
        $valores_quantidade = get_post_meta($product->get_id(), '_valores_quantidade', true);
        $valores = explode("\n", $valores_quantidade);

        $output = '<div class="grafica-rapida-quantidade">';
        $output .= '<h4>Quantidade</h4>';
        $output .= '<div class="campos-container">';
        $output .= '<div class="campo-wrapper">';
        $output .= '<label for="quantidade">Quantidade:</label>';
        $output .= '<select id="quantidade" name="quantidade">';
        foreach ($valores as $valor) {
            list($quantidade, $preco) = explode('=', trim($valor));
            $output .= '<option value="' . esc_attr($quantidade) . '" data-preco="' . esc_attr(trim($preco)) . '">' . esc_html($quantidade) . ' - R$ ' . number_format(floatval(trim($preco)), 2, ',', '.') . '</option>';
        }
        $output .= '</select>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    private function render_acabamentos($product) {
        $acabamentos_ids = get_post_meta($product->get_id(), '_acabamentos', true);
        
        if (!$acabamentos_ids || !is_array($acabamentos_ids) || empty($acabamentos_ids)) {
            return '';
        }
    
        $acabamentos_option = $this->get_acabamentos();
    
        $output = '<div class="grafica-rapida-acabamentos">';
        $output .= '<h4>Acabamentos</h4>';
        $output .= '<div class="campos-container">';
    
        foreach ($acabamentos_ids as $acabamento_id) {
            if (isset($acabamentos_option[$acabamento_id])) {
                $acabamento = $acabamentos_option[$acabamento_id];
                $output .= '<div class="acabamento-item">';
                $output .= '<input type="checkbox" id="acabamento_' . esc_attr($acabamento_id) . '" name="acabamentos[]" value="' . esc_attr($acabamento_id) . '" data-preco="' . esc_attr($acabamento['valor_adicional']) . '">';
                $output .= '<label for="acabamento_' . esc_attr($acabamento_id) . '">';
                $output .= '<span class="acabamento-nome">' . esc_html($acabamento['acabamento']) . '</span>';
                $output .= '<span class="acabamento-valor">(+R$ ' . number_format($acabamento['valor_adicional'], 2, ',', '.') . ')</span>';
                $output .= '<span class="acabamento-prazo">(' . esc_html($acabamento['prazo_adicional']) . ' dias)</span>';
                $output .= '</label>';
                $output .= '</div>';
            }
        }
    
        $output .= '</div>';
        $output .= '</div>';
    
        return $output;
    }

    private function get_acabamentos() {
        $acabamentos_obj = new Grafica_Rapida_Acabamentos();
        $acabamentos = $acabamentos_obj->get_acabamentos();
        $options = array();
        foreach ($acabamentos as $acabamento) {
            $options[$acabamento['id']] = $acabamento;
        }
        return $options;
    }

    public function criacao_arte_shortcode() {
        global $product;
        if (!$product) {
            return '';
        }

        $tem_criacao_arte = get_post_meta($product->get_id(), '_tem_criacao_arte', true);
        if ($tem_criacao_arte !== 'yes') {
            return '';
        }

        $valor_criacao_arte = get_post_meta($product->get_id(), '_valor_criacao_arte', true);

        $output = '<div class="grafica-rapida-criacao-arte">';
        $output .= '<h4>Criação de Arte</h4>';
        $output .= '<div class="campos-container">';
        $output .= '<div class="campo-wrapper opcoes-criacao-arte">';
        $output .= '<label><input type="radio" name="criacao_arte" value="sim" data-preco="' . esc_attr($valor_criacao_arte) . '"> Preciso de criação de arte (+R$ ' . number_format($valor_criacao_arte, 2, ',', '.') . ')</label>';
        $output .= '<label><input type="radio" name="criacao_arte" value="nao"> Já tenho a arte</label>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<div id="upload-arte" style="display:none;">';
        $output .= '<input type="file" name="arte_upload" accept="' . esc_attr($this->get_allowed_file_types()) . '">';
        $output .= '<button type="button" id="confirmar-upload">Confirmar e Enviar</button>';
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    public function gabaritos_shortcode() {
        global $product;
        if (!$product) {
            return '';
        }

        $gabaritos = get_post_meta($product->get_id(), '_gabaritos', true);
        if (!$gabaritos) {
            return '';
        }

        $output = '<div class="grafica-rapida-gabaritos">';
        $output .= '<h4>Gabaritos</h4>';
        $output .= '<div class="gabaritos-container">';
        foreach ($gabaritos as $gabarito) {
            $nome = pathinfo($gabarito['icone'], PATHINFO_FILENAME);
            $output .= '<div class="gabarito-item">';
            $output .= '<a href="' . esc_url($gabarito['link']) . '" target="_blank">';
            $output .= '<img src="' . esc_url(GRAFICA_RAPIDA_PLUGIN_URL . 'images/icones/' . $gabarito['icone']) . '" alt="' . esc_attr($nome) . '">';
            $output .= '<span>' . esc_html($nome) . '</span>';
            $output .= '</a>';
            $output .= '</div>';
        }
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    public function handle_upload_arte() {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $uploadedfile = $_FILES['arte'];
        
        // Gerar um nome de arquivo único
        $filename = pathinfo($uploadedfile['name'], PATHINFO_FILENAME);
        $extension = pathinfo($uploadedfile['name'], PATHINFO_EXTENSION);
        $unique_filename = $filename . '_' . time() . '_' . uniqid() . '.' . $extension;
        
        $uploadedfile['name'] = $unique_filename;

        $upload_overrides = array('test_form' => false);

        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            wp_send_json_success(array(
                'message' => 'Arquivo enviado com sucesso.',
                'filename' => $unique_filename
            ));
        } else {
            wp_send_json_error($movefile['error']);
        }
    }

    private function get_allowed_file_types() {
        $options = get_option('grafica_rapida_upload_options');
        return isset($options['allowed_types']) ? $options['allowed_types'] : 'jpg,jpeg,png,pdf';
    }
}
