<?php
/**
 * Ajax Functions
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

add_action( 'wp_ajax_tdb_ajax_list_table_request', function() {

    global $tdb_table, $tdb_query, $tdb_list_table;

    if( ! isset( $_GET['object'] ) ) {
        wp_send_json_error();
    }

    $tdb_table = tdb_setup_table( $_GET['object'] );

    if( ! is_object( $tdb_table ) ) {
        wp_send_json_error();
    }

    if( is_array( $_GET['query_args'] ) ) {
        $query_args = $_GET['query_args'];
    } else {
        $query_args = json_decode( str_replace( "\\'", "\"", $_GET['query_args'] ), true );
    }

    $query_args = wp_parse_args( $query_args, array(
        'paged' => 1,
        'items_per_page' => 20,
    ) );

    if( isset( $_GET['paged'] ) ) {
        $query_args['paged'] = $_GET['paged'];
    }

    // Set up vars
    $tdb_query = new TDB_Query( $query_args );
    $tdb_list_table = new TDB_List_Table();

    $tdb_list_table->prepare_items();

    ob_start();
    $tdb_list_table->display();
    $output = ob_get_clean();

    wp_send_json_success( $output );

});
