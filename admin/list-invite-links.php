<?php
// Evitar acceso directo al archivo
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;
$table_name = $wpdb->prefix . 'invite_links';
$links = $wpdb->get_results( "SELECT * FROM $table_name" );
$protected_page = get_option( 'anjrot_protected_page' );
?>

<div class="wrap">
    <h1>Generated Invite Links</h1>
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
