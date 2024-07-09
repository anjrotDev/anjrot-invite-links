<?php
// Evitar acceso directo al archivo
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;
$table_name_links = $wpdb->prefix . 'invite_links';
$table_name_pages = $wpdb->prefix . 'protected_pages';

// Guardar la página protegida al generar un enlace de invitación
if ( isset( $_POST['generate_link'] ) ) {
    $page_id = $_POST['protected_page'];
    $uses_remaining = isset($_POST['uses_remaining']) ? intval($_POST['uses_remaining']) : -1;
    $wpdb->replace( $table_name_pages, array( 'page_id' => $page_id, 'protected' => 1 ) );

    error_log('$_POST =>');
    error_log(print_r($_POST, true));

    $uuid = wp_generate_uuid4();
    $wpdb->insert( $table_name_links, array( 'uuid' => $uuid, 'uses_remaining' => $uses_remaining, 'page_id' => $page_id, 'uses_quantity' =>  $uses_remaining ) );
}

$pages = get_pages();
$protected_page = get_option( 'anjrot_protected_page' );
$links = $wpdb->get_results( "SELECT * FROM $table_name_links" );

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
        <label for="uses_remaining">Number of Uses (0 for unlimited):</label>
        <input type="number" name="uses_remaining" id="uses_remaining" value="0" min="0">
        <input type="submit" name="generate_link" class="button button-primary" value="Generate Invite Link">
    </form>

    <h2>Generated Links</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Link</th>
                <th>Uses Configured</th>
                <th>Uses Remaining</th>
                <th>Used</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $links as $link ):
                error_log('link =>');
                error_log(print_r($link, true));
                ?>
                <tr>
                    <td><?php echo $link->id; ?></td>
                    <td><?php echo home_url( '/'. get_page_uri( $link->page_id ).'?invite=' . $link->uuid); ?></td>
                    <td><?php echo $link->uses_quantity == -1 ? 'Unlimited' : $link->uses_quantity; ?></td>
                    <td><?php echo $link->uses_remaining == -1 ? 'Unlimited' : max(0, $link->uses_remaining); ?></td>
                    <td><?php echo $link->used ? 'Yes' : 'No'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
