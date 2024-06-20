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
        error_log("handle_invite_link", 0);
        $uuid = sanitize_text_field( $_GET['invite'] );
        global $wpdb;
        $table_name_links = $wpdb->prefix . 'invite_links';

        $link = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name_links WHERE uuid = %s AND used = 0 AND (uses_remaining > 0 OR uses_remaining = -1)",
            $uuid
        ));

        if ( $link ) {
            add_action( 'wp_footer', function() use ( $wpdb, $table_name_links, $uuid, $link ) {
                // Reducir el número de usos restantes si es mayor que 0
                if ($link->uses_remaining > 0) {
                    $wpdb->update( $table_name_links, array( 'uses_remaining' => $link->uses_remaining - 1 ), array( 'uuid' => $uuid ) );
                }

                // Marcar como usado si los usos restantes son 0
                if ($link->uses_remaining == 1) {
                    $wpdb->update( $table_name_links, array( 'used' => 1 ), array( 'uuid' => $uuid ) );
                }
            } );
            return; // Dejar que la página se renderice normalmente
        } else {
            error_log("estoy en este else de handle_invite_link");
            wp_redirect( home_url() );
            exit;
        }
    }

    private function protect_page() {
        error_log("is Page protected");
        global $wpdb;
        $table_name_pages = $wpdb->prefix . 'protected_pages';
        $page_id = get_the_ID();
        
        
        $protected_page_query = $wpdb->get_results( "SELECT * FROM $table_name_pages WHERE page_id = $page_id" );

        if (count($protected_page_query) == 0) return;
        
        $protected_page_id = $protected_page_query->page_id;
        error_log(count($protected_page_query));
        error_log('protected_page_id =>');
        error_log(print_r($protected_page_query, true));

        error_log("none!!!");
        // error_log($post);
        error_log("end none none!!!");
        error_log(is_page($protected_page_id));
        if ( is_page( $protected_page_id ) ) {
            error_log("en el if!!!");
            error_log(isset($_GET['invite']));
            if ( !isset($_GET['invite']) || !$this->is_valid_invite(sanitize_text_field($_GET['invite'])) ) {
                wp_redirect( home_url() );
                exit;
            }
        }else{
            error_log("no consiguio la pagina", 0);
            return;
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
