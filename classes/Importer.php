<?php

class Importer
{
    public function __construct()
    {
        add_action('wp_ajax_rpi_porter_save_post', array($this, 'rpi_porter_save_post_handler'));
    }


    function rpi_porter_save_post_handler()
    {
        // Überprüfen der Nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce')) {
            wp_die('Sicherheitsüberprüfung fehlgeschlagen.');
        }

        $postMapping = $_POST['postMapping'];

        if (key_exists('route', $postMapping) )
        {
            $route = $postMapping['route'];
            $response = wp_remote_get($route);
            if (is_wp_error($response)) {
                // Handle error
                echo 'Error: ' . $response->get_error_message();
            } else {
                // Process the response
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);

            }
        }

        if (is_array($postData)) {

            foreach ($postData as $postItem) {
                // Hier rufen Sie eine Funktion auf, um jeden potenziellen Post zu verarbeiten
                $this->create_individual_post($postItem);
            }
        } else {
            wp_send_json_error('Fehler: Die Daten sind nicht im erwarteten Format.');
        }

        wp_send_json_success('Alle Posts wurden erfolgreich importiert.');
        wp_die();
    }


// Function to extract pagination links from headers
    function get_pagination_links($response) {
        $pagination_links = array();

        $header_links = wp_remote_retrieve_header($response, 'link');
        if ($header_links) {
            $matches = array();
            preg_match_all('/<([^>]+)>;\s*rel="([^"]+)"/', $header_links, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $pagination_links[$match[2]] = $match[1];
            }
        }

        return $pagination_links;
    }


    function create_individual_post($postItem)
    {

        $postData = $postItem;

// TODO : Save all created post,  tax and term ids in var and return them in response to delete them if the user wishes to do so

        $arr = array(
            'post_author' => 1, // oder einen dynamischen Autor
            'post_content' => $postData['content']['rendered'],
            'post_title' => $postData['title']['rendered'],
            'post_status' => 'publish',
            'post_type' => $postData['type'],
            'meta_input' => array(
                'import_id' => $postData['id'], // oder eine andere eindeutige ID
            ),
            'tax_input' => [],
        );

        if (array_key_exists('acf', $postData)) {
            foreach ($postData['acf'] as $post_meta_key => $post_meta_value) {
                $arr['meta_input'][$post_meta_key] = $post_meta_value;
            }
        }

        if (array_key_exists('wp_term', $postData)) {
            $wp_terms = $postData['wp_term'];
            foreach ($wp_terms as $wp_term) {
                $taxonomy_slug = $wp_term['taxonomy'];
                if (!taxonomy_exists($taxonomy_slug)) {
                    register_taxonomy(sanitize_key($taxonomy_slug), $postData['type']);
                }

                // Senden der Anfrage
                $response = wp_remote_get($wp_term['href']);

                // Überprüfen, ob die Anfrage erfolgreich war
                if (is_wp_error($response)) {
                    // Fehlerbehandlung
                    error_log('Fehler bei der Anfrage: ' . $response->get_error_message());
                    return;
                }

                // Verarbeiten der Antwort
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body);

                if (!term_exists($data['slug'], $taxonomy_slug)) {
                    wp_create_term($data['slug'], $taxonomy_slug);
                }
                $arr['tax_input'][$taxonomy_slug] = $data['slug'];


            }

        }

        $post_id = wp_insert_post($arr);

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