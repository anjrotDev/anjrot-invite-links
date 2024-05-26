<?php
/* 
Template Name: Invite Form
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;
$table_name = $wpdb->prefix . 'invite_links';
$uuid = sanitize_text_field( $_GET['invite'] );

if ( $wpdb->update( $table_name, array( 'used' => 1 ), array( 'uuid' => $uuid ) ) !== false ) {
    echo '<h1>Formulario de invitación</h1>';
    // Aquí va tu formulario
} else {
    wp_redirect( home_url() );
    exit;
}
