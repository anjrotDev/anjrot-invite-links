<?php
// Evitar acceso directo al archivo
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;
$table_name_pages = $wpdb->prefix . 'protected_pages';

// Guardar la página protegida
if ( isset( $_POST['protect_page'] ) ) {
    $page_id = $_POST['page_to_protect'];
    $wpdb->replace( $table_name_pages, array( 'page_id' => $page_id, 'protected' => 1 ) );
}

$pages = get_pages();
$protected_pages = $wpdb->get_results( "SELECT * FROM $table_name_pages" );

?>

<div class="wrap">
    <h1>Protected Pages</h1>
    <form method="post">
        <h2>Protect a Page</h2>
        <label for="page_to_protect">Select Page:</label>
        <select name="page_to_protect" id="page_to_protect">
            <?php foreach ( $pages as $page ): ?>
                <option value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" name="protect_page" class="button button-primary" value="Protect Page">
    </form>
    <h2>Protected Pages List</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Page</th>
                <th>Protected</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $protected_pages as $protected_page ): ?>
                <tr>
                    <td><?php echo $protected_page->id; ?></td>
                    <td><?php echo get_the_title( $protected_page->page_id ); ?></td>
                    <td><?php echo $protected_page->protected ? 'Yes' : 'No'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
