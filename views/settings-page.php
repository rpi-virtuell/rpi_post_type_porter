<?php
if (!current_user_can('manage_options')) {
    return;
}

// Holen Sie alle verfügbaren öffentlichen Beitragstypen
$post_types = get_post_types(['public' => true], 'objects');
?>

<div class="wrap">
    <h1>Places Export-Import Einstellungen</h1>
    <form method="post" action="options.php">
        <?php settings_fields('places_export_import_options_group'); ?>
        <?php do_settings_sections('places_export_import_options_group'); ?>

        <h2>Beitragstyp auswählen</h2>
        <select name="selected_post_type" id="selected_post_type">
            <?php foreach ($post_types as $post_type): ?>
                <option value="<?php echo esc_attr($post_type->name); ?>">
                    <?php echo esc_html($post_type->labels->name); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <h2>ACF-Feldgruppe auswählen</h2>
        <select name="selected_acf_group" id="selected_acf_group">
            <option value="">Bitte wählen Sie zuerst einen Beitragstyp</option>
        </select>

        <?php submit_button(); ?>
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#selected_post_type').change(function() {
            var postType = $(this).val();
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_acf_field_groups',
                    post_type: postType
                },
                success: function(response) {
                    var $fieldGroupSelect = $('#selected_acf_group');
                    $fieldGroupSelect.empty();

                    $.each(response, function(i, group) {
                        $fieldGroupSelect.append($('<option>', {
                            value: group.key,
                            text: group.title
                        }));
                    });
                }
            });
        });
    });
</script>
