<?php
/**
 * Scripts
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

function tdb_ajax_list_table_enqueue_scripts() {

  wp_enqueue_script( 'tdb-ajax-list-table-js', TDB_AJAX_LIST_TABLE_URL . 'assets/js/ajax-list-table.js', array( 'jquery' ), TDB_AJAX_LIST_TABLE_VER, true );

  wp_enqueue_style( 'tdb-ajax-list-table-css', TDB_AJAX_LIST_TABLE_URL . 'assets/css/ajax-list-table.css', array(), TDB_AJAX_LIST_TABLE_VER );
}