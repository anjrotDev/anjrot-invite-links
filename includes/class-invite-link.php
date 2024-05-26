<?php
class WP_Invite_Link {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
        add_action( 'template_redirect', array( $this, 'handle_invite_link' ) );
    }

    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'invite_links';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            uuid varchar(255) NOT NULL,
            used tinyint(1) DEFAULT 0 NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public function create_admin_menu() {
        add_menu_page(
            'Invite Links',
            'Invite Links',
            'manage_options',
            'invite-links',
            array( $this, 'admin_page_content' ),
            'dashicons-admin-links'
        );
    }

    public function admin_page_content() {
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'invite-links' ) {
            include plugin_dir_path( __FILE__ ) . '../admin/admin-page.php';
        }
    }

    public function handle_invite_link() {
        if ( isset( $_GET['invite'] ) ) {
            $uuid = sanitize_text_field( $_GET['invite'] );
            global $wpdb;
            $table_name = $wpdb->prefix . 'invite_links';

            $link = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM $table_name WHERE uuid = %s AND used = 0",
                $uuid
            ));

            if ( $link ) {
                include plugin_dir_path( __FILE__ ) . '../templates/form-template.php';
                exit;
            } else {
                wp_redirect( home_url() );
                exit;
            }
        }
    }
}
