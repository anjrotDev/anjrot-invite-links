<?php
// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Guardar configuraciones de la API
if (isset($_POST['save_api_settings'])) {
    $api_endpoints = array_map('sanitize_text_field', explode("\n", $_POST['api_endpoints']));
    update_option('anjrot_invite_links_api_endpoints', $api_endpoints);
    
    echo '<div class="updated"><p>API settings saved.</p></div>';
}

$api_endpoints = get_option('anjrot_invite_links_api_endpoints', []);
?>

<div class="wrap">
    <h1>API Settings</h1>
    <form method="post" action="">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="api_endpoints">API Endpoints</label></th>
                <td>
                    <textarea name="api_endpoints" id="api_endpoints" rows="10" cols="50" class="large-text"><?php echo esc_textarea(implode("\n", $api_endpoints)); ?></textarea>
                    <p class="description">Enter one API endpoint per line.</p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="save_api_settings" class="button button-primary" value="Save API Settings">
        </p>
    </form>
</div>
