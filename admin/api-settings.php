<?php
// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Guardar configuraciones de la API
if (isset($_POST['save_api_settings'])) {
    $api_endpoint = sanitize_url($_POST['api_endpoint']);
    $api_key = sanitize_text_field($_POST['api_key']);
    
    update_option('anjrot_invite_links_api_endpoint', $api_endpoint);
    update_option('anjrot_invite_links_api_key', $api_key);
    
    echo '<div class="updated"><p>API settings saved.</p></div>';
}

$api_endpoint = get_option('anjrot_invite_links_api_endpoint', '');
$api_key = get_option('anjrot_invite_links_api_key', '');
?>

<div class="wrap">
    <h1>API Settings</h1>
    <form method="post" action="">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="api_endpoint">API Endpoint</label></th>
                <td><input name="api_endpoint" type="url" id="api_endpoint" value="<?php echo esc_attr($api_endpoint); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="api_key">API Key</label></th>
                <td><input name="api_key" type="text" id="api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text"></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="save_api_settings" class="button button-primary" value="Save API Settings">
        </p>
    </form>
</div>
