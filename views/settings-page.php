<?php
if (!current_user_can('manage_options')) {
    return;
}
?>

<div class="wrap">
    <h1>REST API Daten Import</h1>
    <form id="api-data-request-form" method="post">
        <input type="text" name="api_route" id="api_route" placeholder="https://example.com/wp-json/wp/v2/places/123" style="width: 100%;">
        <input type="button" id="request_data_button" value="Daten anfordern" class="button button-primary">
    </form>
    <div id="data-display" style="margin-top: 20px;"></div>
</div>

