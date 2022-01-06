<?php
/**
 * Functions
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Render an ajax list table
 *
 * @since 1.0.0
 *
 * @param TDB_Table|string   $table      TDB_Table object or TDB_Table name
 * @param array             $query_args Query parameters
 * @param array             $view_args  View parameters
 */
function tdb_render_ajax_list_table( $table, $query_args = array(), $view_args = array() ) {

    global $tdb_table, $tdb_query, $tdb_list_table, $tdb_ajax_list_items_per_page;

    $tdb_table = tdb_setup_table( $table );

    if ( !is_object( $tdb_table ) ) return;

    // Enqueue assets
    tdb_ajax_list_table_enqueue_scripts();

    // Setup query args
    $query_args = wp_parse_args( $query_args, array(
        'paged' => 1,
        'items_per_page' => 20,
    ) );

    // setup view args
    $view_args = wp_parse_args( $view_args, array(
        'views' => true,
        'search_box' => true,
    ) );

    // Add a filter to override the items per page user setting
    $tdb_ajax_list_items_per_page = $query_args['items_per_page'];
    add_filter( 'edit_' . $tdb_table->name . '_per_page', 'tdb_ajax_list_override_items_per_page' );

    // Set up vars
    $tdb_query = new TDB_Query( $query_args );
    $tdb_list_table = new TDB_List_Table();

    $tdb_list_table->prepare_items();

    ?>

    <div class="wrap tdb-ajax-list-table" data-object="<?php echo $tdb_table->name; ?>" data-query-args="<?php echo str_replace( '"', "'", json_encode( $query_args ) ); ?>">

        <?php
        if( $view_args['views'] ) {
            $tdb_list_table->views();
        }
        ?>

        <?php // <form id="tdb-list-filter" method="get"> ?>

            <?php
            if( $view_args['search_box'] ) {
                $tdb_list_table->search_box( $tdb_table->labels->search_items, $tdb_table->name );
            }
            ?>

            <?php tdb_render_ajax_list_tablenav( $tdb_list_table, 'top' ); ?>

            <table class="wp-list-table <?php echo implode( ' ', $tdb_list_table->get_table_classes() ); ?>">
                <thead>
                <tr>
                    <?php $tdb_list_table->print_column_headers(); ?>
                </tr>
                </thead>

                <?php $singular = $tdb_list_table->_args['singular']; ?>
                <tbody id="the-list"<?php
                if ( $singular ) {
                    echo " data-wp-lists='list:$singular'";
                } ?>>
                <?php $tdb_list_table->display_rows_or_placeholder(); ?>
                </tbody>

                <tfoot>
                <tr>
                    <?php $tdb_list_table->print_column_headers( false ); ?>
                </tr>
                </tfoot>

            </table>

            <?php tdb_render_ajax_list_tablenav( $tdb_list_table, 'bottom' ); ?>

        <?php // </form> ?>

        <div id="ajax-response"></div>
        <br class="clear" />

    </div>

    <?php
}

/**
 * Overrides the items per page user setting
 *
 * @since 1.0.0
 *
 * @param int $items_per_page
 *
 * @return int
 */
function tdb_ajax_list_override_items_per_page( $items_per_page ) {

    global $tdb_ajax_list_items_per_page;

    if( absint( $tdb_ajax_list_items_per_page ) !== 0 ) {
        return absint( $tdb_ajax_list_items_per_page );
    }

    return $items_per_page;

}

/**
 * Custom table nav function
 *
 * @param TDB_List_Table $tdb_list_table
 * @param string        $which
 */
function tdb_render_ajax_list_tablenav( $tdb_list_table, $which ) {
    ?>
    <div class="tablenav <?php echo esc_attr( $which ); ?>">

        <?php if ( $tdb_list_table->has_items() ): ?>
            <div class="alignleft actions bulkactions">
                <?php $tdb_list_table->bulk_actions( $which ); ?>
            </div>
        <?php endif;
        $tdb_list_table->extra_tablenav( $which );
        $tdb_list_table->pagination( $which );
        ?>

        <br class="clear" />
    </div>
    <?php
}