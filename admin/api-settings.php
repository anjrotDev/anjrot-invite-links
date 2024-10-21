<?php
// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Guardar configuraciones de la API
if (isset($_POST['save_api_settings'])) {
    $api_endpoints = array_map('sanitize_text_field', explode("\n", $_POST['api_endpoints']));
    $api_key = sanitize_text_field($_POST['api_key']);
    
    update_option('anjrot_invite_links_api_endpoints', $api_endpoints);
    update_option('anjrot_invite_links_api_key', $api_key);
    
    echo '<div class="updated"><p>API settings saved.</p></div>';
}

$api_endpoints = get_option('anjrot_invite_links_api_endpoints', []);
$api_key = get_option('anjrot_invite_links_api_key', '');
?>

<div class="wrap">
    <h1><?php esc_html_e('API Settings', 'anjrot-invite-links'); ?></h1>
    <form method="post" action="">
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php esc_html_e('API Endpoints', 'anjrot-invite-links'); ?></th>
                <td>
                    <textarea name="api_endpoints" rows="5" cols="50"><?php echo esc_textarea(implode("\n", $api_endpoints)); ?></textarea>
                    <p class="description"><?php esc_html_e('Enter multiple API endpoints, one per line.', 'anjrot-invite-links'); ?></p>
                </td>
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
