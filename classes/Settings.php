<?php
class Settings {

public function __construct()
{
    add_action('wp_ajax_get_acf_field_groups', 'get_acf_field_groups_for_post_type');

    function get_acf_field_groups_for_post_type() {
        $post_type = $_POST['post_type'];

        // Holen Sie die ACF-Feldgruppen für den ausgewählten Beitragstyp
        $field_groups = acf_get_field_groups(['post_type' => $post_type]);

        wp_send_json($field_groups);
    }
}

    public function add_settings_page() {
        add_options_page(
            'Places Export-Import Einstellungen',
            'Places Export-Import',
            'manage_options',
            'places-export-import',
            array($this, 'render_settings_page')
        );
    }


    public function render_settings_page() {
        // Holen Sie alle verfügbaren Beitragstypen
        $post_types = get_post_types(['public' => true], 'objects');
        // Holen Sie alle ACF-Feldgruppen
        $field_groups = acf_get_field_groups();

        require_once plugin_dir_path(__FILE__) . '../views/settings-page.php';
    }
}
?>
