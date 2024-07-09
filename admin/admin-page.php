<?php
// Evitar acceso directo al archivo
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;
$table_name = $wpdb->prefix . 'invite_links';

// Guardar la página protegida
if ( isset( $_POST['protected_page'] ) ) {
    update_option( 'anjrot_protected_page', $_POST['protected_page'] );
}

// Generar enlace de invitación
if ( isset( $_POST['generate_link'] ) ) {
    $uuid = wp_generate_uuid4();
    $wpdb->insert( $table_name, array( 'uuid' => $uuid ) );
}

$links = $wpdb->get_results( "SELECT * FROM $table_name" );
$pages = get_pages();
$protected_page = get_option( 'anjrot_protected_page' );

?>

<div class="wrap">
    <h1>Invite Links</h1>
    <form method="post">
        <h2>Settings</h2>
        <label for="protected_page">Protected Page:</label>
        <select name="protected_page" id="protected_page">
            <?php foreach ( $pages as $page ): ?>
                <option value="<?php echo $page->ID; ?>" <?php selected( $protected_page, $page->ID ); ?>><?php echo $page->post_title; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Save Settings" class="button button-primary">
    </form>
    <form method="post">
        <input type="submit" name="generate_link" class="button button-primary" value="Generate Invite Link">
    </form>
    <h2>Generated Links</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Link</th>
                <th>Used</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $links as $link ): ?>
                <tr>
                    <td><?php echo $link->id; ?></td>
                    <td><?php echo get_permalink( $protected_page ) . '?invite=' . $link->uuid; ?></td>
                    <td><?php echo $link->used ? 'Yes' : 'No'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
