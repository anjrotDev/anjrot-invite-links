<?php
class WP_Invite_Link {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
        add_action( 'template_redirect', array( $this, 'handle_protection' ) );
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
            array( $this, 'list_invite_links_page' ),
            'dashicons-admin-links'
        );

        add_submenu_page(
            'invite-links',
            'Configure Invite Links',
            'Configure',
            'manage_options',
            'configure-invite-links',
            array( $this, 'configure_invite_links_page' )
        );
    }

    public function list_invite_links_page() {
        include plugin_dir_path( __FILE__ ) . '../admin/list-invite-links.php';
    }

    public function configure_invite_links_page() {
        include plugin_dir_path( __FILE__ ) . '../admin/configure-invite-links.php';
    }

    public function handle_protection() {
        if ( isset( $_GET['invite'] ) ) {
            $this->handle_invite_link();
        } else {
            $this->protect_page();
        }
    }

    private function handle_invite_link() {
        $uuid = sanitize_text_field( $_GET['invite'] );
        global $wpdb;
        $table_name = $wpdb->prefix . 'invite_links';

        $link = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name WHERE uuid = %s AND used = 0",
            $uuid
        ));

        if ( $link ) {
            add_action( 'wp_footer', function() use ( $wpdb, $table_name, $uuid ) {
                $wpdb->update( $table_name, array( 'used' => 1 ), array( 'uuid' => $uuid ) );
            } );
            return; // Dejar que la página se renderice normalmente
        } else {
            wp_redirect( home_url() );
            exit;
        }
    }

    private function protect_page() {
        $protected_page = get_option( 'anjrot_protected_page' );
        if ( is_page( $protected_page ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }
    
    private function is_valid_invite( $uuid ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'invite_links';
        $link = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name WHERE uuid = %s AND used = 0",
            $uuid
        ));
        return $link ? true : false;
    }
}
