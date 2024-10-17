<?php
 /**
 * Plugin Name:       Anjrot Invite Links
 * Description:       Plugin to create invitation links and protect a form.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Anjrot Dev
 * Author URI:        https://anjrot.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       anjrot-invite-links
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

include_once plugin_dir_path( __FILE__ ) . 'includes/class-invite-link.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/database-functions.php';

register_activation_hook( __FILE__, 'anjrot_activate_plugin' );

function anjrot_activate_plugin() {
    create_tables();
    update_option('anjrot_invite_links_version', '0.3.0');
}

function anjrot_invite_links_init() {
	$current_version = get_option('anjrot_invite_links_version', '0.1.0');
	if (version_compare($current_version, '0.3.0', '<')) {
		create_tables();
		update_option('anjrot_invite_links_version', '0.3.0');
	}
	$plugin = new WP_Invite_Link();
}
add_action( 'plugins_loaded', 'anjrot_invite_links_init' );

function anjrot_invite_links_block_init() {
	register_block_type( __DIR__ . '/build/custom-form' );
	register_block_type( __DIR__ . '/build/text-input' );
	register_block_type( __DIR__ . '/build/email-input' );
}
add_action( 'init', 'anjrot_invite_links_block_init' );

// Función para escribir en el log
function anjrot_log($message) {
    $log_file = plugin_dir_path(__FILE__) . 'anjrot_invite_links.log';
    $timestamp = date('[Y-m-d H:i:s]');
    $log_message = $timestamp . ' ' . $message . "\n";
    error_log($log_message, 3, $log_file);
}

// Modificar la función de manejo del formulario
function anjrot_handle_form_submission() {
    error_log('Form submission started');
    error_log('POST data: ' . print_r($_POST, true));

    // Verifica el nonce para seguridad
    if (!wp_verify_nonce($_POST['_wpnonce'], 'anjrot_form_nonce')) {
        error_log('Invalid nonce');
        wp_send_json_error('Invalid nonce');
        return;
    }

    $submit_action = isset($_POST['submitAction']) ? sanitize_text_field($_POST['submitAction']) : '';
    error_log('Submit action: ' . $submit_action);

    if ($submit_action === 'sendToAPI') {
        $api_endpoint = get_option('anjrot_invite_links_api_endpoint', '');
        $api_key = get_option('anjrot_invite_links_api_key', '');

        error_log('API Endpoint: ' . $api_endpoint);

        if (empty($api_endpoint)) {
            error_log('API endpoint not configured');
            wp_send_json_error('API endpoint not configured');
            return;
        }

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key
        );

        $body = wp_json_encode($_POST);

        error_log('Sending data to API: ' . $body);

        $response = wp_remote_post($api_endpoint, array(
            'headers' => $headers,
            'body' => $body,
            'timeout' => 60
        ));

        if (is_wp_error($response)) {
            error_log('Failed to send data to API: ' . $response->get_error_message());
            wp_send_json_error('Failed to send data to API: ' . $response->get_error_message());
        } else {
            $response_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);
            error_log('API response code: ' . $response_code);
            error_log('API response body: ' . $response_body);
            if ($response_code === 200) {
                wp_send_json_success('Data sent to API successfully');
            } else {
                wp_send_json_error('API returned error: ' . $response_code);
            }
        }
    } else {
        // Manejar el envío de correo electrónico (código existente)
        $to = isset($_POST['emailTo']) ? sanitize_email($_POST['emailTo']) : '';
        $cc = isset($_POST['emailCc']) ? sanitize_email($_POST['emailCc']) : '';
        $subject = isset($_POST['emailSubject']) ? sanitize_text_field($_POST['emailSubject']) : 'New form submission';

        error_log("Sending email to: $to, CC: $cc, Subject: $subject");

        // Construye el cuerpo del correo electrónico
        $body = "New form submission:\n\n";
        foreach ($_POST as $key => $value) {
            if (!in_array($key, ['emailTo', 'emailCc', 'emailSubject', '_wpnonce', 'action'])) {
                $body .= ucfirst($key) . ": " . sanitize_text_field($value) . "\n";
            }
        }

        $headers = array('Content-Type: text/html; charset=UTF-8');
        if (!empty($cc)) {
            $headers[] = 'Cc: ' . $cc;
        }

        error_log("Email body: $body");
        error_log("Email headers: " . print_r($headers, true));

        // Envía el correo electrónico
        if (!empty($to)) {
            $sent = wp_mail($to, $subject, $body, $headers);

            if ($sent) {
                error_log('Email sent successfully');
                wp_send_json_success('Email sent successfully');
            } else {
                error_log('Failed to send email');
                $last_error = error_get_last();
                if ($last_error) {
                    error_log('Last error: ' . print_r($last_error, true));
                }
                wp_send_json_error('Failed to send email');
            }
        } else {
            error_log('No recipient email provided. emailTo: ' . $to);
            wp_send_json_error('No recipient email provided');
        }
    }
}
add_action('wp_ajax_anjrot_submit_form', 'anjrot_handle_form_submission');
add_action('wp_ajax_nopriv_anjrot_submit_form', 'anjrot_handle_form_submission');

// Añade esta función para incluir la URL de AJAX en el script
function anjrot_enqueue_custom_scripts() {
    wp_enqueue_script('anjrot-invite-links-view-script', plugins_url('build/custom-form/view.js', __FILE__), array('wp-element'), '1.0.0', true);
    wp_localize_script('anjrot-invite-links-view-script', 'anjrotInviteLinks', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('anjrot_form_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'anjrot_enqueue_custom_scripts');
