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

        add_submenu_page(
            'invite-links',
            'API Settings',
            'API Settings',
            'manage_options',
            'api-settings',
            array( $this, 'api_settings_page' )
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

    public function api_settings_page() {
        include plugin_dir_path( __FILE__ ) . '../admin/api-settings.php';
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
        $current_page_id = get_the_ID();

        if ( self::is_valid_invite($uuid, $current_page_id) ) {
            add_action( 'wp_footer', function() use ($uuid) {
                self::update_invite_usage($uuid);
            });
            return; // Dejar que la página se renderice normalmente
        } else {
            $this->redirect_to_custom_or_home($uuid);
        }
    }

    private function protect_page() {
        $current_page_id = get_the_ID();
        
        if ( self::is_page_protected($current_page_id) ) {
            if ( !isset($_GET['invite']) || !self::is_valid_invite(sanitize_text_field($_GET['invite']), $current_page_id) ) {
                $this->redirect_to_custom_or_home(sanitize_text_field($_GET['invite'] ?? ''));
            }
        }
    }

    private function redirect_to_custom_or_home($uuid) {
        global $wpdb;
        $table_name_links = $wpdb->prefix . 'invite_links';
        
        $link = $wpdb->get_row( $wpdb->prepare(
            "SELECT redirect_url FROM $table_name_links WHERE uuid = %s",
            $uuid
        ));

        if ($link && !empty($link->redirect_url)) {
            wp_redirect($link->redirect_url);
        } else {
            wp_redirect(home_url());
        }
        exit;
    }

    public static function is_page_protected($page_id) {
        global $wpdb;
        $table_name_pages = $wpdb->prefix . 'protected_pages';
        
        $protected = $wpdb->get_var( $wpdb->prepare(
            "SELECT protected FROM $table_name_pages WHERE page_id = %d",
            $page_id
        ));

        return $protected == 1;
    }

    public static function is_valid_invite($uuid, $page_id) {
        global $wpdb;
        $table_name_links = $wpdb->prefix . 'invite_links';
        
        $link = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name_links WHERE uuid = %s AND page_id = %d AND (uses_remaining > 0 OR uses_remaining = -1)",
            $uuid,
            $page_id
        ));

        return $link ? true : false;
    }

    private static function update_invite_usage($uuid) {
        global $wpdb;
        $table_name_links = $wpdb->prefix . 'invite_links';

        $wpdb->query( $wpdb->prepare(
            "UPDATE $table_name_links 
            SET uses_remaining = CASE 
                WHEN uses_remaining > 0 THEN uses_remaining - 1 
                ELSE uses_remaining 
            END,
            used = CASE 
                WHEN uses_remaining = 1 THEN 1 
                ELSE used 
            END
            WHERE uuid = %s",
            $uuid
        ));
    }
}
