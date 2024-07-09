<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;
$table_name = $wpdb->prefix . 'invite_links';
$uuid = sanitize_text_field( $_GET['invite'] );

// Verificar si el UUID es válido y marcarlo como usado
if ( $wpdb->update( $table_name, array( 'used' => 1 ), array( 'uuid' => $uuid ) ) !== false ) {
    // Incluir el header del tema activo
    get_header();

    // Comenzar el loop de WordPress para obtener el contenido de la página
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;

    // Incluir el footer del tema activo
    get_footer();
} else {
    wp_redirect( home_url() );
    exit;
}
