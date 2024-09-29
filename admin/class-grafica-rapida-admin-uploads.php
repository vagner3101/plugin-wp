<?php

class Grafica_Rapida_Admin_Uploads {
    private $options;

    public function init() {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_notices', array($this, 'admin_notices'));
        $this->options = get_option('grafica_rapida_upload_options');
    }

    public function register_settings() {
        register_setting('grafica_rapida_upload_options', 'grafica_rapida_upload_options', array($this, 'sanitize_options'));

        add_settings_section('upload_settings', 'Configurações de Upload', array($this, 'section_info'), 'grafica-rapida-uploads');

        add_settings_field('allowed_types', 'Tipos de arquivos permitidos', array($this, 'allowed_types_callback'), 'grafica-rapida-uploads', 'upload_settings');
        add_settings_field('max_size', 'Tamanho máximo de upload (MB)', array($this, 'max_size_callback'), 'grafica-rapida-uploads', 'upload_settings');
        add_settings_field('delete_after', 'Excluir arquivos após (dias)', array($this, 'delete_after_callback'), 'grafica-rapida-uploads', 'upload_settings');
    }

    public function admin_notices() {
        if (isset($_GET['page']) && $_GET['page'] == 'grafica-rapida-uploads' && isset($_GET['settings-updated'])) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e('Configurações salvas com sucesso.', 'grafica-rapida-plugin'); ?></p>
            </div>
            <?php
        }
    }

    public function sanitize_options($input) {
        $new_input = array();
        $new_input['allowed_types'] = sanitize_text_field($input['allowed_types']);
        $new_input['max_size'] = absint($input['max_size']);
        $new_input['delete_after'] = absint($input['delete_after']);
        return $new_input;
    }

    public function section_info() {
        echo 'Configure as opções de upload abaixo:';
    }

    public function allowed_types_callback() {
        $value = isset($this->options['allowed_types']) ? $this->options['allowed_types'] : '';
        echo "<input type='text' name='grafica_rapida_upload_options[allowed_types]' value='{$value}' />";
        echo "<p class='description'>Separe os tipos de arquivo por vírgula (ex: jpg,png,pdf)</p>";
    }

    public function max_size_callback() {
        $value = isset($this->options['max_size']) ? $this->options['max_size'] : '';
        echo "<input type='number' name='grafica_rapida_upload_options[max_size]' value='{$value}' min='1' />";
    }

    public function delete_after_callback() {
        $value = isset($this->options['delete_after']) ? $this->options['delete_after'] : '';
        echo "<input type='number' name='grafica_rapida_upload_options[delete_after]' value='{$value}' min='1' />";
    }

    public function render_page() {
        ?>
        <div class="wrap grafica-rapida-uploads">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('grafica_rapida_upload_options');
                do_settings_sections('grafica-rapida-uploads');
                submit_button();
                ?>
            </form>

            <h2>Teste de Upload</h2>
            <form method="post" enctype="multipart/form-data" class="upload-test-section">
                <input type="file" name="test_upload" />
                <?php submit_button('Testar Upload', 'secondary', 'test_upload_submit'); ?>
            </form>

            <?php $this->handle_test_upload(); ?>

            <h2>Exclusão Manual de Arquivos Antigos</h2>
            <form method="post" class="manual-deletion-section">
                <input type="number" name="days_old" min="1" value="30" />
                <?php submit_button('Excluir Arquivos Antigos', 'secondary', 'delete_old_files'); ?>
            </form>

            <?php $this->handle_manual_deletion(); ?>
        </div>
        <?php
    }

    private function handle_test_upload() {
        if (isset($_POST['test_upload_submit']) && isset($_FILES['test_upload'])) {
            $file = $_FILES['test_upload'];
            $upload_dir = wp_upload_dir();
            $upload_folder = 'grafica-rapida-uploads';
            $target_dir = trailingslashit($upload_dir['basedir']) . $upload_folder;

            // Verificar o tipo de arquivo
            $allowed_types = isset($this->options['allowed_types']) ? explode(',', $this->options['allowed_types']) : array('jpg', 'jpeg', 'png');
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($file_extension, $allowed_types)) {
                echo "<div class='upload-result'>";
                echo "<p class='error-message'>Tipo de arquivo não permitido. Tipos permitidos: " . implode(', ', $allowed_types) . "</p>";
                echo "</div>";
                return;
            }

            // Verificar o tamanho do arquivo
            $max_size = isset($this->options['max_size']) ? $this->options['max_size'] * 1024 * 1024 : 5 * 1024 * 1024; // Converter MB para bytes
            if ($file['size'] > $max_size) {
                echo "<div class='upload-result'>";
                echo "<p class='error-message'>O arquivo é muito grande. Tamanho máximo permitido: " . size_format($max_size) . "</p>";
                echo "</div>";
                return;
            }

            // Criar a pasta de uploads se não existir
            if (!file_exists($target_dir)) {
                wp_mkdir_p($target_dir);
            }

            $target_file = $target_dir . '/' . basename($file['name']);
            $upload_result = move_uploaded_file($file['tmp_name'], $target_file);

            echo "<div class='upload-result'>";
            echo "<h3>Resultado do Upload:</h3>";
            if ($upload_result) {
                echo "<p class='success-message'>Upload bem-sucedido!</p>";
                echo "<p>Tipo de arquivo: " . $file['type'] . "</p>";
                echo "<p>Tamanho: " . size_format($file['size']) . "</p>";
                echo "<p>Salvo em: " . $upload_folder . '/' . basename($file['name']) . "</p>";
            } else {
                echo "<p class='error-message'>Falha no upload. Por favor, tente novamente.</p>";
            }
            echo "</div>";
        }
    }

    private function handle_manual_deletion() {
        if (isset($_POST['delete_old_files'])) {
            $days_old = intval($_POST['days_old']);
            $deleted_count = $this->delete_old_files($days_old);
            echo "<div class='deletion-result'>";
            echo "<p>Foram excluídos {$deleted_count} arquivos com mais de {$days_old} dias.</p>";
            echo "</div>";
        }
    }

    private function delete_old_files($days) {
        $upload_dir = wp_upload_dir();
        $upload_folder = 'grafica-rapida-uploads';
        $target_dir = trailingslashit($upload_dir['basedir']) . $upload_folder;
        $files = glob($target_dir . '*');
        $now = time();
        $deleted_count = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= $days * 24 * 60 * 60) {
                    unlink($file);
                    $deleted_count++;
                }
            }
        }

        return $deleted_count;
    }
}
