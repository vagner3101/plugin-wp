<?php

class Grafica_Rapida_Admin_Product_Fields {

    public function init() {
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_custom_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_fields'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function add_custom_fields() {
        global $woocommerce, $post;

        echo '<div class="options_group">';

        // Tipo de Venda
        woocommerce_wp_select(array(
            'id' => '_tipo_venda',
            'label' => 'Tipo de Venda',
            'options' => array(
                'nenhum' => 'Nenhum',
                'metro_quadrado' => 'Metro Quadrado',
                'metro_linear' => 'Metro Linear',
                'quantidade' => 'Quantidade',
                'acabamentos' => 'Acabamentos'
            ),
            'desc_tip' => true,
            'description' => 'Selecione o tipo de venda para este produto.'
        ));

        // Campos para Metro Quadrado
        echo '<div class="metro_quadrado_fields">';
        woocommerce_wp_text_input(array(
            'id' => '_valor_minimo_m2',
            'label' => 'Valor Mínimo (M²)',
            'type' => 'number',
            'custom_attributes' => array('step' => '0.01', 'min' => '0')
        ));
        woocommerce_wp_text_input(array(
            'id' => '_largura_minima_m2',
            'label' => 'Largura Mínima (cm)',
            'type' => 'number',
            'custom_attributes' => array('step' => '1', 'min' => '0')
        ));
        woocommerce_wp_text_input(array(
            'id' => '_largura_maxima_m2',
            'label' => 'Largura Máxima (cm)',
            'type' => 'number',
            'custom_attributes' => array('step' => '1', 'min' => '0')
        ));
        echo '</div>';

        // Campos para Metro Linear
        echo '<div class="metro_linear_fields">';
        woocommerce_wp_text_input(array(
            'id' => '_valor_minimo_ml',
            'label' => 'Valor Mínimo (ML)',
            'type' => 'number',
            'custom_attributes' => array('step' => '0.01', 'min' => '0')
        ));
        woocommerce_wp_text_input(array(
            'id' => '_larguras_padrao_ml',
            'label' => 'Larguras Padrão',
            'description' => 'Insira as larguras padrão separadas por /',
            'desc_tip' => true,
        ));
        woocommerce_wp_text_input(array(
            'id' => '_altura_minima_ml',
            'label' => 'Altura Mínima (cm)',
            'type' => 'number',
            'custom_attributes' => array('step' => '1', 'min' => '0')
        ));
        woocommerce_wp_text_input(array(
            'id' => '_altura_maxima_ml',
            'label' => 'Altura Máxima (cm)',
            'type' => 'number',
            'custom_attributes' => array('step' => '1', 'min' => '0')
        ));
        echo '</div>';

        // Campo para Quantidade
        echo '<div class="quantidade_fields">';
        woocommerce_wp_textarea_input(array(
            'id' => '_valores_quantidade',
            'label' => 'Valores por Quantidade',
            'description' => 'Insira os valores no formato "quantidade = valor", um por linha',
            'desc_tip' => true,
        ));
        echo '</div>';

        // Campo para Acabamentos
        echo '<div class="acabamentos_fields">';
        $acabamentos = $this->get_acabamentos();
        $acabamentos_selecionados = get_post_meta($post->ID, '_acabamentos', true);
        if (!is_array($acabamentos_selecionados)) {
            $acabamentos_selecionados = array();
        }
        echo '<p class="form-field"><label for="_acabamentos">Acabamentos</label>';
        echo '<select multiple="multiple" class="select2" name="_acabamentos[]" id="_acabamentos" style="width: 50%;">';
        foreach ($acabamentos as $id => $acabamento) {
            echo '<option value="' . esc_attr($id) . '" ' . (in_array($id, $acabamentos_selecionados) ? 'selected="selected"' : '') . '>' . esc_html($acabamento['acabamento']) . ' - R$ ' . number_format($acabamento['valor_adicional'], 2, ',', '.') . ' - ' . $acabamento['prazo_adicional'] . ' dias</option>';
        }
        echo '</select></p>';
        echo '</div>';

        echo '<div class="grafica-rapida-divider"></div>';

        // Criação de Arte
        woocommerce_wp_checkbox(array(
            'id' => '_tem_criacao_arte',
            'label' => 'Tem Criação de Arte?',
            'description' => 'Marque se este produto tem opção de criação de arte',
            'value' => get_post_meta($post->ID, '_tem_criacao_arte', true) === 'yes' ? 'yes' : 'no',
        ));
        woocommerce_wp_text_input(array(
            'id' => '_valor_criacao_arte',
            'label' => 'Valor da Criação de Arte',
            'type' => 'number',
            'custom_attributes' => array('step' => '0.01', 'min' => '0'),
            'value' => get_post_meta($post->ID, '_valor_criacao_arte', true),
        ));

        echo '<div class="grafica-rapida-divider"></div>';

        // Gabaritos
        echo '<div class="gabaritos_fields">';
        echo '<h4>Gabaritos</h4>';
        echo '<div id="gabaritos_container">';
        $gabaritos = get_post_meta($post->ID, '_gabaritos', true);
        $count = 0;
        if ($gabaritos) {
            foreach ($gabaritos as $gabarito) {
                echo $this->gabarito_field($count, $gabarito);
                $count++;
            }
        }
        echo '</div>';
        echo '<button type="button" class="button add_gabarito">Adicionar Gabarito</button>';
        echo '</div>';

        echo '</div>';
    }

