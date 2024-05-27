<?php
include_once plugin_dir_path( __FILE__ ) . 'database-functions.php';

class WP_Invite_Link {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
        add_action( 'template_redirect', array( $this, 'handle_protection' ) );
    }

    public static function activate() {
        create_tables();
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

        add_submenu_page(
            'invite-links',
            'Protected Pages',
            'Protected Pages',
            'manage_options',
            'protected-pages',
            array( $this, 'protected_pages_page' )
        );
    }

    public function list_invite_links_page() {
        include plugin_dir_path( __FILE__ ) . '../admin/list-invite-links.php';
    }

    public function configure_invite_links_page() {
        include plugin_dir_path( __FILE__ ) . '../admin/configure-invite-links.php';
    }

    public function protected_pages_page() {
        include plugin_dir_path( __FILE__ ) . '../admin/protected-pages.php';
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
        $table_name_links = $wpdb->prefix . 'invite_links';

        $link = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name_links WHERE uuid = %s AND used = 0 AND (uses_remaining > 0 OR uses_remaining = -1)",
            $uuid
        ));

        if ( $link ) {
            add_action( 'wp_footer', function() use ( $wpdb, $table_name_links, $uuid, $link ) {
                if ($link->uses_remaining > 0) {
                    $wpdb->update( $table_name_links, array( 'uses_remaining' => $link->uses_remaining - 1 ), array( 'uuid' => $uuid ) );
                }
                if ($link->uses_remaining == 1) {
                    $wpdb->update( $table_name_links, array( 'used' => 1 ), array( 'uuid' => $uuid ) );
                }
            } );
            return; // Dejar que la página se renderice normalmente
        } else {
            wp_redirect( home_url() );
            exit;
        }
    }

    private function protect_page() {
        global $wpdb;
        $table_name_pages = $wpdb->prefix . 'protected_pages';
        $protected_page = get_option( 'anjrot_protected_page' );

        if ( is_page( $protected_page ) ) {
            $page_id = get_queried_object_id();
            $protected = $wpdb->get_var( $wpdb->prepare(
                "SELECT protected FROM $table_name_pages WHERE page_id = %d",
                $page_id
            ));

            if ( $protected ) {
                wp_redirect( home_url() );
                exit;
            }
        }
    }
    
    private function is_valid_invite( $uuid ) {
        global $wpdb;
        $table_name_links = $wpdb->prefix . 'invite_links';
        $link = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name_links WHERE uuid = %s AND used = 0 AND (uses_remaining > 0 OR uses_remaining = -1)",
            $uuid
        ));
        return $link ? true : false;
    }
}
