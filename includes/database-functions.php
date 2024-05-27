<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function create_tables() {
    global $wpdb;
    $table_name_links = $wpdb->prefix . 'invite_links';
    $table_name_pages = $wpdb->prefix . 'protected_pages';
    $charset_collate = $wpdb->get_charset_collate();

    $sql1 = "CREATE TABLE $table_name_links (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        uuid varchar(255) NOT NULL,
        used tinyint(1) DEFAULT 0 NOT NULL,
        uses_remaining int DEFAULT 0 NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    $sql2 = "CREATE TABLE $table_name_pages (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        page_id bigint(20) NOT NULL,
        protected tinyint(1) DEFAULT 1 NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY page_id (page_id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql1 );
    dbDelta( $sql2 );
}
