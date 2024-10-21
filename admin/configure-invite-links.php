<?php
// Evitar acceso directo al archivo
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;
$table_name_links = $wpdb->prefix . 'invite_links';
$table_name_pages = $wpdb->prefix . 'protected_pages';

// Guardar la página protegida al generar un enlace de invitación
if ( isset( $_POST['generate_link'] ) ) {
    $page_id = $_POST['protected_page'];
    $uses_remaining = isset($_POST['uses_remaining']) ? intval($_POST['uses_remaining']) : -1;
    $redirect_url = isset($_POST['redirect_url']) ? esc_url_raw($_POST['redirect_url']) : '';
    $internal_redirect_page = isset($_POST['internal_redirect_page']) ? intval($_POST['internal_redirect_page']) : 0; // Nueva variable

    // Guardar la página protegida
    $wpdb->replace( 
        $table_name_pages, 
        array( 
            'page_id' => $page_id, 
            'protected' => 1
        ) 
    );

    $uuid = wp_generate_uuid4();
    $wpdb->insert( $table_name_links, array( 
        'uuid' => $uuid, 
        'uses_remaining' => $uses_remaining, 
        'page_id' => $page_id,  // Página protegida
        'internal_redirect_page' => $internal_redirect_page, // Nueva columna para redirección interna
        'uses_quantity' => $uses_remaining,
        'redirect_url' => $redirect_url
    ) );
}

// Actualizar la URL de redirección de un enlace específico
if ( isset( $_POST['update_redirect'] ) ) {
    $link_id = intval($_POST['link_id']);
    $new_redirect_url = esc_url_raw($_POST['new_redirect_url']);
    
    $wpdb->update(
        $table_name_links,
        array('redirect_url' => $new_redirect_url),
        array('id' => $link_id)
    );
}

$pages = get_pages();
$protected_pages = $wpdb->get_results("SELECT * FROM $table_name_pages WHERE protected = 1");
$links = $wpdb->get_results("SELECT * FROM $table_name_links");

?>

<div class="wrap">
    <h1>Configure Invite Links</h1>
    
    <form method="post" class="invite-link-form">
        <h2>Generate New Invite Link</h2>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="protected_page">Page to Protect:</label></th>
                <td>
                    <select name="protected_page" id="protected_page">
                        <?php foreach ( $pages as $page ): ?>
                            <option value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="uses_remaining">Number of Uses (0 for unlimited):</label></th>
                <td>
                    <input type="number" name="uses_remaining" id="uses_remaining" value="0" min="0">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="redirect_url">Custom Redirect URL (leave empty to use home page):</label></th>
                <td>
                    <input type="url" name="redirect_url" id="redirect_url" style="width: 100%;">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="internal_redirect_page">Internal Redirect Page:</label></th>
                <td>
                    <select name="internal_redirect_page" id="internal_redirect_page">
                        <option value="0"><?php _e('None (leave empty for no internal redirect)', 'anjrot-invite-links'); ?></option>
                        <?php foreach ( $pages as $page ): ?>
                            <option value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php _e('Select an internal page to redirect to.', 'anjrot-invite-links'); ?></p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="generate_link" class="button button-primary" value="Generate Invite Link">
        </p>
    </form>

    <h2>Generated Links</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Link</th>
                <th>Protected Page</th>
                <th>Uses Configured</th>
                <th>Uses Remaining</th>
                <th>Used</th>
                <th>Redirect URL</th>
                <th>Internal Redirect Page</th> <!-- Nueva columna -->
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $links as $link ): 
                $page_title = get_the_title($link->page_id);
                $internal_redirect_page_title = $link->internal_redirect_page ? get_the_title($link->internal_redirect_page) : 'None';
                ?>
                <tr>
                    <td><?php echo $link->id; ?></td>
                    <td><?php echo home_url( '/'. get_page_uri( $link->page_id ).'?invite=' . $link->uuid); ?></td>
                    <td><?php echo $page_title; ?></td>
                    <td><?php echo $link->uses_quantity == -1 ? 'Unlimited' : $link->uses_quantity; ?></td>
                    <td><?php echo $link->uses_remaining == -1 ? 'Unlimited' : max(0, $link->uses_remaining); ?></td>
                    <td><?php echo $link->used ? 'Yes' : 'No'; ?></td>
                    <td><?php echo $link->redirect_url ? $link->redirect_url : 'Home Page'; ?></td>
                    <td><?php echo $internal_redirect_page_title; ?></td> <!-- Mostrar la página de redirección interna -->
                    <td>
                        <form method="post">
                            <input type="hidden" name="link_id" value="<?php echo $link->id; ?>">
                            <input type="url" name="new_redirect_url" value="<?php echo $link->redirect_url; ?>" style="width: 70%;">
                            <input type="submit" name="update_redirect" class="button button-secondary" value="Update">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
    .invite-link-form {
        background: #fff;
        border: 1px solid #ccd0d4;
        padding: 20px;
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .invite-link-form h2 {
        margin-top: 0;
    }
    .form-table th {
        width: 200px;
    }
</style>
