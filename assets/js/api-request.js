jQuery(document).ready(function ($) {
    $('#request_data_button').click(function () {
        var apiRoute = $('#api_route').val();
        $.ajax({
            url: apiRoute,
            method: 'GET',
            success: function (response) {
                displayDataForEditing(response);
            },
            error: function () {
                alert('Fehler beim Abrufen der Daten. Bitte überprüfen Sie die URL.');
            }
        });
    });

    function displayDataForEditing(data) {
        var html = iterateThroughData(data);
        $('#data-display').html(html);
    }

    function iterateThroughData(data) {
        var html = '<div class="rpi-porter-data-container">';
        for (var key in data) {

                if (data[key] !== null && typeof data[key] === 'object') {
                    html += '<div class="rpi-porter-object-container"><strong class="rpi-porter-object-title">' + key + '</strong>';
                    html += '<div class="rpi-porter-object-content">';
                    html += iterateThroughData(data[key]);
                    html += '</div></div>';
                } else {
                    html += createInputRow(key, data[key]);
                }

        }
        html += '</div>';
        return html;
    }

    function createInputRow(key, value) {
        return '<div class="rpi-porter-entry-row">' +
            '<input type="checkbox" checked> ' +
            '<input type="text" value="' + key + '" disabled> ' +
            '<input type="text" value="' + key + '"> ' +
            '<span>' + value + '</span>' +
            '</div>';
    }

    function saveDataAsPost(data) {
        $.ajax({
            url: my_ajax_obj.ajax_url,
            method: 'POST',
            data: {
                action: 'rpi_porter_save_post',
                postData: data,
                nonce: my_ajax_obj.nonce // Nonce einfügen
            },
            success: function (response) {
                alert('Post erfolgreich gespeichert!');
            },
            error: function () {
                alert('Fehler beim Speichern des Posts.');
            }
        });
    }


});
