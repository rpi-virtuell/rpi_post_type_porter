<?php
/**
 * Plugin Name: RPI Export-Import
 * Plugin URI: http://beispiel.de/plugins/places-export-import-plugin
 * Description: Exportiert und importiert einen Beitragstyp inklusive ACF-Felder zwischen WordPress-Instanzen und wandelt Google Maps Koordinaten in Leaflet JS Koordinaten um.
 * Version: 1.0
 * Author: Daniel Reintanz
 * License: GPLv2
 */

// Sicherstellen, dass das Plugin nicht direkt aufgerufen wird
defined('ABSPATH') or die('Direkter Zugriff auf Skripte ist nicht erlaubt.');

// Laden der Klassen
require_once plugin_dir_path(__FILE__) . 'classes/Importer.php';
require_once plugin_dir_path(__FILE__) . 'classes/Settings.php';

class RpiPostTypePorter
{

    public function __construct()
    {
        $settings = new Settings();
        add_action('admin_menu', array($settings, 'add_settings_page'));

        wp_enqueue_script('jquery-ui-accordion');
        add_action('admin_enqueue_scripts', 'enqueue_custom_admin_script');

        function enqueue_custom_admin_script()
        {
            wp_enqueue_script('dynamic_fields', plugin_dir_url(__FILE__) . 'assets/js/dynamic-fields.js', array('jquery'), null, true);
            wp_enqueue_script('rpi-porter-api-request', plugin_dir_url(__FILE__) . 'assets/js/api-request.js', array('jquery'), null, true);

            $post_types = get_post_types();
            $post_status = get_post_statuses();
            $post_authors = get_users();
            foreach ($post_authors as $author) {
                $post_authors_arr[$author->ID] = $author->user_login;
            }
            wp_localize_script('rpi-porter-api-request', 'post_type', $post_types);
            wp_localize_script('rpi-porter-api-request', 'post_status', $post_status);
            wp_localize_script('rpi-porter-api-request', 'post_author', $post_authors_arr);

            wp_enqueue_style('rpi-porter-admin-styles', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css');
        }


    }

}

new RpiPostTypePorter();