<?php

class Grafica_Rapida_Acabamentos {
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'grafica_rapida_acabamentos';
    }

    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            acabamento varchar(255) NOT NULL,
            valor_adicional decimal(10,2) NOT NULL,
            prazo_adicional int(11) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function add_acabamento($acabamento, $valor_adicional, $prazo_adicional) {
        global $wpdb;

        return $wpdb->insert(
            $this->table_name,
            array(
                'acabamento' => $acabamento,
                'valor_adicional' => $valor_adicional,
                'prazo_adicional' => $prazo_adicional
            ),
            array('%s', '%f', '%d')
        );
    }

    public function update_acabamento($id, $acabamento, $valor_adicional, $prazo_adicional) {
        global $wpdb;

        return $wpdb->update(
            $this->table_name,
            array(
                'acabamento' => $acabamento,
                'valor_adicional' => $valor_adicional,
                'prazo_adicional' => $prazo_adicional
            ),
            array('id' => $id),
            array('%s', '%f', '%d'),
            array('%d')
        );
    }

    public function delete_acabamento($id) {
        global $wpdb;

        return $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );
    }

    public function get_acabamentos() {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM $this->table_name ORDER BY acabamento ASC", ARRAY_A);
    }

    public function get_acabamento($id) {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->table_name WHERE id = %d", $id), ARRAY_A);
    }
}
