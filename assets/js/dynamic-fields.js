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

// TODO: Add progressbar and Button