    public function gabarito_field($count, $gabarito = array()) {
        $icones = $this->get_icones();
        ob_start();
        ?>
        <div class="gabarito_field">
            <select name="gabaritos[<?php echo $count; ?>][icone]" class="gabarito-icone-select">
                <?php foreach ($icones as $icone) : 
                    $nome_icone = pathinfo($icone, PATHINFO_FILENAME); // Remove a extensão do arquivo
                ?>
                    <option value="<?php echo esc_attr($icone); ?>" <?php selected(isset($gabarito['icone']) ? $gabarito['icone'] : '', $icone); ?> data-imagem="<?php echo GRAFICA_RAPIDA_PLUGIN_URL . 'images/icones/' . $icone; ?>"><?php echo esc_html($nome_icone); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="gabaritos[<?php echo $count; ?>][link]" value="<?php echo isset($gabarito['link']) ? esc_attr($gabarito['link']) : ''; ?>" placeholder="Link de Download">
            <img src="" alt="Miniatura do ícone" class="gabarito-icone-preview">
            <button type="button" class="button remove_gabarito">Remover</button>
        </div>
        <?php
        return ob_get_clean();
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

    private function get_icones() {
        $icones_dir = GRAFICA_RAPIDA_PLUGIN_DIR . 'images/icones/';
        $icones = array_diff(scandir($icones_dir), array('..', '.'));
        return $icones;
    }

    public function save_custom_fields($post_id) {
        $fields = array(
            '_tipo_venda',
            '_valor_minimo_m2',
            '_largura_minima_m2',
            '_largura_maxima_m2',
            '_valor_minimo_ml',
            '_larguras_padrao_ml',
            '_altura_minima_ml',
            '_altura_maxima_ml',
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        // Salvar campo de criação de arte
        $tem_criacao_arte = isset($_POST['_tem_criacao_arte']) ? 'yes' : 'no';
        update_post_meta($post_id, '_tem_criacao_arte', $tem_criacao_arte);

        if (isset($_POST['_valor_criacao_arte'])) {
            update_post_meta($post_id, '_valor_criacao_arte', sanitize_text_field($_POST['_valor_criacao_arte']));
        }

        // Salvar valores de quantidade
        if (isset($_POST['_valores_quantidade'])) {
            $valores_quantidade = sanitize_textarea_field($_POST['_valores_quantidade']);
            $valores_quantidade = preg_replace('/\r\n/', "\n", $valores_quantidade);
            update_post_meta($post_id, '_valores_quantidade', $valores_quantidade);
        }

        // Salvar acabamentos
        if (isset($_POST['_acabamentos'])) {
            $acabamentos = array_map('intval', $_POST['_acabamentos']);
            update_post_meta($post_id, '_acabamentos', $acabamentos);
        }

        if (isset($_POST['gabaritos'])) {
            $gabaritos = array();
            foreach ($_POST['gabaritos'] as $gabarito) {
                $gabaritos[] = array(
                    'icone' => sanitize_text_field($gabarito['icone']),
                    'link' => esc_url_raw($gabarito['link'])
                );
            }
            update_post_meta($post_id, '_gabaritos', $gabaritos);
        }
    }

    public function enqueue_scripts() {
        wp_enqueue_script('grafica-rapida-admin-product', GRAFICA_RAPIDA_PLUGIN_URL . 'admin/js/admin-product.js', array('jquery'), GRAFICA_RAPIDA_VERSION, true);
        wp_enqueue_style('grafica-rapida-admin-product', GRAFICA_RAPIDA_PLUGIN_URL . 'admin/css/admin-product.css', array(), GRAFICA_RAPIDA_VERSION);
    }
}
