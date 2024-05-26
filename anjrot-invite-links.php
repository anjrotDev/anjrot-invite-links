<?php
 /**
 * Plugin Name:       Anjrot Invite Links
 * Description:       Plugin to create invitation links and protect a form.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Anjrot Dev
 * Author URI:    		https://anjrot.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       anjrot-invite-links
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

include_once plugin_dir_path( __FILE__ ) . 'includes/class-invite-link.php';

register_activation_hook( __FILE__, array( 'WP_Invite_Link', 'activate' ) );

function anjrot_invite_links_init() {
    $plugin = new WP_Invite_Link();
}
add_action( 'plugins_loaded', 'anjrot_invite_links_init' );


function anjrot_invite_links_block_init() {
	register_block_type( __DIR__ . '/build/custom-form' );
	register_block_type( __DIR__ . '/build/text-input' );
	register_block_type( __DIR__ . '/build/email-input' );
}
add_action( 'init', 'anjrot_invite_links_block_init' );
