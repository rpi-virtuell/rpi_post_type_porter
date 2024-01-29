jQuery(document).ready(function ($) {
    $('#request_data_button').click(function () {
        var apiRoute = $('#api_route').val();
        $.ajax({
            url: apiRoute,
            method: 'GET',
            success: function (data, textStatus, xhr) {
                // Zugriff auf die Header
                const total = xhr.getResponseHeader('X-WP-Total');
                displayDataForEditing(data, total);
            },
            error: function () {
                alert('Fehler beim Abrufen der Daten. Bitte überprüfen Sie die URL.');
            }
        });
    });

    function displayDataForEditing(data, total) {
        if ("id" in data) {
            var html = iterateThroughData(data);
            $('#data-display').html(html);
        } else {

            var html = '<h2>' + total + ' Posts gefunden</h2>';
            html += iterateThroughData(data[0]);
            html += '<form id="import-form" method="post">\n' +
                '    <input type="button" id="import-button" class="button button-primary" value="Importieren">\n' +
                '</form>'
            $('#data-display').html(html);
        }
    }

    function iterateThroughData(data) {
        var html = '<div class="rpi-porter-data-container">';


        var routeMapping = {"status": post_status, "type": post_type, "author": post_author,};
        var ignoreDataFields = ['content', 'title', 'link'];
        console.log(routeMapping);
        for (var key in data) {
            if (routeMapping[key] !== undefined) {
                html += '<label class="'+key+'">'+key+'</label></br>';
                html += '<select name="'+key+'">';
                for (var post_data_key in routeMapping[key]){
                    html += '<option value="'+post_data_key+'">'+routeMapping[key][post_data_key]+'</option>'
                }
                html += '</select>';
                break;
            }
            if (routeMapping[key] === undefined && !ignoreDataFields.includes(key)  && data[key] !== null && typeof data[key] === 'object') {
                html += '<div class="rpi-porter-object-container"><strong class="rpi-porter-object-title">' + key + '</strong>';
                html += '<div class="rpi-porter-object-content">';
                html += iterateThroughData(data[key]);
                html += '</div></div>';
            }
            else {
                html += createInputRow(key, data[key]);
            }

        }
        html += '</div>';

        return html;
    }

    function createInputRow(key, value) {
        return '<div class="rpi-porter-entry-row">' +
            '<input type="checkbox" checked class="rpi-porter-import-check"> ' +
            '<input type="text" class="rpi-porter-old-key" value="' + key + '" disabled> ' +
            '<input type="text" class="rpi-porter-new-key" value="' + key + '"> ' +
            '<span>' + value + '</span>' +
            '</div>';
    }


    jQuery(document).ready(function ($) {
        $('#import-button').click(function () {
            var postMapping = collectDataForImport();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'rpi_porter_save_post',
                    postMapping: postMapping,
                    nonce: my_ajax_obj.nonce // Stellen Sie sicher, dass die Nonce übergeben wird
                },
                success: function (response) {
                    alert('Import erfolgreich!');
                },
                error: function () {
                    alert('Fehler beim Import.');
                }
            });
        });
    });

    function collectDataForImport() {
        var importedData = [];

        $('.rpi-porter-data-container').each(function () {
            var postMapping = {
                'route': $(this).find('#api_route').val()
            };

            $(this).find('.rpi-porter-entry-row').each(function () {
                if ($(this).find('.rpi-porter-import-check').is(':checked')) {
                    postMapping[$(this).find('.rpi-porter-old-key').val()] = $(this).find('.rpi-porter-new-key').val();
                }
            });

            if (!$.isEmptyObject(postMapping)) {
                importedData.push(postMapping);
            }
        });

        return importedData;
    }

});
