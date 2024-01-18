<?php
class Importer
{
    public function __construct()
    {
        add_action('wp_ajax_rpi_porter_save_post', array($this, 'rpi_porter_save_post_handler'));
    }


    function rpi_porter_save_post_handler() {
        $postData = $_POST['postData'];

        // Überprüfen der Nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce')) {
            wp_die('Sicherheitsüberprüfung fehlgeschlagen.');
        }

        $post_data = array(
            'post_author' => 1, // oder einen dynamischen Autor
            'post_content' => $postData['content']['rendered'],
            'post_title' => $postData['title']['rendered'],
            'post_status' => 'publish',
            'post_type' => 'news',
            'meta_input' => array(
                'import_id' => $postData['id'], // oder eine andere eindeutige ID
            ),
        );
        
        $post_id  = wp_insert_post($post_data);

        // Überprüfen, ob der Post erfolgreich erstellt wurde
        if ($post_id !== 0) {
            // Erfolgreich
            wp_send_json_success('Post erfolgreich erstellt.');
        } else {
            // Fehler
            wp_send_json_error('Fehler beim Erstellen des Posts.');
        }

        wp_die();
    }
}