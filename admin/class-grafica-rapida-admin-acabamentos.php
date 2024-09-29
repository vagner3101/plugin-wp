<?php

class Grafica_Rapida_Admin_Acabamentos {
    private $acabamentos;

    public function __construct() {
        $this->acabamentos = new Grafica_Rapida_Acabamentos();
    }

    public function init() {
        add_action('admin_post_grafica_rapida_save_acabamento', array($this, 'save_acabamento'));
        add_action('wp_ajax_grafica_rapida_delete_acabamento', array($this, 'delete_acabamento'));
    }

    public function render_page() {
        $acabamento_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
        $acabamento = $acabamento_id ? $this->acabamentos->get_acabamento($acabamento_id) : null;
        ?>
        <div class="wrap grafica-rapida-acabamentos" style="width: 80%;">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php
            if (isset($_GET['updated'])) {
                echo '<div class="notice notice-success is-dismissible"><p>Acabamento atualizado com sucesso.</p></div>';
            } elseif (isset($_GET['added'])) {
                echo '<div class="notice notice-success is-dismissible"><p>Novo acabamento adicionado com sucesso.</p></div>';
            } elseif (isset($_GET['deleted'])) {
                echo '<div class="notice notice-success is-dismissible"><p>Acabamento excluído com sucesso.</p></div>';
            }
            ?>
            
            <button id="toggle-form" class="button button-primary"><?php echo $acabamento ? 'Editar Acabamento' : 'Adicionar Novo Acabamento'; ?></button>
            
            <form id="acabamento-form" style="display: <?php echo $acabamento ? 'block' : 'none'; ?>;" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="grafica_rapida_save_acabamento">
                <?php wp_nonce_field('grafica_rapida_acabamento_nonce', 'grafica_rapida_acabamento_nonce'); ?>
                <input type="hidden" name="acabamento_id" value="<?php echo $acabamento_id; ?>">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="acabamento">Acabamento</label></th>
                        <td><input type="text" name="acabamento" id="acabamento" class="regular-text" value="<?php echo $acabamento ? esc_attr($acabamento['acabamento']) : ''; ?>" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="valor_adicional">Valor Adicional (R$)</label></th>
                        <td><input type="number" name="valor_adicional" id="valor_adicional" class="regular-text" step="0.01" min="0" value="<?php echo $acabamento ? esc_attr($acabamento['valor_adicional']) : ''; ?>" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="prazo_adicional">Prazo Adicional (dias)</label></th>
                        <td><input type="number" name="prazo_adicional" id="prazo_adicional" class="regular-text" min="0" value="<?php echo $acabamento ? esc_attr($acabamento['prazo_adicional']) : ''; ?>" required></td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $acabamento ? 'Atualizar Acabamento' : 'Adicionar Acabamento'; ?>">
                </p>
            </form>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Acabamento</th>
                        <th>Valor Adicional</th>
                        <th>Prazo Adicional</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $acabamentos = $this->acabamentos->get_acabamentos();
                    foreach ($acabamentos as $acabamento) {
                        echo '<tr>';
                        echo '<td>' . esc_html($acabamento['acabamento']) . '</td>';
                        echo '<td>R$ ' . number_format($acabamento['valor_adicional'], 2, ',', '.') . '</td>';
                        echo '<td>' . esc_html($acabamento['prazo_adicional']) . ' dias</td>';
                        echo '<td>';
                        echo '<a href="' . add_query_arg('edit', $acabamento['id']) . '" class="button">Editar</a> ';
                        echo '<button class="button delete-acabamento" data-id="' . esc_attr($acabamento['id']) . '">Excluir</button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#toggle-form').click(function() {
                $('#acabamento-form').toggle();
            });

            $('.delete-acabamento').click(function() {
                if (confirm('Tem certeza que deseja excluir este acabamento?')) {
                    var id = $(this).data('id');
                    $.post(ajaxurl, {
                        action: 'grafica_rapida_delete_acabamento',
                        id: id,
                        _ajax_nonce: '<?php echo wp_create_nonce('grafica_rapida_delete_acabamento'); ?>'
                    }, function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Erro ao excluir o acabamento.');
                        }
                    });
                }
            });
        });
        </script>
        <?php
    }

    public function save_acabamento() {
        if (!isset($_POST['grafica_rapida_acabamento_nonce']) || !wp_verify_nonce($_POST['grafica_rapida_acabamento_nonce'], 'grafica_rapida_acabamento_nonce')) {
            wp_die('Ação não autorizada.');
        }

        $acabamento_id = isset($_POST['acabamento_id']) ? intval($_POST['acabamento_id']) : 0;
        $acabamento = sanitize_text_field($_POST['acabamento']);
        $valor_adicional = floatval($_POST['valor_adicional']);
        $prazo_adicional = intval($_POST['prazo_adicional']);

        if ($acabamento_id) {
            $this->acabamentos->update_acabamento($acabamento_id, $acabamento, $valor_adicional, $prazo_adicional);
            $redirect = add_query_arg('updated', 'true', admin_url('admin.php?page=grafica-rapida-acabamentos'));
        } else {
            $this->acabamentos->add_acabamento($acabamento, $valor_adicional, $prazo_adicional);
            $redirect = add_query_arg('added', 'true', admin_url('admin.php?page=grafica-rapida-acabamentos'));
        }

        wp_redirect($redirect);
        exit;
    }

    public function delete_acabamento() {
        check_ajax_referer('grafica_rapida_delete_acabamento');

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id) {
            $result = $this->acabamentos->delete_acabamento($id);
            wp_send_json_success($result);
        } else {
            wp_send_json_error('ID de acabamento inválido.');
        }
    }
}
