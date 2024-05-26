<?php
// Evitar acceso directo al archivo
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;
$table_name = $wpdb->prefix . 'invite_links';

if ( isset( $_POST['generate_link'] ) ) {
    $uuid = wp_generate_uuid4();
    $wpdb->insert( $table_name, array( 'uuid' => $uuid ) );
}

$links = $wpdb->get_results( "SELECT * FROM $table_name" );

?>

<div class="wrap">
    <h1>Invite Links</h1>
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
                    <td><?php echo home_url( '/formulario?invite=' . $link->uuid ); ?></td>
                    <td><?php echo $link->used ? 'Yes' : 'No'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
