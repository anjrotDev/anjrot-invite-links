<?php
// Evitar acceso directo al archivo
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;
$table_name = $wpdb->prefix . 'invite_links';

// Guardar la página protegida al generar un enlace de invitación
if ( isset( $_POST['generate_link'] ) ) {
    update_option( 'anjrot_protected_page', $_POST['protected_page'] );
    $uuid = wp_generate_uuid4();
    $wpdb->insert( $table_name, array( 'uuid' => $uuid ) );
}

$pages = get_pages();
$protected_page = get_option( 'anjrot_protected_page' );

?>

<div class="wrap">
    <h1>Configure Invite Links</h1>
    <form method="post">
        <h2>Settings</h2>
        <label for="protected_page">Protected Page:</label>
        <select name="protected_page" id="protected_page">
            <?php foreach ( $pages as $page ): ?>
                <option value="<?php echo $page->ID; ?>" <?php selected( $protected_page, $page->ID ); ?>><?php echo $page->post_title; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" name="generate_link" class="button button-primary" value="Generate Invite Link">
    </form>
</div>
