<?php
/*
Plugin Name: Places Export-Import Plugin
Plugin URI: http://beispiel.de/plugins/places-export-import-plugin
Description: Exportiert und importiert den Beitragstyp  inklusive ACF-Felder zwischen WordPress-Instanzen und wandelt Google Maps Koordinaten in Leaflet JS Koordinaten um.
Version: 1.0
Author: Daniel Reintanz
License: GPLv2
*/

// Sicherstellen, dass das Plugin nicht direkt aufgerufen wird
defined('ABSPATH') or die('Direkter Zugriff auf Skripte ist nicht erlaubt.');

// Laden der Klassen
require_once plugin_dir_path(__FILE__) . 'classes/Exporter.php';
require_once plugin_dir_path(__FILE__) . 'classes/Importer.php';
require_once plugin_dir_path(__FILE__) . 'classes/Settings.php';

class RpiPostTypePorter
{

    public function __construct()
    {
        $settings = new Settings();
        add_action('admin_menu', array($settings, 'add_settings_page'));

        add_action('admin_enqueue_scripts', 'enqueue_custom_admin_script');

        function enqueue_custom_admin_script() {
            wp_enqueue_script('my_custom_script', plugin_dir_url(__FILE__) . 'assets/js/dynamic-fields.js', array('jquery'), null, true);
        }


    }

}
new RpiPostTypePorter();