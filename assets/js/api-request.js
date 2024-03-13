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
            html += '<div class="rpi-porter-progress"></div>'
            $('#data-display').html(html);
        }
    }

    function iterateThroughData(data, recursiv = false) {
        var html = '<div class="rpi-porter-data-container">';


        var routeMapping = {"status": post_status, "type": post_type, "author": post_author,};
        var dataFields = ['status', 'type', 'author', 'acf', 'meta', '_links', 'wp:attachment', 'wp:term', 'version_history'];
        for (var key in data) {
            if (dataFields.includes(key) || recursiv) {
                if (routeMapping[key] !== undefined) {
                    html += '<label class="' + key + '"><strong>' + key + '</strong></label></br>';
                    html += '<select class="rpi-porter-select-box" name="' + key + '">';
                    for (var post_data_key in routeMapping[key]) {
                        html += '<option value="' + post_data_key + '">' + routeMapping[key][post_data_key] + '</option>'
                    }
                    html += '</select></br>';
                    continue;
                }
                if (routeMapping[key] === undefined && data[key] !== null && typeof data[key] === 'object') {
                    html += '<div class="rpi-porter-object-container"><strong class="rpi-porter-object-title">' + key + '</strong>';
                    html += '<div class="rpi-porter-object-content">';
                    html += iterateThroughData(data[key], true);
                    html += '</div></div>';
                } else {
                    html += createInputRow(key, data[key]);
                }
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
            rpi - porter - progress
            $(this).find('.rpi-porter-progress').html += '<div class="progress-container">' +
                '<div class="progress-bar" id="myProgressBar"></div>' +
                '    </div>' +
                '    <button id="stopButton">Stop Import</button>\n';
            var postMapping = collectDataForImport(); // todo this needs to return all fields which need to be imported

            preparePostsForImport();
            //TODO get post count. Iterate thorugh data array and unset all data which is not mapped
            //   cluster posts in 5 and send to server. after received answer
            //TODO: this part needs to be in its own function to be called recursivly
            // required params are next route and field_template
            // optional param is created posts

        });
    });

    function preparePostsForImport(posts, mapping) {
        /*
        this checks if the key in post are present in mapping if not it is removed
         from the array. if the Key is present but the value of the mapping is not
         the same it is replaced by the mapping key and the old key is removed
         */

        posts.forEach(function (item, index, arr) {
            if (!index in mapping) {
                arr.splice(index, 1);
            } else {
                if (mapping[index] !== index) {
                    arr[index] = arr[mapping[index]];
                    arr.splice(index, 1);


                }

            }
        });

    }

    function send_prepared_posts_via_ajax() {
        if (data.length === totalposts) {

            console.log('Import abgeschlossen')
            return;
        }
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
                updateProgressBar();

                //TODO ADD update Progress bar logic
                //TODO: check if stop  button has been pressed
                // if not recall save post function with passed data from response
            },
            error: function () {
                alert('Fehler beim Import.');
            },
            complete: function () {
                send_prepared_posts_via_ajax();
            }
        });

    }

    function updateProgressBar() {
        const progress = (importedData / totalData) * 100;
        progressBar.style.width = `${progress}%`;

        if (importedData === totalData) {
            alert('Data import complete!');
            importInProgress = false;
        }
    }


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
                if ($(this).find('.rpi-porter-select-box').length) {
                    postMapping[$(this).find('.rpi-porter-select-box').name] = $(this).find('.rpi-porter-select-box').val();
                }
            });

            if (!$.isEmptyObject(postMapping)) {
                importedData.push(postMapping);
            }
        });

        return importedData;
    }

});
